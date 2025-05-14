<?php

namespace App\Livewire\Audits;

use App\Models\Audit;
use App\Models\AuditVisit;
use App\Models\Template;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class AuditList extends Component
{
    use WithPagination;

    public $showAuditModal = false;
    public $showVisitModal = false;
    
    public $auditId;
    public $audit = [
        'type' => '',
        'template_id' => '',
        'status' => '',
        'notes' => ''
    ];
    
    public $visit = [
        'visit_date' => '',
        'type' => '',
        'responsible_user_id' => '',
        'notes' => ''
    ];

    protected $rules = [
        'audit.type' => 'required|in:planned,unplanned',
        'audit.template_id' => 'required|exists:templates,id',
        'audit.status' => 'required|in:planned,in_progress,completed',
        'audit.notes' => 'nullable',
        
        'visit.visit_date' => 'required|date',
        'visit.type' => 'required|in:primary,repeat',
        'visit.responsible_user_id' => 'required|exists:users,id',
        'visit.notes' => 'nullable'
    ];

    public function createAudit()
    {
        $this->validate([
            'audit.type' => 'required|in:planned,unplanned',
            'audit.template_id' => 'required|exists:templates,id',
            'audit.status' => 'required|in:planned,in_progress,completed',
            'audit.notes' => 'nullable'
        ]);

        Audit::create($this->audit);
        
        $this->reset('audit');
        $this->showAuditModal = false;
        session()->flash('message', 'Аудит успешно создан');
    }

    public function createVisit()
    {
        $this->validate([
            'visit.visit_date' => 'required|date',
            'visit.type' => 'required|in:primary,repeat',
            'visit.responsible_user_id' => 'required|exists:users,id',
            'visit.notes' => 'nullable'
        ]);

        AuditVisit::create([
            ...$this->visit,
            'audit_id' => $this->auditId
        ]);
        
        $this->reset('visit');
        $this->showVisitModal = false;
        session()->flash('message', 'Выезд успешно создан');
    }

    public function openVisitModal($auditId)
    {
        $this->auditId = $auditId;
        $this->showVisitModal = true;
    }

    public function render()
    {
        return view('livewire.audits.audit-list', [
            'audits' => Audit::with(['template', 'visits.responsibleUser'])->paginate(10),
            'templates' => Template::all(),
            'users' => User::all()
        ]);
    }
} 