<?php

namespace App\Livewire\SuperAdmin;

use App\Livewire\SuperAdmin\Concerns\ManagesWebsitePage;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class Careers extends Component
{
    use WireUiActions, ManagesWebsitePage;

    public function mount(): void
    {
        $this->slug = 'careers';
        $this->loadPage();
    }

    protected function defaultMeta(): array
    {
        return [
            'tag'      => '',
            'title'    => '',
            'subtitle' => '',
            'perks'    => [$this->rowTemplates()['perks']],
            'jobs'     => [$this->rowTemplates()['jobs']],
        ];
    }

    protected function rowTemplates(): array
    {
        return [
            'perks' => ['icon' => '', 'title' => '', 'desc' => ''],
            'jobs'  => ['role' => '', 'department' => '', 'location' => '', 'type' => ''],
        ];
    }

    public function render()
    {
        return view('livewire.super-admin.website.careers');
    }
}
