<?php

namespace App\Http\Controllers\v1;

use App\Models\Admin\Book;
use Illuminate\Http\Request;

class BookController extends ApiController
{
    /**
     * GET /api/v1/books
     *
     * Returns books for the authenticated user's organization.
     * Filters: standard_id, section_id, subject_id, search (optional)
     *
     * Both students (role=user) and teachers can access.
     */
    public function index(Request $request)
    {
        [$user, $err] = $this->authUser();
        if ($err) return $err;

        $query = Book::with(['standard:id,name', 'section:id,name', 'subject:id,name,image'])
            ->where('organization_id', $user->organization_id)
            ->where('is_active', true);

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
     * Returns a single book with PDF URL.
     */
    public function show(int $id)
    {
        [$user, $err] = $this->authUser();
        if ($err) return $err;

        $book = Book::with(['standard:id,name', 'section:id,name', 'subject:id,name,image'])
            ->where('organization_id', $user->organization_id)
            ->where('is_active', true)
            ->find($id);

        if (!$book) {
            return $this->error('Book not found.', 404);
        }

        return $this->success($this->formatBook($book, withPdf: true), 'Book fetched successfully.');
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
