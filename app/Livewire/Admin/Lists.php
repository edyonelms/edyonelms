<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class Lists extends Component
{
    public $organization = null;

    public function mount(): void
    {
        $this->organization = request()->route('organization')
            ?? auth()->user()?->organization_id;
    }

    public function render()
    {
        return view('livewire.admin.lists');
    }
}
