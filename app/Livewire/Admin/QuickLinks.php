<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Url;
use Livewire\Component;

class QuickLinks extends Component
{
    public array $links = [];

    /** Display order: 'sidebar' (menu order) or 'asc' (A–Z by title). */
    #[Url]
    public string $sort = 'sidebar';

    /** Columns per row. Defaults to a 10-wide grid (≈ 10×4). */
    #[Url]
    public int $columns = 10;

    public array $columnOptions = [4, 5, 6, 8, 10];

    public function mount(): void
    {
        $configLinks = config('menu.admin', []);

        // Drop the Quick Links tile itself.
        $configLinks = array_filter($configLinks, fn($link) => $link['link'] !== 'admin.quick-links');

        // Hide modules this school has not been granted (core items always stay).
        $configLinks = \App\Support\ModuleAccess::filterMenu(
            array_values($configLinks),
            auth()->user()?->organization
        );

        $colors = [
            'blue', 'indigo', 'purple', 'green', 'yellow', 'pink', 'teal', 'rose',
            'cyan', 'lime', 'fuchsia', 'red', 'orange', 'amber', 'sky', 'violet', 'gray',
        ];

        // Preserve sidebar (config) order; give each tile a STABLE colour so
        // re-sorting / changing columns never reshuffles the palette.
        foreach (array_values($configLinks) as $i => $link) {
            $this->links[] = [
                'title' => $link['title'],
                'route' => $link['link'],
                'icon'  => $link['icon'],
                'color' => $colors[abs(crc32($link['title'])) % count($colors)],
                'order' => $i,
            ];
        }
    }

    /** Columns the grid should actually use (guarded against bad URL input). */
    protected function safeColumns(): int
    {
        return in_array((int) $this->columns, $this->columnOptions, true)
            ? (int) $this->columns
            : 10;
    }

    public function render()
    {
        $links = $this->links;

        if ($this->sort === 'asc') {
            usort($links, fn($a, $b) => strcasecmp($a['title'], $b['title']));
        } else {
            usort($links, fn($a, $b) => $a['order'] <=> $b['order']);
        }

        return view('livewire.admin.quick-links', [
            'orderedLinks' => $links,
            'columns'      => $this->safeColumns(),
        ]);
    }
}
