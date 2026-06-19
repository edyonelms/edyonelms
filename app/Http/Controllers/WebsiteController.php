<?php

namespace App\Http\Controllers;

use App\Models\Admin\RateLms;
use App\Models\Admin\TermAndCondition;
use App\Models\Organization;
use App\Models\PrivacyPolicy;
use App\Models\Student\StudentDetail;
use App\Models\Teacher\TeacherDetail;
use App\Models\TermOfUse;
use App\Models\WebsiteContact;
use App\Models\WebsiteDemo;
use App\Models\WebsitePage;
use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    /** GET /api/website/stats */
    public function stats()
    {
        $schools  = Organization::count();
        $students = StudentDetail::count();
        $teachers = TeacherDetail::count();
        $rating   = RateLms::where('status', 1)->avg('rating');

        return response()->json([
            'success' => true,
            'data' => [
                'schools'  => $schools,
                'students' => $students,
                'teachers' => $teachers,
                'rating'   => $rating ? round($rating, 1) : 4.9,
            ],
        ]);
    }

    /** GET /api/website/schools */
    public function schools()
    {
        $schools = Organization::where('status', true)
            ->select('name', 'logo')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($school) => [
                'name'     => $school->name,
                'logo_url' => $this->resolveLogoUrl($school->logo),
            ]);

        return response()->json([
            'success' => true,
            'data'    => $schools,
        ]);
    }

    /**
     * Return an absolute URL for an Organization logo path, or null when missing.
     * Some records store an absolute URL (S3); others store a relative path
     * served via asset().
     */
    private function resolveLogoUrl(?string $logo): ?string
    {
        if (! $logo) {
            return null;
        }
        if (str_starts_with($logo, 'http://') || str_starts_with($logo, 'https://')) {
            return $logo;
        }
        return asset($logo);
    }

    /** GET /api/website/testimonials */
    public function testimonials()
    {
        $reviews = RateLms::with('organization:id,name,logo')
            ->where('status', 1)
            ->latest()
            ->get()
            ->map(function ($r) {
                $name = $r->organization->name ?? 'Anonymous';
                $logo     = $r->organization->logo ?? null;
                $words = preg_split('/\s+/', trim($name)) ?: [];
                // $initials = collect($words)
                //     ->filter()
                //     ->take(2)
                //     ->map(fn($word) => strtoupper(mb_substr($word, 0, 1)))
                //     ->implode('');
                $feedback = trim($r->feedback ?? '', '"\'');
                $initials = implode('', array_map(fn($w) => strtoupper($w[0]), array_slice(explode(' ', $name), 0, 2)));
                return [
                    'id'           => $r->id,
                    'feedback'     => $feedback,
                    'rating'       => $r->rating,
                    'school_name'  => $name,
                    'logo'        => $logo,
                    'logo_url'     => $this->resolveLogoUrl($r->organization->logo ?? null),
                    'initials'     => $initials ?: 'S',
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => $reviews,
        ]);
    }

    /** GET /api/website/privacy-policy */
    public function privacyPolicy()
    {
        $policy = PrivacyPolicy::first();

        return response()->json([
            'success' => true,
            'data'    => $policy ? [
                'sections'     => $policy->metadata['sections'] ?? [],
                'last_updated' => $policy->last_updated?->format('d M Y'),
            ] : null,
        ]);
    }

    /** GET /api/website/terms-conditions */
    public function termsConditions()
    {
        $tc = TermAndCondition::first();

        return response()->json([
            'success' => true,
            'data'    => $tc ? [
                'platform_name' => $tc->platform_name,
                'company_name'  => $tc->company_name,
                'sections'      => $tc->metadata['sections'] ?? [],
                'last_updated'  => $tc->last_updated?->format('d M Y'),
            ] : null,
        ]);
    }

    /** GET /api/website/terms-of-use */
    public function termsOfUse()
    {
        $tou = TermOfUse::first();

        return response()->json([
            'success' => true,
            'data'    => $tou ? [
                'sections'     => $tou->metadata['sections'] ?? [],
                'last_updated' => $tou->last_updated?->format('d M Y'),
            ] : null,
        ]);
    }

    /**
     * GET /api/website/page/{slug}
     * Returns the metadata for a dynamic marketing page
     * (why-us, services, careers, become-executive, blogs, faqs).
     */
    public function page(string $slug)
    {
        $page = WebsitePage::where('slug', $slug)->first();

        return response()->json([
            'success' => true,
            'data'    => $page ? array_merge(
                $page->metadata ?? [],
                ['last_updated' => $page->last_updated?->format('d M Y')]
            ) : null,
        ]);
    }

    /** POST /api/website/contact */
    public function contact(Request $request)
    {
        $validated = $request->validate([
            'full_name'    => 'required|string|max:255',
            'school_name'  => 'required|string|max:255',
            'phone_number' => ['required', 'string', 'regex:/^[6-9][0-9]{9}$/'],
            'email'        => 'required|email|max:255',
            'subject'      => 'required|string|max:255',
            'description'  => 'required|string|max:5000',
        ]);

        WebsiteContact::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully! We will get back to you within 3 business days.',
        ]);
    }

    /** POST /api/website/demo */
    public function demo(Request $request)
    {
        $validated = $request->validate([
            'full_name'      => 'required|string|max:255',
            'school_name'    => 'required|string|max:255',
            'phone'          => ['required', 'string', 'regex:/^[6-9][0-9]{9}$/'],
            'email'          => 'required|email|max:255',
            'city'           => 'required|string|max:255',
            'no_of_students' => 'required|string|max:50',
            'role'           => 'required|string|max:100',
        ]);

        WebsiteDemo::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Demo request received! Our team will contact you within 3 business days.',
        ]);
    }
}
