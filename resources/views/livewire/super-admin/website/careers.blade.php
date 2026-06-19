<div class="min-h-screen bg-gray-50">
    @include('livewire.super-admin.website.partials.topbar', [
        'heading'     => 'Careers',
        'description' => 'Manage perks and open job positions.',
        'url'         => 'web/careers',
    ])

    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8 space-y-6">
        @include('livewire.super-admin.website.partials.header-fields')

        @include('livewire.super-admin.website.partials.repeater', [
            'key'      => 'perks',
            'label'    => 'Why Work Here (Perks)',
            'singular' => 'Perk',
            'cols'     => 2,
            'fields'   => [
                ['name' => 'icon',  'label' => 'Icon (emoji)', 'placeholder' => '🚀'],
                ['name' => 'title', 'label' => 'Title',        'placeholder' => 'Real Impact'],
                ['name' => 'desc',  'label' => 'Description',   'type' => 'textarea', 'full' => true, 'placeholder' => 'Short description...'],
            ],
        ])

        @include('livewire.super-admin.website.partials.repeater', [
            'key'      => 'jobs',
            'label'    => 'Open Positions',
            'singular' => 'Job',
            'cols'     => 2,
            'fields'   => [
                ['name' => 'role',       'label' => 'Role',       'full' => true, 'placeholder' => 'Business Development Executive'],
                ['name' => 'department', 'label' => 'Department',  'placeholder' => 'Sales'],
                ['name' => 'location',   'label' => 'Location',    'placeholder' => 'Field / Remote'],
                ['name' => 'type',       'label' => 'Type',        'placeholder' => 'Full-time'],
            ],
        ])
    </div>

    @include('livewire.super-admin.website.partials.delete-modal')
</div>
