<div class="min-h-screen bg-gray-50">
    @include('livewire.super-admin.website.partials.topbar', [
        'heading'     => 'Careers',
        'description' => 'Manage job openings and view applications submitted from the website.',
        'url'         => 'web/careers',
    ])

    {{-- ── Tabs ── --}}
    <div class="max-w-5xl mx-auto px-4 sm:px-6 pt-6">
        <div class="inline-flex bg-white border border-gray-200 rounded-xl p-1 shadow-sm">
            <button wire:click="switchTab('jobs')"
                @class([
                    'px-4 py-2 text-sm font-semibold rounded-lg transition-colors',
                    'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow' => $activeTab === 'jobs',
                    'text-gray-600 hover:bg-gray-50' => $activeTab !== 'jobs',
                ])>
                Job Openings
            </button>
            <button wire:click="switchTab('applications')"
                @class([
                    'px-4 py-2 text-sm font-semibold rounded-lg transition-colors flex items-center gap-2',
                    'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow' => $activeTab === 'applications',
                    'text-gray-600 hover:bg-gray-50' => $activeTab !== 'applications',
                ])>
                Applications
                @if ($newCount > 0)
                    <span @class([
                        'text-[11px] font-bold px-1.5 py-0.5 rounded-full',
                        'bg-white/25 text-white' => $activeTab === 'applications',
                        'bg-indigo-100 text-indigo-700' => $activeTab !== 'applications',
                    ])>{{ $newCount }} new</span>
                @endif
            </button>
        </div>
    </div>

    {{-- ══════════════════ TAB 1 — JOB OPENINGS ══════════════════ --}}
    @if ($activeTab === 'jobs')
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-6 space-y-6">
            <p class="text-sm text-gray-500">
                Add or remove the job openings shown on the public Careers page. Click
                <span class="font-semibold text-gray-700">Save Changes</span> at the top to publish.
            </p>

            @include('livewire.super-admin.website.partials.repeater', [
                'key'      => 'jobs',
                'label'    => 'Open Positions',
                'singular' => 'Job',
                'cols'     => 2,
                'fields'   => [
                    ['name' => 'role',       'label' => 'Role',       'full' => true, 'placeholder' => 'Business Development Executive'],
                    ['name' => 'salary',     'label' => 'Salary',     'placeholder' => '₹3–6 LPA + incentives'],
                    ['name' => 'department', 'label' => 'Department',  'placeholder' => 'Sales'],
                    ['name' => 'location',   'label' => 'Location',    'placeholder' => 'Aligarh / Remote'],
                    ['name' => 'type',       'label' => 'Type',        'placeholder' => 'Full-time'],
                ],
            ])
        </div>
    @endif

    {{-- ══════════════════ TAB 2 — APPLICATIONS ══════════════════ --}}
    @if ($activeTab === 'applications')
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-6">
            @if ($applications->isEmpty())
                <div class="bg-white border border-gray-200 rounded-2xl p-12 text-center shadow-sm">
                    <div class="text-4xl mb-3">📭</div>
                    <h3 class="text-base font-semibold text-gray-800">No applications yet</h3>
                    <p class="text-sm text-gray-500 mt-1">Applications submitted from the Careers page will appear here.</p>
                </div>
            @else
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                    <th class="px-5 py-3">Applicant</th>
                                    <th class="px-5 py-3">Applied For</th>
                                    <th class="px-5 py-3">Contact</th>
                                    <th class="px-5 py-3">Date</th>
                                    <th class="px-5 py-3">Status</th>
                                    <th class="px-5 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($applications as $app)
                                    <tr class="hover:bg-gray-50/70">
                                        <td class="px-5 py-3">
                                            <div class="font-semibold text-gray-900">{{ $app->full_name }}</div>
                                            <div class="text-xs text-gray-400">{{ $app->experience ?: $app->qualification }}</div>
                                        </td>
                                        <td class="px-5 py-3 text-gray-600">{{ $app->job_role ?: '—' }}</td>
                                        <td class="px-5 py-3 text-gray-600">
                                            <div>{{ $app->mobile }}</div>
                                            <div class="text-xs text-gray-400">{{ $app->email }}</div>
                                        </td>
                                        <td class="px-5 py-3 text-gray-500 whitespace-nowrap">{{ $app->created_at->format('d M Y') }}</td>
                                        <td class="px-5 py-3">
                                            @if ($app->status === 'reviewed')
                                                <span class="text-xs font-semibold px-2 py-1 rounded-full bg-green-100 text-green-700">Reviewed</span>
                                            @else
                                                <span class="text-xs font-semibold px-2 py-1 rounded-full bg-amber-100 text-amber-700">New</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <button wire:click="viewApplication({{ $app->id }})"
                                                    class="px-2.5 py-1.5 text-xs font-medium text-indigo-600 border border-indigo-200 rounded-lg hover:bg-indigo-50 transition-colors">
                                                    View
                                                </button>
                                                @if ($app->document_path)
                                                    <button wire:click="downloadDocument({{ $app->id }})"
                                                        class="px-2.5 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                                        Doc
                                                    </button>
                                                @endif
                                                <button wire:click="confirmDeleteApplication({{ $app->id }})"
                                                    class="px-2.5 py-1.5 text-xs font-medium text-red-600 border border-red-200 rounded-lg hover:bg-red-50 transition-colors">
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-4">{{ $applications->links() }}</div>
            @endif
        </div>
    @endif

    {{-- ── Application detail modal ── --}}
    @if ($viewing)
        <div class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-[9999] px-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden max-h-[90vh] flex flex-col">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-indigo-50 to-purple-50">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">{{ $viewing->full_name }}</h3>
                        <p class="text-xs text-gray-500">{{ $viewing->job_role ?: 'General application' }}</p>
                    </div>
                    <button wire:click="closeApplication" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
                </div>
                <div class="p-6 space-y-4 overflow-y-auto">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs font-semibold text-gray-400 uppercase">Email</div>
                            <div class="text-sm text-gray-800 break-all">{{ $viewing->email }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-gray-400 uppercase">Mobile</div>
                            <div class="text-sm text-gray-800">{{ $viewing->mobile }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-gray-400 uppercase">Experience</div>
                            <div class="text-sm text-gray-800">{{ $viewing->experience ?: '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-gray-400 uppercase">Applied On</div>
                            <div class="text-sm text-gray-800">{{ $viewing->created_at->format('d M Y, h:i A') }}</div>
                        </div>
                        @if ($viewing->qualification)
                            <div>
                                <div class="text-xs font-semibold text-gray-400 uppercase">Qualification</div>
                                <div class="text-sm text-gray-800">{{ $viewing->qualification }}</div>
                            </div>
                        @endif
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-gray-400 uppercase">Address</div>
                        <div class="text-sm text-gray-800 whitespace-pre-line">{{ $viewing->address ?: '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-gray-400 uppercase">About / Message</div>
                        <div class="text-sm text-gray-800 whitespace-pre-line">{{ $viewing->description ?: '—' }}</div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 flex items-center gap-2 bg-gray-50">
                    @if ($viewing->document_path)
                        <button wire:click="downloadDocument({{ $viewing->id }})"
                            class="px-4 py-2 text-sm font-semibold text-indigo-600 border border-indigo-200 rounded-lg hover:bg-indigo-50 transition-colors">
                            ⬇ Download Document
                        </button>
                    @endif
                    <button wire:click="toggleReviewed({{ $viewing->id }})"
                        class="px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all">
                        {{ $viewing->status === 'reviewed' ? 'Mark as New' : 'Mark as Reviewed' }}
                    </button>
                    <button wire:click="closeApplication"
                        class="ml-auto px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ── Delete application confirm ── --}}
    @if ($pendingAppDelete !== null)
        <div class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-[9999] px-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-red-50 flex items-center gap-3">
                    <div class="w-9 h-9 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900">Delete Application</h3>
                </div>
                <div class="p-5">
                    <p class="text-sm text-gray-600">Are you sure you want to delete this application? This will also remove the attached document and cannot be undone.</p>
                </div>
                <div class="px-5 pb-5 flex items-center gap-2">
                    <button wire:click="deleteApplication"
                        class="flex-1 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition-colors">
                        Yes, Delete
                    </button>
                    <button wire:click="cancelDeleteApplication"
                        class="flex-1 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    @endif

    @include('livewire.super-admin.website.partials.delete-modal')
</div>
