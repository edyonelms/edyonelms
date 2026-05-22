<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;

class QuickLinks extends Component
{
    public $links = [];

    public function mount()
    {
        $configLinks = config('menu.super-admin');

        $colors = [
            'blue',
            'indigo',
            'purple',
            'green',
            'yellow',
            'pink',
            'teal',
            'rose',
            'cyan',
            'lime',
            'fuchsia',
            'red',
            'orange',
            'amber',
            'sky',
            'violet',
            'gray'
        ];

        $configLinks = array_filter($configLinks, fn($link) => $link['link'] !== 'super-admin.quick-links');

        foreach ($configLinks as $link) {
            $randomColor = $colors[array_rand($colors)];
            $this->links[] = [
                'title' => $link['title'],
                'route' => $link['link'],
                'icon' => $link['icon'],
                'color' => $randomColor
            ];
        }
    }

    public function render()
    {
        return view('livewire.super-admin.quick-links');
    }
}
