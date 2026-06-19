<div class="min-h-screen bg-gray-50">
    @include('livewire.super-admin.website.partials.topbar', [
        'heading'     => 'Blogs',
        'description' => 'Manage the blog posts shown on the website.',
        'url'         => 'web/blogs',
    ])

    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8 space-y-6">
        @include('livewire.super-admin.website.partials.header-fields')

        @include('livewire.super-admin.website.partials.repeater', [
            'key'      => 'posts',
            'label'    => 'Blog Posts',
            'singular' => 'Post',
            'cols'     => 2,
            'fields'   => [
                ['name' => 'category',  'label' => 'Category',       'placeholder' => 'School Tech'],
                ['name' => 'icon',      'label' => 'Icon (emoji)',   'placeholder' => '📲'],
                ['name' => 'title',     'label' => 'Title',          'full' => true, 'placeholder' => '5 ways an LMS saves your school hours every week'],
                ['name' => 'excerpt',   'label' => 'Excerpt',        'type' => 'textarea', 'full' => true, 'placeholder' => 'Short summary of the post...'],
                ['name' => 'read_time', 'label' => 'Read Time',      'placeholder' => '5 min read'],
                ['name' => 'link',      'label' => 'Link (URL)',     'placeholder' => '#'],
            ],
        ])
    </div>

    @include('livewire.super-admin.website.partials.delete-modal')
</div>
