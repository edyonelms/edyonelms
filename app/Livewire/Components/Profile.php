<?php

namespace App\Livewire\Components;

use Livewire\Component;

class Profile extends Component
{
    public $activeTab = 'profile';

    public function showTab($tab)
    {
        // Line 13 check karein, yahan $ hona chahiye
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.components.profile');
    }
}
