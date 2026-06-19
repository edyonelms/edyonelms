<?php

namespace App\Livewire\SuperAdmin;

use App\Livewire\SuperAdmin\Concerns\ManagesWebsitePage;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class Faqs extends Component
{
    use WireUiActions, ManagesWebsitePage;

    public function mount(): void
    {
        $this->slug = 'faqs';
        $this->loadPage();
    }

    protected function defaultMeta(): array
    {
        return [
            'tag'      => '',
            'title'    => '',
            'subtitle' => '',
            'faqs'     => [$this->rowTemplates()['faqs']],
        ];
    }

    protected function rowTemplates(): array
    {
        return ['faqs' => ['question' => '', 'answer' => '']];
    }

    public function render()
    {
        return view('livewire.super-admin.website.faqs');
    }
}
