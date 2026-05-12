<?php

namespace App\Livewire\SuperAdmin;

use App\Models\AboutApp as AboutAppModel;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use WireUi\Traits\WireUiActions;

class AboutApp extends Component
{
    use WithFileUploads, WireUiActions;

    public string $activeTab = 'view';

    public $aboutApp;
    public $heading      = '';
    public $sub_heading  = '';
    public $content      = [];
    public $logo;
    public $logoPreview  = null;
    public $contact_details = [];
    public $address      = '';
    public $core_team    = [];
    public $social_media = [];

    // ─── Contact Modal ────────────────────────────────────────────────────────
    public bool  $showContactModal = false;
    public array $newContact       = [];
    public       $editContactIndex = null;

    // ─── Team Modal ───────────────────────────────────────────────────────────
    public bool  $showTeamModal       = false;
    public array $newTeamMember       = [];
    public       $newTeamMemberImage;
    public       $editTeamIndex       = null;

    // ─── Social Modal ─────────────────────────────────────────────────────────
    public bool  $showSocialModal    = false;
    public array $newSocialMedia     = [];
    public       $newSocialMediaIcon;
    public       $editSocialIndex    = null;

    // ─── Delete Confirmations ─────────────────────────────────────────────────
    public $pendingDeleteContactIndex = null;
    public $pendingDeleteTeamIndex    = null;
    public $pendingDeleteSocialIndex  = null;

    protected $rules = [
        'heading'                     => 'required|string|max:255',
        'sub_heading'                 => 'nullable|string|max:500',
        'content.*.title'             => 'nullable|string|max:255',
        'content.*.description'       => 'nullable|string',
        'logo'                        => 'nullable|image|max:2048',
        'address'                     => 'nullable|string|max:500',
        'newTeamMemberImage'          => 'nullable|image|max:2048',
        'newSocialMediaIcon'          => 'nullable|image|max:1024',
    ];

    public function mount(): void
    {
        $this->aboutApp = AboutAppModel::first();

        if ($this->aboutApp) {
            $this->heading         = $this->aboutApp->heading;
            $this->sub_heading     = $this->aboutApp->sub_heading;
            $this->content         = $this->aboutApp->content ?? [];
            $this->logoPreview     = $this->aboutApp->logo;
            $this->contact_details = $this->aboutApp->contact_details ?? [];
            $this->address         = $this->aboutApp->address ?? '';
            $this->core_team       = $this->aboutApp->core_team ?? [];
            $this->social_media    = $this->aboutApp->social_media ?? [];
        } else {
            $this->content = [['title' => '', 'description' => '']];
        }
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;

        // Reload data from DB when switching tabs so view/edit always shows fresh data
        if ($tab === 'edit') {
            $this->mount();
        } elseif ($tab === 'view') {
            $this->aboutApp = AboutAppModel::first();
        }
    }

    // ─── Content Sections ─────────────────────────────────────────────────────

    public function addContentSection(): void
    {
        $this->content[] = ['title' => '', 'description' => ''];
    }

    public function removeContentSection($index): void
    {
        if (count($this->content) > 1) {
            unset($this->content[$index]);
            $this->content = array_values($this->content);
        }
    }

    // ─── Contact ──────────────────────────────────────────────────────────────

    public function openContactModal($index = null): void
    {
        $this->editContactIndex = $index;
        $this->newContact = $index !== null
            ? $this->contact_details[$index]
            : ['type' => '', 'value' => ''];
        $this->showContactModal = true;
    }

    public function closeContactModal(): void
    {
        $this->showContactModal  = false;
        $this->editContactIndex  = null;
        $this->newContact        = [];
    }

    public function saveContact(): void
    {
        $this->validate([
            'newContact.type'  => 'required|string|max:100',
            'newContact.value' => 'required|string|max:255',
        ]);

        if ($this->editContactIndex !== null) {
            $this->contact_details[$this->editContactIndex] = $this->newContact;
        } else {
            $this->contact_details[] = $this->newContact;
        }

        $this->closeContactModal();
        $this->notification()->success($this->editContactIndex !== null ? 'Contact updated!' : 'Contact added!');
    }

    public function removeContact($index): void
    {
        $this->pendingDeleteContactIndex = $index;
    }

    public function executeRemoveContact(): void
    {
        if ($this->pendingDeleteContactIndex !== null) {
            unset($this->contact_details[$this->pendingDeleteContactIndex]);
            $this->contact_details = array_values($this->contact_details);
            $this->notification()->success('Contact removed!');
        }
        $this->pendingDeleteContactIndex = null;
    }

    public function cancelRemoveContact(): void
    {
        $this->pendingDeleteContactIndex = null;
    }

    // ─── Team ─────────────────────────────────────────────────────────────────

    public function openTeamModal($index = null): void
    {
        $this->editTeamIndex    = $index;
        $this->newTeamMember    = $index !== null
            ? $this->core_team[$index]
            : ['name' => '', 'position' => '', 'description' => '', 'link' => ''];
        $this->newTeamMemberImage = null;
        $this->showTeamModal    = true;
    }

    public function closeTeamModal(): void
    {
        $this->showTeamModal      = false;
        $this->editTeamIndex      = null;
        $this->newTeamMember      = [];
        $this->newTeamMemberImage = null;
    }

    public function saveTeamMember(): void
    {
        $this->validate([
            'newTeamMember.name'     => 'required|string|max:255',
            'newTeamMember.position' => 'required|string|max:255',
            'newTeamMember.description' => 'nullable|string',
            'newTeamMember.link'     => 'nullable|url:http,https|max:255',
            'newTeamMemberImage'     => 'nullable|image|max:2048',
        ]);

        $member = $this->newTeamMember;

        // Upload new image if provided
        if ($this->newTeamMemberImage) {
            // Delete old image if editing
            if ($this->editTeamIndex !== null && !empty($this->core_team[$this->editTeamIndex]['image'])) {
                Storage::disk('s3')->delete(
                    ltrim(parse_url($this->core_team[$this->editTeamIndex]['image'], PHP_URL_PATH), '/')
                );
            }
            $path = $this->newTeamMemberImage->store('team-members', 's3');
            Storage::disk('s3')->setVisibility($path, 'public');
            $member['image'] = Storage::disk('s3')->url($path);
        } elseif ($this->editTeamIndex !== null) {
            // Keep existing image
            $member['image'] = $this->core_team[$this->editTeamIndex]['image'] ?? null;
        }

        if ($this->editTeamIndex !== null) {
            $this->core_team[$this->editTeamIndex] = $member;
        } else {
            $this->core_team[] = $member;
        }

        $this->closeTeamModal();
        $this->notification()->success($this->editTeamIndex !== null ? 'Team member updated!' : 'Team member added!');
    }

    public function removeTeamMember($index): void
    {
        $this->pendingDeleteTeamIndex = $index;
    }

    public function executeRemoveTeamMember(): void
    {
        if ($this->pendingDeleteTeamIndex !== null) {
            if (!empty($this->core_team[$this->pendingDeleteTeamIndex]['image'])) {
                Storage::disk('s3')->delete(
                    ltrim(parse_url($this->core_team[$this->pendingDeleteTeamIndex]['image'], PHP_URL_PATH), '/')
                );
            }
            unset($this->core_team[$this->pendingDeleteTeamIndex]);
            $this->core_team = array_values($this->core_team);
            $this->notification()->success('Team member removed!');
        }
        $this->pendingDeleteTeamIndex = null;
    }

    public function cancelRemoveTeamMember(): void
    {
        $this->pendingDeleteTeamIndex = null;
    }

    // ─── Social Media ─────────────────────────────────────────────────────────

    public function openSocialModal($index = null): void
    {
        $this->editSocialIndex   = $index;
        $this->newSocialMedia    = $index !== null
            ? $this->social_media[$index]
            : ['platform' => '', 'url' => ''];
        $this->newSocialMediaIcon = null;
        $this->showSocialModal   = true;
    }

    public function closeSocialModal(): void
    {
        $this->showSocialModal   = false;
        $this->editSocialIndex   = null;
        $this->newSocialMedia    = [];
        $this->newSocialMediaIcon = null;
    }

    public function saveSocialMedia(): void
    {
        $this->validate([
            'newSocialMedia.platform' => 'required|string|max:100',
            'newSocialMedia.url'      => 'required|url|max:255',
            'newSocialMediaIcon'      => 'nullable|image|max:1024',
        ]);

        $social = $this->newSocialMedia;

        if ($this->newSocialMediaIcon) {
            $path = $this->newSocialMediaIcon->store('social-media/icons', 's3');
            Storage::disk('s3')->setVisibility($path, 'public');
            $social['icon'] = Storage::disk('s3')->url($path);
        } elseif ($this->editSocialIndex !== null) {
            $social['icon'] = $this->social_media[$this->editSocialIndex]['icon'] ?? null;
        }

        if ($this->editSocialIndex !== null) {
            $this->social_media[$this->editSocialIndex] = $social;
        } else {
            $this->social_media[] = $social;
        }

        $this->closeSocialModal();
        $this->notification()->success($this->editSocialIndex !== null ? 'Social media updated!' : 'Social media added!');
    }

    public function removeSocialMedia($index): void
    {
        $this->pendingDeleteSocialIndex = $index;
    }

    public function executeRemoveSocialMedia(): void
    {
        if ($this->pendingDeleteSocialIndex !== null) {
            unset($this->social_media[$this->pendingDeleteSocialIndex]);
            $this->social_media = array_values($this->social_media);
            $this->notification()->success('Social media removed!');
        }
        $this->pendingDeleteSocialIndex = null;
    }

    public function cancelRemoveSocialMedia(): void
    {
        $this->pendingDeleteSocialIndex = null;
    }

    // ─── Save ─────────────────────────────────────────────────────────────────

    public function save(): void
    {
        $this->validate([
            'heading'     => 'required|string|max:255',
            'sub_heading' => 'nullable|string|max:500',
            'logo'        => 'nullable|image|max:2048',
            'address'     => 'nullable|string|max:500',
        ]);

        try {
            $data = [
                'heading'         => $this->heading,
                'sub_heading'     => $this->sub_heading,
                'content'         => $this->content,
                'contact_details' => $this->contact_details,
                'address'         => $this->address,
                'core_team'       => $this->core_team,
                'social_media'    => $this->social_media,
            ];

            if ($this->logo) {
                if ($this->aboutApp?->logo) {
                    Storage::disk('s3')->delete(
                        ltrim(parse_url($this->aboutApp->logo, PHP_URL_PATH), '/')
                    );
                }
                $path = $this->logo->store('logos', 's3');
                Storage::disk('s3')->setVisibility($path, 'public');
                $data['logo']     = Storage::disk('s3')->url($path);
                $this->logoPreview = $data['logo'];
            }

            if ($this->aboutApp) {
                $this->aboutApp->update($data);
            } else {
                $this->aboutApp = AboutAppModel::create($data);
            }

            $this->notification()->success('About App saved successfully!');
            $this->activeTab = 'view';
        } catch (\Exception $e) {
            $this->notification()->error('Error saving: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.super-admin.about-app');
    }
}
