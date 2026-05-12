<div class="min-h-screen bg-gray-50/50">

    {{-- ═══ STICKY HEADER ═══ --}}
    <div class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
        <div class="px-6 pt-4 pb-3 flex items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-emerald-700 leading-tight">Payroll</h1>
                <p class="text-xs text-gray-400 mt-0.5">Teacher payroll roster and records</p>
            </div>
        </div>

        {{-- Analytics Strip --}}
        <div class="px-6 pb-3 grid grid-cols-2 gap-3">
            <div class="flex items-center gap-3 bg-emerald-50 rounded-xl px-4 py-3 border border-emerald-100">
                <div class="w-8 h-8 rounded-lg bg-emerald-500 flex items-center justify-center flex-shrink-0">
                    <x-icon name="users" class="w-4 h-4 text-white" />
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Total Teachers</p>
                    <p class="text-lg font-bold text-emerald-700">{{ $totalTeachers }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3 bg-blue-50 rounded-xl px-4 py-3 border border-blue-100">
                <div class="w-8 h-8 rounded-lg bg-blue-500 flex items-center justify-center flex-shrink-0">
                    <x-icon name="user-plus" class="w-4 h-4 text-white" />
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Joined This Month</p>
                    <p class="text-lg font-bold text-blue-700">{{ $joinedThisMonth }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="p-6 space-y-5">

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-green-700 text-sm flex items-center gap-2">
                <x-icon name="check-circle" class="w-4 h-4 text-green-500" />
                {{ session('success') }}
            </div>
        @endif

        {{-- Info Notice --}}
        <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-xs text-amber-700 flex items-start gap-2">
            <x-icon name="information-circle" class="w-4 h-4 flex-shrink-0 mt-0.5" />
            <span>Salary payment management is being configured. Currently showing the teacher roster with their joining details.</span>
        </div>

        {{-- ─── Filter Bar ──────────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4">
            <div class="flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
                    <div class="relative">
                        <x-icon name="magnifying-glass" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                        <input wire:model.live="search" type="text"
                            placeholder="Name, email or employee ID…"
                            class="w-full pl-9 pr-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 outline-none" />
                    </div>
                </div>
                <button wire:click="resetFilters"
                    class="px-3 py-2 text-sm text-gray-500 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors flex items-center gap-1">
                    <x-icon name="x-mark" class="w-4 h-4" /> Clear
                </button>
            </div>
        </div>

        {{-- ─── Teachers Table ──────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">#</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Name</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Employee ID</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Qualification</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Phone</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Joined</th>
                            <th class="text-right px-4 py-3 font-semibold text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($teachers as $teacher)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-4 py-3 text-gray-400 text-xs">{{ $loop->iteration + ($teachers->currentPage() - 1) * $teachers->perPage() }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                            <span class="text-xs font-bold text-emerald-700">
                                                {{ strtoupper(substr($teacher->user?->name ?? 'T', 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800">{{ $teacher->user?->name ?? '—' }}</p>
                                            <p class="text-xs text-gray-400">{{ $teacher->user?->email ?? '' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-gray-600 text-xs font-mono">{{ $teacher->employee_id ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-600 text-xs">{{ $teacher->qualification ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-600 text-xs">{{ $teacher->phone ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-600 text-xs">
                                    {{ $teacher->date_of_joining ? \Carbon\Carbon::parse($teacher->date_of_joining)->format('d M Y') : '—' }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button wire:click="viewTeacher({{ $teacher->id }})"
                                        class="p-1.5 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors" title="View Details">
                                        <x-icon name="eye" class="w-4 h-4" />
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                                    <x-icon name="users" class="w-10 h-10 mx-auto mb-2 text-gray-200" />
                                    <p class="text-sm">No teachers found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($teachers->hasPages())
                <div class="px-4 py-3 border-t border-gray-100">
                    {{ $teachers->links() }}
                </div>
            @endif
        </div>

    </div>

    {{-- ═══ VIEW MODAL ═══ --}}
    @if($showViewModal && $viewTeacher)
    <div class="fixed inset-0 z-[999] flex items-center justify-center px-4 py-6"
         style="background: rgba(0,0,0,0.45); backdrop-filter: blur(4px);"
         wire:click="closeViewModal">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md flex flex-col max-h-[90vh]"
             wire:click.stop>

            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
                <h2 class="text-lg font-bold text-gray-800">Teacher Details</h2>
                <button wire:click="closeViewModal"
                    class="p-2 text-gray-400 hover:bg-gray-100 rounded-lg transition-colors">
                    <x-icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>

            <div class="px-6 py-5 overflow-y-auto space-y-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                        <span class="text-xl font-bold text-emerald-700">
                            {{ strtoupper(substr($viewTeacher->user?->name ?? 'T', 0, 1)) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-gray-800">{{ $viewTeacher->user?->name ?? '—' }}</p>
                        <p class="text-sm text-gray-400">{{ $viewTeacher->user?->email ?? '' }}</p>
                    </div>
                </div>

                <dl class="divide-y divide-gray-100">
                    <div class="flex justify-between py-2 text-sm">
                        <dt class="text-gray-500 font-medium">Employee ID</dt>
                        <dd class="text-gray-800 font-mono text-xs">{{ $viewTeacher->employee_id ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between py-2 text-sm">
                        <dt class="text-gray-500 font-medium">Qualification</dt>
                        <dd class="text-gray-700">{{ $viewTeacher->qualification ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between py-2 text-sm">
                        <dt class="text-gray-500 font-medium">Phone</dt>
                        <dd class="text-gray-700">{{ $viewTeacher->phone ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between py-2 text-sm">
                        <dt class="text-gray-500 font-medium">Date of Joining</dt>
                        <dd class="text-gray-700">
                            {{ $viewTeacher->date_of_joining ? \Carbon\Carbon::parse($viewTeacher->date_of_joining)->format('d M Y') : '—' }}
                        </dd>
                    </div>
                    <div class="flex justify-between py-2 text-sm">
                        <dt class="text-gray-500 font-medium">Address</dt>
                        <dd class="text-gray-700 text-right max-w-[60%]">{{ $viewTeacher->address ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between py-2 text-sm">
                        <dt class="text-gray-500 font-medium">Emergency Contact</dt>
                        <dd class="text-gray-700">{{ $viewTeacher->emergency_contact ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 flex justify-end flex-shrink-0">
                <button wire:click="closeViewModal"
                    class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
