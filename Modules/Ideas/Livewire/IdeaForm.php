<?php

declare(strict_types=1);

namespace Modules\Ideas\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Modules\Ideas\Models\AppIdea;

class IdeaForm extends Component
{
    public ?AppIdea $idea = null;

    public string $name = '';

    public ?string $tagline = null;

    public ?string $description = null;

    public string $features = '';

    public string $techStack = '';

    public string $platform = 'web';

    public string $status = 'draft';

    public string $priority = 'medium';

    public string $tags = '';

    public ?string $notes = null;

    public bool $isEditing = false;

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'features' => 'nullable|string',
            'techStack' => 'nullable|string',
            'platform' => 'required|in:web,mobile,desktop,hybrid',
            'status' => 'required|in:draft,research,development,archived',
            'priority' => 'required|in:low,medium,high',
            'tags' => 'nullable|string',
            'notes' => 'nullable|string',
        ];
    }

    public function mount(?AppIdea $idea = null): void
    {
        $this->idea = $idea;

        if ($idea && $idea->exists) {
            $this->isEditing = true;
            $this->name = $idea->name;
            $this->tagline = $idea->tagline;
            $this->description = $idea->description;
            $this->features = is_array($idea->features) ? implode("\n", $idea->features) : '';
            $this->techStack = is_array($idea->tech_stack) ? implode("\n", $idea->tech_stack) : '';
            $this->platform = $idea->platform;
            $this->status = $idea->status;
            $this->priority = $idea->priority;
            $this->tags = is_array($idea->tags) ? implode(', ', $idea->tags) : '';
            $this->notes = $idea->notes;
        }
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'user_id' => auth()->id(),
            'name' => $this->name,
            'tagline' => $this->tagline,
            'description' => $this->description,
            'features' => $this->features ? array_filter(array_map('trim', explode("\n", $this->features))) : null,
            'tech_stack' => $this->techStack ? array_filter(array_map('trim', explode("\n", $this->techStack))) : null,
            'platform' => $this->platform,
            'status' => $this->status,
            'priority' => $this->priority,
            'tags' => $this->tags ? array_filter(array_map('trim', explode(',', $this->tags))) : null,
            'notes' => $this->notes,
        ];

        if ($this->isEditing && $this->idea) {
            $this->idea->update($data);
        } else {
            AppIdea::create($data);
        }

        $this->dispatch('idea-saved');
        $this->redirect(route('ideas.index'));
    }

    public function render(): View
    {
        return view('ideas::livewire.idea-form');
    }
}
