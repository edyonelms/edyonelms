<div class="min-h-screen bg-gray-50">
    @include('livewire.super-admin.website.partials.topbar', [
        'heading'     => 'Become an Executive',
        'description' => 'Manage the partner program benefits and steps.',
        'url'         => 'web/become-an-executive',
    ])

    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8 space-y-6">
        @include('livewire.super-admin.website.partials.header-fields')

        @include('livewire.super-admin.website.partials.repeater', [
            'key'      => 'benefits',
            'label'    => 'Benefits',
            'singular' => 'Benefit',
            'cols'     => 2,
            'fields'   => [
                ['name' => 'icon',  'label' => 'Icon (emoji)', 'placeholder' => '💸'],
                ['name' => 'title', 'label' => 'Title',        'placeholder' => 'Attractive Commissions'],
                ['name' => 'desc',  'label' => 'Description',   'type' => 'textarea', 'full' => true, 'placeholder' => 'Short description...'],
            ],
        ])

        @include('livewire.super-admin.website.partials.repeater', [
            'key'      => 'steps',
            'label'    => 'How It Works (Steps)',
            'singular' => 'Step',
            'cols'     => 2,
            'fields'   => [
                ['name' => 'title', 'label' => 'Step Title',  'placeholder' => 'Apply'],
                ['name' => 'desc',  'label' => 'Description',  'type' => 'textarea', 'full' => true, 'placeholder' => 'What happens in this step...'],
            ],
        ])
    </div>

    @include('livewire.super-admin.website.partials.delete-modal')
</div>
