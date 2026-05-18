<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Admin\Announcement as AnnouncementModel;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Rule;
use Illuminate\Support\Facades\Storage;
use WireUi\Traits\WireUiActions;
use Carbon\Carbon;

class Announcement extends Component
{
    use WithPagination, WithFileUploads, WireUiActions;

    public $open = false;
    public $viewModal = false;
    public $editId = null;
    public $selectedAnnouncement = null;
    public $dateFilter = 'all';

    #[Rule('required|string|max:255')]
    public $announcementName = '';

    #[Rule('required|string')]
    public $announcementContent = '';

    #[Rule('required|in:all,user,teacher')]
    public $type = 'all';

    #[Rule('nullable|image|max:2048')] // 2MB max
    public $announcementImage;

    #[Rule('nullable|mimes:pdf|max:5120')] // 5MB max
    public $announcementPdf;

    public function render()
    {
        $query = AnnouncementModel::where('organization_id', Auth::user()->organization_id)->latest();

        if ($this->dateFilter !== 'all') {
            $days = (int) $this->dateFilter;
            $startDate = Carbon::now()->subDays($days);
            $query->where('created_at', '>=', $startDate);
        }

        $announcements = $query->paginate(10);

        // Stats
        $baseQuery = AnnouncementModel::where('organization_id', Auth::user()->organization_id);
        $stats = [
            'total'       => (clone $baseQuery)->count(),
            'this_month'  => (clone $baseQuery)->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)->count(),
            'last_month'  => (clone $baseQuery)->whereMonth('created_at', Carbon::now()->subMonth()->month)
                ->whereYear('created_at', Carbon::now()->subMonth()->year)->count(),
        ];

        return view('livewire.admin.announcement', compact('announcements', 'stats'));
    }

    public function openModal()
    {
        $this->open = true;
        $this->resetForm();
    }

    public function viewAnnouncement($id)
    {
        $this->selectedAnnouncement = AnnouncementModel::findOrFail($id);
        $this->viewModal = true;
    }

    public function closeViewModal()
    {
        $this->viewModal = false;
        $this->selectedAnnouncement = null;
    }

    public function editFromView($id)
    {
        $this->viewModal = false;
        $this->edit($id);
    }

    public function save()
    {
        $this->validate();

        $data = [
            'organization_id' => Auth::user()->organization_id,
            'user_id' => Auth::user()->id,
            'announcement_name' => $this->announcementName,
            'announcement_content' => $this->announcementContent,
            'type' => $this->type,
        ];

        // Handle image upload
        if ($this->announcementImage) {
            $imagePath = $this->announcementImage->store('admin/announcements/images', 's3');
            Storage::disk('s3')->setVisibility($imagePath, 'public');
            $data['announcement_image'] = Storage::disk('s3')->url($imagePath);
        }

        // Handle PDF upload
        if ($this->announcementPdf) {
            $pdfPath = $this->announcementPdf->store('admin/announcements/pdfs', 's3');
            Storage::disk('s3')->setVisibility($pdfPath, 'public');
            $data['announcement_pdf'] = Storage::disk('s3')->url($pdfPath);
        }

        if ($this->editId) {
            $announcement = AnnouncementModel::find($this->editId);

            // Delete old files if new ones are uploaded
            if ($this->announcementImage && $announcement->announcement_image) {
                $oldImagePath = parse_url($announcement->announcement_image, PHP_URL_PATH);
                Storage::disk('s3')->delete($oldImagePath);
            }

            if ($this->announcementPdf && $announcement->announcement_pdf) {
                $oldPdfPath = parse_url($announcement->announcement_pdf, PHP_URL_PATH);
                Storage::disk('s3')->delete($oldPdfPath);
            }

            $announcement->update($data);
            $message = 'Announcement updated successfully!';
        } else {
            AnnouncementModel::create($data);
            $message = 'Announcement created successfully!';
        }

        $this->closeModal();
        $this->dispatch('notify', type: 'success', message: $message);
    }

    public function edit($id)
    {
        $announcement = AnnouncementModel::findOrFail($id);
        $this->editId = $id;
        $this->announcementName = $announcement->announcement_name;
        $this->announcementContent = $announcement->announcement_content;
        $this->type = $announcement->type;
        $this->open = true;
    }

    public function onDelete($id)
    {
        $this->dialog()->confirm([
            'title' => 'Are you Sure?',
            'icon' => 'exclamation-circle',
            'iconColor' => 'text-red-500',
            'description' => 'Are you sure you want to delete this announcement? This action cannot be undone.',
            'accept' => [
                'label' => 'Yes, delete it',
                'method' => 'doDelete',
                'params' => $id,
                'color' => 'negative',
                'size' => 'md',
            ],
            'reject' => [
                'label' => 'No, cancel',
                'size' => 'md',
            ],
        ]);
    }

    public function doDelete($id)
    {
        $announcement = AnnouncementModel::find($id);

        if ($announcement) {
            // Delete associated files from S3
            if ($announcement->announcement_image) {
                $oldImagePath = parse_url($announcement->announcement_image, PHP_URL_PATH);
                Storage::disk('s3')->delete($oldImagePath);
            }

            if ($announcement->announcement_pdf) {
                $oldPdfPath = parse_url($announcement->announcement_pdf, PHP_URL_PATH);
                Storage::disk('s3')->delete($oldPdfPath);
            }

            // Delete the announcement record
            $announcement->delete();

            $this->dispatch('notify', type: 'success', message: "Announcement Deleted Successfully!");
        } else {
            $this->dispatch('notify', type: 'error', message: "Announcement not found!");
        }
    }

    public function deleteFile($type)
    {
        if ($type === 'image') {
            if ($this->editId && $this->announcementImage) {
                $this->announcementImage = null;
            } elseif ($this->editId) {
                $announcement = AnnouncementModel::find($this->editId);
                if ($announcement->announcement_image) {
                    $oldImagePath = parse_url($announcement->announcement_image, PHP_URL_PATH);
                    Storage::disk('s3')->delete($oldImagePath);
                    $announcement->update(['announcement_image' => null]);
                }
            }
        } elseif ($type === 'pdf') {
            if ($this->editId && $this->announcementPdf) {
                $this->announcementPdf = null;
            } elseif ($this->editId) {
                $announcement = AnnouncementModel::find($this->editId);
                if ($announcement->announcement_pdf) {
                    $oldPdfPath = parse_url($announcement->announcement_pdf, PHP_URL_PATH);
                    Storage::disk('s3')->delete($oldPdfPath);
                    $announcement->update(['announcement_pdf' => null]);
                }
            }
        }
    }

    public function closeModal()
    {
        $this->open = false;
        $this->resetForm();
    }

    protected function resetForm()
    {
        $this->reset([
            'editId',
            'announcementName',
            'announcementContent',
            'type',
            'announcementImage',
            'announcementPdf'
        ]);
        $this->resetErrorBag();
    }
}
