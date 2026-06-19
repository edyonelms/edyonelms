<div class="min-h-screen bg-gray-50">
    @include('livewire.super-admin.website.partials.topbar', [
        'heading'     => 'FAQs',
        'description' => 'Manage the frequently asked questions.',
        'url'         => 'web/faqs',
    ])

    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8 space-y-6">
        @include('livewire.super-admin.website.partials.header-fields')

        @include('livewire.super-admin.website.partials.repeater', [
            'key'      => 'faqs',
            'label'    => 'Questions & Answers',
            'singular' => 'FAQ',
            'cols'     => 1,
            'fields'   => [
                ['name' => 'question', 'label' => 'Question', 'placeholder' => 'What is EDYONE LMS?'],
                ['name' => 'answer',   'label' => 'Answer',   'type' => 'textarea', 'placeholder' => 'Write the answer...'],
            ],
        ])
    </div>

    @include('livewire.super-admin.website.partials.delete-modal')
</div>
