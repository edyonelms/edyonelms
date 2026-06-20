<?php

namespace App\Livewire\SuperAdmin;

use App\Livewire\SuperAdmin\Concerns\ManagesWebsitePage;
use App\Models\CareerApplication;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class Careers extends Component
{
    use WireUiActions, ManagesWebsitePage, WithPagination;

    /** Application currently open in the detail modal. */
    public ?int $viewingId = null;

    /** Application pending delete confirmation. */
    public ?int $pendingAppDelete = null;

    public function mount(): void
    {
        $this->slug = 'careers';
        $this->loadPage();
        $this->activeTab = 'jobs';
    }

    protected function defaultMeta(): array
    {
        return [
            'jobs' => [$this->rowTemplates()['jobs']],
        ];
    }

    protected function rowTemplates(): array
    {
        return [
            'jobs' => ['role' => '', 'department' => '', 'location' => '', 'type' => '', 'salary' => ''],
        ];
    }

    // ─── Applications ─────────────────────────────────────────────────

    public function viewApplication(int $id): void
    {
        $this->viewingId = $id;
    }

    public function closeApplication(): void
    {
        $this->viewingId = null;
    }

    public function downloadDocument(int $id): mixed
    {
        $app = CareerApplication::find($id);

        if (!$app || !$app->document_path) {
            $this->notification()->error('No document attached for this application.');
            return null;
        }

        if (!Storage::disk('s3')->exists($app->document_path)) {
            $this->notification()->error('File missing on storage.');
            return null;
        }

        $ext      = pathinfo($app->document_path, PATHINFO_EXTENSION) ?: 'pdf';
        $filename = 'application-' . str($app->full_name)->slug() . '.' . $ext;

        $url = Storage::disk('s3')->temporaryUrl(
            $app->document_path,
            now()->addMinutes(5),
            ['ResponseContentDisposition' => 'attachment; filename="' . $filename . '"'],
        );

        return $this->redirect($url);
    }

    public function toggleReviewed(int $id): void
    {
        $app = CareerApplication::find($id);
        if ($app) {
            $app->status = $app->status === 'reviewed' ? 'new' : 'reviewed';
            $app->save();
            $this->notification()->success('Updated', 'Application marked as ' . $app->status . '.');
        }
    }

    public function confirmDeleteApplication(int $id): void
    {
        $this->pendingAppDelete = $id;
    }

    public function cancelDeleteApplication(): void
    {
        $this->pendingAppDelete = null;
    }

    public function deleteApplication(): void
    {
        $app = CareerApplication::find($this->pendingAppDelete);
        if ($app) {
            if ($app->document_path && Storage::disk('s3')->exists($app->document_path)) {
                Storage::disk('s3')->delete($app->document_path);
            }
            $app->delete();
            $this->notification()->success('Deleted', 'Application removed.');
        }
        $this->pendingAppDelete = null;
        if ($this->viewingId === ($app->id ?? null)) {
            $this->viewingId = null;
        }
    }

    public function render()
    {
        $applications = CareerApplication::latest()->paginate(15);
        $viewing      = $this->viewingId ? CareerApplication::find($this->viewingId) : null;
        $newCount     = CareerApplication::where('status', 'new')->count();

        return view('livewire.super-admin.website.careers', [
            'applications' => $applications,
            'viewing'      => $viewing,
            'newCount'     => $newCount,
        ]);
    }
}
