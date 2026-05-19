<?php

namespace App\Http\Controllers\v1;

use App\Models\Admin\Book;
use App\Models\Student\StudentDetail;
use App\Models\Teacher\TeacherDetail;
use App\Models\Teacher\TeacherSubject;
use Illuminate\Http\Request;

class BookController extends ApiController
{
    /**
     * GET /api/v1/books
     *
     * Returns books auto-scoped by user role:
     *   - Student (role=user) → books for their class + (section or null)
     *   - Teacher → books for their assigned (standard, subject) pairs
     *
     * Optional filters: standard_id, section_id, subject_id, search
     */
    public function index(Request $request)
    {
        [$user, $err] = $this->authUser();
        if ($err) return $err;

        $query = Book::with(['standard:id,name', 'section:id,name', 'subject:id,name,image'])
            ->where('organization_id', $user->organization_id)
            ->where('is_active', true);

        $this->scopeByRole($query, $user);

        if ($request->filled('standard_id')) {
            $query->where('standard_id', $request->standard_id);
        }

        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $books = $query->latest()->paginate((int) $request->get('per_page', 20));

        $items = $books->getCollection()->map(fn($b) => $this->formatBook($b));

        return $this->paginated($items, $this->paginationMeta($books), 'Books fetched successfully.');
    }

    /**
     * GET /api/v1/books/{id}
     *
     * Returns a single book with PDF URL (role-scoped).
     */
    public function show(int $id)
    {
        [$user, $err] = $this->authUser();
        if ($err) return $err;

        $query = Book::with(['standard:id,name', 'section:id,name', 'subject:id,name,image'])
            ->where('organization_id', $user->organization_id)
            ->where('is_active', true);

        $this->scopeByRole($query, $user);

        $book = $query->find($id);

        if (!$book) {
            return $this->error('Book not found.', 404);
        }

        return $this->success($this->formatBook($book, withPdf: true), 'Book fetched successfully.');
    }

    /**
     * Apply role-based access scope:
     *   - Student → standard + (section_id matches OR section_id is null/all)
     *   - Teacher → matches any of their (standard_id, subject_id) assignments
     */
    private function scopeByRole($query, $user): void
    {
        if ($user->role === 'user') {
            $student = StudentDetail::where('user_id', $user->id)->first(['standard_id', 'section_id']);
            if (!$student) {
                $query->whereRaw('1 = 0'); // no class → no books
                return;
            }
            $query->where('standard_id', $student->standard_id);
            if ($student->section_id) {
                $query->where(function ($q) use ($student) {
                    $q->where('section_id', $student->section_id)
                      ->orWhereNull('section_id');
                });
            }
        } elseif ($user->role === 'teacher') {
            $teacher = TeacherDetail::where('user_id', $user->id)->first(['id']);
            if (!$teacher) {
                $query->whereRaw('1 = 0');
                return;
            }
            $pairs = TeacherSubject::where('teacher_detail_id', $teacher->id)
                ->get(['standard_id', 'subject_id'])
                ->map(fn($r) => $r->standard_id . '-' . $r->subject_id)
                ->unique()
                ->values()
                ->toArray();

            if (empty($pairs)) {
                $query->whereRaw('1 = 0');
                return;
            }
            $query->whereIn(\DB::raw('CONCAT(standard_id, "-", subject_id)'), $pairs);
        }
    }

    // ── Private ───────────────────────────────────────────────────────────────

    private function formatBook(Book $book, bool $withPdf = false): array
    {
        $data = [
            'id'       => $book->id,
            'title'    => $book->title,
            'logo_url' => $book->book_logo,
            'standard' => $book->standard ? ['id' => $book->standard->id, 'name' => $book->standard->name] : null,
            'section'  => $book->section  ? ['id' => $book->section->id,  'name' => $book->section->name]  : null,
            'subject'  => $book->subject  ? ['id' => $book->subject->id,  'name' => $book->subject->name]  : null,
        ];

        if ($withPdf) {
            $data['pdf_url'] = $book->pdf_file;
        }

        return $data;
    }
}
