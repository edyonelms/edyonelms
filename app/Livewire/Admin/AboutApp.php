<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\AboutApp as AboutAppModel;

class AboutApp extends Component
{
    public $aboutApp = null;
    public array $contactDetails = [];

    public function mount(): void
    {
        $this->aboutApp = AboutAppModel::first();
    }

    public function render()
    {
        return view('livewire.admin.about-app');
    }
}
