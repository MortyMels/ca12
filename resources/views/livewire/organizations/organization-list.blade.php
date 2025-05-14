<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">Организации</h2>
        <button wire:click="$set('showOrganizationModal', true)" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Создать организацию
        </button>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        @foreach($organizations as $organization)
            <div class="border-b p-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold">{{ $organization->name }}</h3>
                        @if($organization->inn)
                            <p class="text-gray-600">ИНН: {{ $organization->inn }}</p>
                        @endif
                    </div>
                    <div class="flex gap-2">
                        <button wire:click="openContactModal('organization', {{ $organization->id }})" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                            Добавить контакт
                        </button>
                        <button wire:click="openBranchModal({{ $organization->id }})" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Добавить филиал
                        </button>
                    </div>
                </div>

                @if($organization->contacts->count() > 0)
                    <div class="mt-4 ml-4">
                        <h4 class="font-medium text-gray-700 mb-2">Контакты организации:</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($organization->contacts as $contact)
                                <div class="border rounded p-3 bg-gray-50">
                                    <p class="font-medium">{{ $contact->last_name }} {{ $contact->first_name }} {{ $contact->middle_name }}</p>
                                    @if($contact->position)
                                        <p class="text-gray-600">{{ $contact->position }}</p>
                                    @endif
                                    @if($contact->phone)
                                        <p class="text-gray-600">Тел: {{ $contact->phone }}</p>
                                    @endif
                                    @if($contact->email)
                                        <p class="text-gray-600">Email: {{ $contact->email }}</p>
                                    @endif
                                    @if($contact->comment)
                                        <p class="text-gray-600 mt-1">{{ $contact->comment }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($organization->branches->count() > 0)
                    <div class="mt-4 ml-4">
                        @foreach($organization->branches as $branch)
                            <div class="border-l-2 border-gray-200 pl-4 mb-4">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h4 class="font-medium">{{ $branch->name }}</h4>
                                        @if($branch->address)
                                            <p class="text-gray-600">{{ $branch->address }}</p>
                                        @endif
                                    </div>
                                    <button wire:click="openContactModal('branch', {{ $branch->id }})" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                                        Добавить контакт
                                    </button>
                                </div>

                                @if($branch->contacts->count() > 0)
                                    <div class="mt-2 ml-4">
                                        <h5 class="font-medium text-gray-700 mb-2">Контакты филиала:</h5>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                            @foreach($branch->contacts as $contact)
                                                <div class="border rounded p-3 bg-gray-50">
                                                    <p class="font-medium">{{ $contact->last_name }} {{ $contact->first_name }} {{ $contact->middle_name }}</p>
                                                    @if($contact->position)
                                                        <p class="text-gray-600">{{ $contact->position }}</p>
                                                    @endif
                                                    @if($contact->phone)
                                                        <p class="text-gray-600">Тел: {{ $contact->phone }}</p>
                                                    @endif
                                                    @if($contact->email)
                                                        <p class="text-gray-600">Email: {{ $contact->email }}</p>
                                                    @endif
                                                    @if($contact->comment)
                                                        <p class="text-gray-600 mt-1">{{ $contact->comment }}</p>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $organizations->links() }}
    </div>

    <!-- Модальное окно создания организации -->
    <div x-data="{ show: @entangle('showOrganizationModal') }" x-show="show" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form wire:submit.prevent="createOrganization">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                                Наименование *
                            </label>
                            <input wire:model="organization.name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="name" type="text">
                            @error('organization.name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="inn">
                                ИНН
                            </label>
                            <input wire:model="organization.inn" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="inn" type="text">
                            @error('organization.inn') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Создать
                        </button>
                        <button type="button" wire:click="$set('showOrganizationModal', false)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Отмена
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Модальное окно создания филиала -->
    <div x-data="{ show: @entangle('showBranchModal') }" x-show="show" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form wire:submit.prevent="createBranch">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="branch_name">
                                Наименование *
                            </label>
                            <input wire:model="branch.name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="branch_name" type="text">
                            @error('branch.name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="branch_address">
                                Адрес
                            </label>
                            <textarea wire:model="branch.address" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="branch_address" rows="3"></textarea>
                            @error('branch.address') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Создать
                        </button>
                        <button type="button" wire:click="$set('showBranchModal', false)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Отмена
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Модальное окно создания контакта -->
    <div x-data="{ show: @entangle('showContactModal') }" x-show="show" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form wire:submit.prevent="createContact">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="last_name">
                                Фамилия *
                            </label>
                            <input wire:model="contact.last_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="last_name" type="text">
                            @error('contact.last_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="first_name">
                                Имя *
                            </label>
                            <input wire:model="contact.first_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="first_name" type="text">
                            @error('contact.first_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="middle_name">
                                Отчество
                            </label>
                            <input wire:model="contact.middle_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="middle_name" type="text">
                            @error('contact.middle_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="position">
                                Должность
                            </label>
                            <input wire:model="contact.position" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="position" type="text">
                            @error('contact.position') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="phone">
                                Телефон
                            </label>
                            <input wire:model="contact.phone" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="phone" type="text">
                            @error('contact.phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                                Электронная почта
                            </label>
                            <input wire:model="contact.email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="email" type="email">
                            @error('contact.email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="comment">
                                Комментарий
                            </label>
                            <textarea wire:model="contact.comment" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="comment" rows="3"></textarea>
                            @error('contact.comment') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Создать
                        </button>
                        <button type="button" wire:click="$set('showContactModal', false)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Отмена
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> 