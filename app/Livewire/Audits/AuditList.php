<?php

namespace App\Livewire\Audits;

use App\Models\Audit;
use App\Models\AuditVisit;
use App\Models\Organization;
use App\Models\Template;
use App\Models\User;
use App\Models\Branch;
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
        'organization_id' => '',
        'branch_id' => '',
        'status' => '',
        'notes' => ''
    ];
    
    public $visit = [
        'visit_date' => '',
        'type' => '',
        'responsible_user_ids' => [],
        'notes' => ''
    ];

    protected $rules = [
        'audit.type' => 'required|in:planned,unplanned',
        'audit.template_id' => 'required|exists:templates,id',
        'audit.organization_id' => 'required|exists:organizations,id',
        'audit.branch_id' => 'nullable|exists:branches,id',
        'audit.status' => 'required|in:planned,in_progress,completed',
        'audit.notes' => 'nullable',
        
        'visit.visit_date' => 'required|date',
        'visit.type' => 'required|in:primary,repeat',
        'visit.responsible_user_ids' => 'required|array|min:1',
        'visit.responsible_user_ids.*' => 'exists:users,id',
        'visit.notes' => 'nullable'
    ];

    public function createAudit()
    {
        $this->validate([
            'audit.type' => 'required|in:planned,unplanned',
            'audit.template_id' => 'required|exists:templates,id',
            'audit.organization_id' => 'required|exists:organizations,id',
            'audit.branch_id' => 'nullable|exists:branches,id',
            'audit.status' => 'required|in:planned,in_progress,completed',
            'audit.notes' => 'nullable'
        ]);

        // Проверяем, что выбранный филиал принадлежит выбранной организации
        if ($this->audit['branch_id']) {
            $branch = Branch::find($this->audit['branch_id']);
            if ($branch->organization_id !== (int)$this->audit['organization_id']) {
                $this->addError('audit.branch_id', 'Выбранный филиал не принадлежит выбранной организации');
                return;
            }
        }

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
            'visit.responsible_user_ids' => 'required|array|min:1',
            'visit.responsible_user_ids.*' => 'exists:users,id',
            'visit.notes' => 'nullable'
        ]);

        $visit = AuditVisit::create([
            'audit_id' => $this->auditId,
            'visit_date' => $this->visit['visit_date'],
            'type' => $this->visit['type'],
            'notes' => $this->visit['notes']
        ]);

        $visit->responsibleUsers()->attach($this->visit['responsible_user_ids']);
        
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
            'audits' => Audit::with(['template', 'organization', 'branch', 'visits.responsibleUsers'])->paginate(10),
            'templates' => Template::all(),
            'organizations' => Organization::with('branches')->get(),
            'users' => User::all()
        ]);
    }
} 