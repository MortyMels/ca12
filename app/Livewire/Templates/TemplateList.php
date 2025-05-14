<?php

namespace App\Livewire\Templates;

use App\Models\Template;
use App\Models\CriteriaGroup;
use App\Models\Criterion;
use Livewire\Component;
use Livewire\WithPagination;

class TemplateList extends Component
{
    use WithPagination;

    public $showTemplateModal = false;
    public $showGroupModal = false;
    public $showCriterionModal = false;
    
    public $templateId;
    public $groupId;
    
    public $template = [
        'code' => '',
        'name' => '',
        'description' => ''
    ];
    
    public $group = [
        'code' => '',
        'name' => ''
    ];
    
    public $criterion = [
        'code' => '',
        'name' => '',
        'description' => ''
    ];

    protected $rules = [
        'template.code' => 'required|min:3|unique:templates,code',
        'template.name' => 'required|min:3',
        'template.description' => 'nullable',
        
        'group.code' => 'required|min:3',
        'group.name' => 'required|min:3',
        
        'criterion.code' => 'required|min:3',
        'criterion.name' => 'required|min:3',
        'criterion.description' => 'nullable'
    ];

    public function createTemplate()
    {
        $this->validate([
            'template.code' => 'required|min:3|unique:templates,code',
            'template.name' => 'required|min:3',
            'template.description' => 'nullable'
        ]);

        Template::create($this->template);
        
        $this->reset('template');
        $this->showTemplateModal = false;
        session()->flash('message', 'Шаблон успешно создан');
    }

    public function createGroup()
    {
        $this->validate([
            'group.code' => 'required|min:3',
            'group.name' => 'required|min:3'
        ]);

        CriteriaGroup::create([
            ...$this->group,
            'template_id' => $this->templateId
        ]);
        
        $this->reset('group');
        $this->showGroupModal = false;
        session()->flash('message', 'Группа критериев успешно создана');
    }

    public function createCriterion()
    {
        $this->validate([
            'criterion.code' => 'required|min:3',
            'criterion.name' => 'required|min:3',
            'criterion.description' => 'nullable'
        ]);

        Criterion::create([
            ...$this->criterion,
            'criteria_group_id' => $this->groupId
        ]);
        
        $this->reset('criterion');
        $this->showCriterionModal = false;
        session()->flash('message', 'Критерий успешно создан');
    }

    public function openGroupModal($templateId)
    {
        $this->templateId = $templateId;
        $this->showGroupModal = true;
    }

    public function openCriterionModal($groupId)
    {
        $this->groupId = $groupId;
        $this->showCriterionModal = true;
    }

    public function render()
    {
        return view('livewire.templates.template-list', [
            'templates' => Template::with(['criteriaGroups.criteria'])->paginate(10)
        ]);
    }
} 