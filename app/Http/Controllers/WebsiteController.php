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
                'logo_url' => $school->logo,
            ]);

        return response()->json([
            'success' => true,
            'data'    => $schools,
        ]);
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
                    'logo_url'     => $r->organization->logo ?? null,
                    'initials'     => $initials ?: 'S',
                    // 'initials'    => $initials,
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

    /** POST /api/website/contact */
    public function contact(Request $request)
    {
        $validated = $request->validate([
            'full_name'    => 'required|string|max:255',
            'school_name'  => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email'        => 'required|email|max:255',
            'subject'      => 'nullable|string|max:255',
            'description'  => 'nullable|string',
        ]);

        WebsiteContact::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully! We will get back to you within 24 hours.',
        ]);
    }

    /** POST /api/website/demo */
    public function demo(Request $request)
    {
        $validated = $request->validate([
            'full_name'      => 'required|string|max:255',
            'school_name'    => 'required|string|max:255',
            'phone'          => 'required|string|max:20',
            'email'          => 'required|email|max:255',
            'city'           => 'nullable|string|max:255',
            'no_of_students' => 'nullable|string|max:50',
            'role'           => 'nullable|string|max:100',
        ]);

        WebsiteDemo::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Demo request received! Our team will contact you within 2 hours.',
        ]);
    }
}
