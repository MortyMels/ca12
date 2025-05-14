<?php

namespace App\Livewire\Organizations;

use App\Models\Organization;
use App\Models\Branch;
use App\Models\Contact;
use Livewire\Component;
use Livewire\WithPagination;

class OrganizationList extends Component
{
    use WithPagination;

    public $showOrganizationModal = false;
    public $showBranchModal = false;
    public $showContactModal = false;
    
    public $organizationId;
    public $branchId;
    public $contactableType;
    public $contactableId;
    
    public $organization = [
        'name' => '',
        'inn' => ''
    ];
    
    public $branch = [
        'name' => '',
        'address' => ''
    ];
    
    public $contact = [
        'last_name' => '',
        'first_name' => '',
        'middle_name' => '',
        'position' => '',
        'phone' => '',
        'email' => '',
        'comment' => ''
    ];

    protected $rules = [
        'organization.name' => 'required|min:3',
        'organization.inn' => 'nullable|min:10|max:12',
        
        'branch.name' => 'required|min:3',
        'branch.address' => 'nullable',
        
        'contact.last_name' => 'required|min:2',
        'contact.first_name' => 'required|min:2',
        'contact.middle_name' => 'nullable',
        'contact.position' => 'nullable',
        'contact.phone' => 'nullable|min:10',
        'contact.email' => 'nullable|email',
        'contact.comment' => 'nullable'
    ];

    public function createOrganization()
    {
        $this->validate([
            'organization.name' => 'required|min:3',
            'organization.inn' => 'nullable|min:10|max:12'
        ]);

        Organization::create($this->organization);
        
        $this->reset('organization');
        $this->showOrganizationModal = false;
        session()->flash('message', 'Организация успешно создана');
    }

    public function createBranch()
    {
        $this->validate([
            'branch.name' => 'required|min:3',
            'branch.address' => 'nullable'
        ]);

        Branch::create([
            ...$this->branch,
            'organization_id' => $this->organizationId
        ]);
        
        $this->reset('branch');
        $this->showBranchModal = false;
        session()->flash('message', 'Филиал успешно создан');
    }

    public function createContact()
    {
        $this->validate([
            'contact.last_name' => 'required|min:2',
            'contact.first_name' => 'required|min:2',
            'contact.middle_name' => 'nullable',
            'contact.phone' => 'nullable|min:10',
            'contact.email' => 'nullable|email',
            'contact.comment' => 'nullable'
        ]);

        $model = $this->contactableType === 'organization' ? Organization::class : Branch::class;
        $contactable = $model::find($this->contactableId);
        
        $contactable->contacts()->create($this->contact);
        
        $this->reset('contact');
        $this->showContactModal = false;
        session()->flash('message', 'Контакт успешно создан');
    }

    public function openBranchModal($organizationId)
    {
        $this->organizationId = $organizationId;
        $this->showBranchModal = true;
    }

    public function openContactModal($type, $id)
    {
        $this->contactableType = $type;
        $this->contactableId = $id;
        $this->showContactModal = true;
    }

    public function render()
    {
        return view('livewire.organizations.organization-list', [
            'organizations' => Organization::with(['branches', 'contacts', 'branches.contacts'])->paginate(10)
        ]);
    }
} 