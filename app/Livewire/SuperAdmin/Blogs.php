<?php

namespace App\Livewire\SuperAdmin;

use App\Livewire\SuperAdmin\Concerns\ManagesWebsitePage;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class Blogs extends Component
{
    use WireUiActions, ManagesWebsitePage;

    public function mount(): void
    {
        $this->slug = 'blogs';
        $this->loadPage();
    }

    protected function defaultMeta(): array
    {
        return [
            'tag'      => '',
            'title'    => '',
            'subtitle' => '',
            'posts'    => [$this->rowTemplates()['posts']],
        ];
    }

    protected function rowTemplates(): array
    {
        return ['posts' => [
            'category'  => '',
            'icon'      => '',
            'title'     => '',
            'excerpt'   => '',
            'read_time' => '',
            'link'      => '',
        ]];
    }

    public function render()
    {
        return view('livewire.super-admin.website.blogs');
    }
}
