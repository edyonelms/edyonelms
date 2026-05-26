<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class QuickLinks extends Component
{
    public $links = [];

    public function mount()
    {
        $configLink1 = config('menu.admin');


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

        $configLink1 = array_filter($configLink1, fn($link) => $link['link'] !== 'admin.quick-links');

        usort($configLink1, fn($a, $b) => strcasecmp($a['title'], $b['title']));

        foreach ($configLink1 as $link) {
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
        return view('livewire.admin.quick-links');
    }
}
