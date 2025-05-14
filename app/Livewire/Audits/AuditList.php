<?php

namespace App\Livewire\Audits;

use App\Models\Audit;
use App\Models\AuditVisit;
use App\Models\Branch;
use App\Models\Organization;
use App\Models\Template;
use App\Models\User;
use Illuminate\Support\Collection;
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
        'status' => '',
        'notes' => ''
    ];
    
    public $visit = [
        'visit_date' => '',
        'type' => '',
        'branch_id' => '',
        'responsible_user_ids' => [],
        'notes' => ''
    ];

    public Collection $availableBranches;

    protected $rules = [
        'audit.type' => 'required|in:planned,unplanned',
        'audit.template_id' => 'required|exists:templates,id',
        'audit.organization_id' => 'required|exists:organizations,id',
        'audit.status' => 'required|in:planned,in_progress,completed',
        'audit.notes' => 'nullable',
        
        'visit.visit_date' => 'required|date',
        'visit.type' => 'required|in:primary,repeat',
        'visit.branch_id' => 'required|exists:branches,id',
        'visit.responsible_user_ids' => 'required|array|min:1',
        'visit.responsible_user_ids.*' => 'exists:users,id',
        'visit.notes' => 'nullable'
    ];

    public function mount()
    {
        $this->availableBranches = collect();
    }

    public function updatedAuditOrganizationId($value)
    {
        if ($value) {
            $this->availableBranches = Branch::where('organization_id', $value)->get();
        } else {
            $this->availableBranches = collect();
        }
        $this->visit['branch_id'] = '';
    }

    public function createAudit()
    {
        $this->validate([
            'audit.type' => 'required|in:planned,unplanned',
            'audit.template_id' => 'required|exists:templates,id',
            'audit.organization_id' => 'required|exists:organizations,id',
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
            'visit.branch_id' => [
                'required',
                'exists:branches,id',
                function ($attribute, $value, $fail) {
                    $audit = Audit::find($this->auditId);
                    $branch = Branch::find($value);
                    
                    if ($branch && $branch->organization_id !== $audit->organization_id) {
                        $fail('Выбранный филиал не принадлежит организации аудита.');
                    }
                },
            ],
            'visit.responsible_user_ids' => 'required|array|min:1',
            'visit.responsible_user_ids.*' => 'exists:users,id',
            'visit.notes' => 'nullable'
        ]);

        $visit = AuditVisit::create([
            'audit_id' => $this->auditId,
            'branch_id' => $this->visit['branch_id'],
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
        $audit = Audit::find($auditId);
        $this->availableBranches = Branch::where('organization_id', $audit->organization_id)->get();
        $this->showVisitModal = true;
    }

    public function render()
    {
        return view('livewire.audits.audit-list', [
            'audits' => Audit::with(['template', 'organization', 'visits.responsibleUsers', 'visits.branch'])->paginate(10),
            'templates' => Template::all(),
            'organizations' => Organization::all(),
            'users' => User::all()
        ]);
    }
} 