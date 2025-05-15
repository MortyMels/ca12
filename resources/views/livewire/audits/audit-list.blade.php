<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">Аудиты</h2>
        <button wire:click="$set('showAuditModal', true)" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Создать аудит
        </button>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        @foreach($audits as $audit)
            <div class="border-b p-4">
                <div class="flex justify-between items-center">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg font-semibold">{{ $audit->template->name }}</h3>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $audit->type === 'planned' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $audit->type_label }}
                            </span>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                {{ match($audit->status) {
                                    'planned' => 'bg-gray-100 text-gray-800',
                                    'in_progress' => 'bg-green-100 text-green-800',
                                    'completed' => 'bg-purple-100 text-purple-800',
                                    default => 'bg-gray-100 text-gray-800'
                                } }}">
                                {{ $audit->status_label }}
                            </span>
                        </div>
                        <p class="text-gray-600 mt-1">Организация: {{ $audit->organization->name }}</p>
                        @if($audit->branch)
                            <p class="text-gray-600">Филиал: {{ $audit->branch->name }}</p>
                        @endif
                        @if($audit->notes)
                            <p class="text-gray-600 mt-1">{{ $audit->notes }}</p>
                        @endif
                    </div>
                    <button wire:click="openVisitModal({{ $audit->id }})" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Добавить выезд
                    </button>
                </div>

                @if($audit->visits->count() > 0)
                    <div class="mt-4 ml-4">
                        <h4 class="font-medium text-gray-700 mb-2">Выезды:</h4>
                        <div class="space-y-4">
                            @foreach($audit->visits as $visit)
                                <div class="border rounded p-3 bg-gray-50">
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium">{{ $visit->visit_date->format('d.m.Y H:i') }}</span>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $visit->type === 'primary' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ $visit->type_label }}
                                        </span>
                                    </div>
                                    <div class="text-gray-600 mt-1">
                                        <p class="font-medium">Ответственные:</p>
                                        <ul class="list-disc list-inside mt-1">
                                            @foreach($visit->responsibleUsers as $user)
                                                <li>{{ $user->name }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @if($visit->notes)
                                        <p class="text-gray-600 mt-1">{{ $visit->notes }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $audits->links() }}
    </div>

    <!-- Модальное окно создания аудита -->
    <div x-data="{ show: @entangle('showAuditModal') }" x-show="show" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form wire:submit.prevent="createAudit">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="type">
                                Тип аудита *
                            </label>
                            <select wire:model="audit.type" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="type">
                                <option value="">Выберите тип</option>
                                <option value="planned">Плановый</option>
                                <option value="unplanned">Внеплановый</option>
                            </select>
                            @error('audit.type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="template_id">
                                Шаблон *
                            </label>
                            <select wire:model="audit.template_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="template_id">
                                <option value="">Выберите шаблон</option>
                                @foreach($templates as $template)
                                    <option value="{{ $template->id }}">{{ $template->name }}</option>
                                @endforeach
                            </select>
                            @error('audit.template_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="organization">
                                Организация *
                            </label>
                            <select wire:model="audit.organization_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="organization">
                                <option value="">Выберите организацию</option>
                                @foreach($organizations as $organization)
                                    <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                                @endforeach
                            </select>
                            @error('audit.organization_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="branch">
                                Филиал
                            </label>
                            <select wire:model="audit.branch_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="branch">
                                <option value="">Выберите филиал</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                            @error('audit.branch_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="status">
                                Статус *
                            </label>
                            <select wire:model="audit.status" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="status">
                                <option value="">Выберите статус</option>
                                <option value="planned">Запланирован</option>
                                <option value="in_progress">Проводится</option>
                                <option value="completed">Завершен</option>
                            </select>
                            @error('audit.status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="notes">
                                Примечания
                            </label>
                            <textarea wire:model="audit.notes" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="notes" rows="3"></textarea>
                            @error('audit.notes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Создать
                        </button>
                        <button type="button" wire:click="$set('showAuditModal', false)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Отмена
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Модальное окно создания выезда -->
    <div x-data="{ show: @entangle('showVisitModal') }" x-show="show" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form wire:submit.prevent="createVisit">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="visit_date">
                                Дата выезда *
                            </label>
                            <input wire:model="visit.visit_date" type="datetime-local" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="visit_date">
                            @error('visit.visit_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="visit_type">
                                Тип выезда *
                            </label>
                            <select wire:model="visit.type" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="visit_type">
                                <option value="">Выберите тип</option>
                                <option value="primary">Первичный</option>
                                <option value="repeat">Повторный</option>
                            </select>
                            @error('visit.type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="responsible_user_ids">
                                Ответственные *
                            </label>
                            <select wire:model="visit.responsible_user_ids" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="responsible_user_ids" multiple>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('visit.responsible_user_ids') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            <p class="text-gray-500 text-xs mt-1">Для выбора нескольких ответственных удерживайте Ctrl (Cmd на Mac)</p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="visit_notes">
                                Примечания
                            </label>
                            <textarea wire:model="visit.notes" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="visit_notes" rows="3"></textarea>
                            @error('visit.notes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Создать
                        </button>
                        <button type="button" wire:click="$set('showVisitModal', false)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Отмена
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> 