<?php

namespace App\Livewire\SuperAdmin;

use App\Livewire\SuperAdmin\Concerns\ManagesWebsitePage;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class BecomeExecutive extends Component
{
    use WireUiActions, ManagesWebsitePage;

    public function mount(): void
    {
        $this->slug = 'become-executive';
        $this->loadPage();
    }

    protected function defaultMeta(): array
    {
        return [
            'tag'      => '',
            'title'    => '',
            'subtitle' => '',
            'benefits' => [$this->rowTemplates()['benefits']],
            'steps'    => [$this->rowTemplates()['steps']],
        ];
    }

    protected function rowTemplates(): array
    {
        return [
            'benefits' => ['icon' => '', 'title' => '', 'desc' => ''],
            'steps'    => ['title' => '', 'desc' => ''],
        ];
    }

    public function render()
    {
        return view('livewire.super-admin.website.become-executive');
    }
}
