<?php

namespace App\Livewire\Marks;

use App\Models\AuditVisit;
use App\Models\Mark;
use App\Models\MarkStage;
use App\Models\Template;
use Livewire\Component;
use Livewire\WithFileUploads;

class MarkList extends Component
{
    use WithFileUploads;

    public $selectedVisitId;
    public $showMarkModal = false;
    public $showStageModal = false;
    public $selectedMarkId;
    public $photos = [];
    
    public $mark = [
        'criteria_group_code' => '',
        'criterion_code' => '',
        'description' => ''
    ];
    
    public $stage = [
        'status' => '',
        'fixation_date' => '',
        'regulation_date' => '',
        'state' => ''
    ];

    protected $rules = [
        'mark.criteria_group_code' => 'required|exists:criteria_groups,code',
        'mark.criterion_code' => 'required|exists:criteria,code',
        'mark.description' => 'nullable',
        
        'stage.status' => 'required|in:corresponds,partially,not_corresponds,needs_clarification,not_applicable',
        'stage.fixation_date' => 'required|date',
        'stage.regulation_date' => 'nullable|date',
        'stage.state' => 'required',
        'photos.*' => 'image|max:10240' // максимум 10MB на фото
    ];

    public function mount()
    {
        $this->stage['fixation_date'] = now()->format('Y-m-d\TH:i');
    }

    public function updatedSelectedVisitId($value)
    {
        $this->reset(['mark', 'stage', 'photos']);
        if ($value) {
            $visit = AuditVisit::with(['audit.template.criteriaGroups.criteria'])->find($value);
            if ($visit) {
                $this->stage['fixation_date'] = $visit->visit_date->format('Y-m-d\TH:i');
            }
        }
    }

    public function openMarkModal($groupCode, $criterionCode)
    {
        $this->mark = [
            'criteria_group_code' => $groupCode,
            'criterion_code' => $criterionCode,
            'description' => ''
        ];
        $this->showMarkModal = true;
    }

    public function createMark()
    {
        $this->validate([
            'mark.criteria_group_code' => 'required|exists:criteria_groups,code',
            'mark.criterion_code' => 'required|exists:criteria,code',
            'mark.description' => 'nullable'
        ]);

        $mark = Mark::create([
            ...$this->mark,
            'audit_visit_id' => $this->selectedVisitId
        ]);
        
        $this->reset('mark');
        $this->showMarkModal = false;
        $this->dispatch('mark-created');
        session()->flash('message', 'Отметка успешно создана');
    }

    public function createStage()
    {
        $this->validate([
            'stage.status' => 'required|in:corresponds,partially,not_corresponds,needs_clarification,not_applicable',
            'stage.fixation_date' => 'required|date',
            'stage.regulation_date' => 'nullable|date',
            'stage.state' => 'required',
            'photos.*' => 'image|max:10240'
        ]);

        $stage = MarkStage::create([
            ...$this->stage,
            'mark_id' => $this->selectedMarkId
        ]);

        // Сохраняем фотографии
        foreach ($this->photos as $photo) {
            $path = $photo->store('mark-photos', 'public');
            $stage->photos()->create(['path' => $path]);
        }
        
        $this->reset(['stage', 'photos']);
        $this->showStageModal = false;
        session()->flash('message', 'Этап успешно создан');
    }

    public function openStageModal($markId)
    {
        $this->selectedMarkId = $markId;
        $this->showStageModal = true;
    }

    public function render()
    {
        $visits = AuditVisit::whereHas('responsibleUsers', function ($query) {
            $query->where('users.id', auth()->id());
        })
        ->with(['audit.organization', 'audit.branch', 'audit.template.criteriaGroups.criteria'])
        ->get();

        $selectedVisit = $this->selectedVisitId ? $visits->find($this->selectedVisitId) : null;
        $template = $selectedVisit?->audit?->template;

        return view('livewire.marks.mark-list', [
            'visits' => $visits,
            'template' => $template,
            'marks' => $selectedVisit ? $selectedVisit->marks()->with('stages.photos')->get() : collect()
        ]);
    }
} 