<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;

class QuickLinks extends Component
{
    public $links = [];

    public function mount()
    {
        $configLinks = config('menu.super-admin');

        // Stable, evenly-spread palette — assigned by position so a tile keeps
        // the same colour on every load (no more random flicker on reload).
        $colors = [
            'blue', 'indigo', 'violet', 'purple', 'fuchsia', 'pink', 'rose',
            'red', 'orange', 'amber', 'lime', 'green', 'teal', 'cyan', 'sky',
        ];

        $configLinks = array_values(array_filter(
            $configLinks,
            fn($link) => $link['link'] !== 'super-admin.quick-links'
        ));

        foreach ($configLinks as $i => $link) {
            $this->links[] = [
                'title' => $link['title'],
                'route' => $link['link'],
                'icon'  => $link['icon'],
                'color' => $colors[$i % count($colors)],
            ];
        }
    }

    public function render()
    {
        return view('livewire.super-admin.quick-links');
    }
}
