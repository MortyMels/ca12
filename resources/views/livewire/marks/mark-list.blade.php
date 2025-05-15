<div class="p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-semibold mb-4">Отметки</h2>
        
        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('message') }}</span>
            </div>
        @endif
        
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="visit">
                Выберите выезд
            </label>
            <select wire:model.live="selectedVisitId" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="visit">
                <option value="">Выберите выезд</option>
                @foreach($visits as $visit)
                    <option value="{{ $visit->id }}">
                        {{ $visit->audit->organization->name }}
                        @if($visit->audit->branch)
                            - {{ $visit->audit->branch->name }}
                        @endif
                        ({{ $visit->visit_date->format('d.m.Y H:i') }})
                    </option>
                @endforeach
            </select>
        </div>

        @if($selectedVisitId)
            <div class="grid grid-cols-2 gap-6">
                <!-- Список критериев -->
                <div class="bg-white shadow-md rounded-lg p-4">
                    <h3 class="text-lg font-semibold mb-4">Критерии</h3>
                    @if($template && $template->criteriaGroups->isNotEmpty())
                        @foreach($template->criteriaGroups as $group)
                            <div class="mb-6">
                                <h4 class="font-medium text-gray-700 mb-2">{{ $group->name }}</h4>
                                <div class="space-y-2">
                                    @foreach($group->criteria as $criterion)
                                        <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                                            <div>
                                                <span class="font-medium">{{ $criterion->name }}</span>
                                                <p class="text-sm text-gray-600">{{ $criterion->description }}</p>
                                            </div>
                                            <button 
                                                wire:click="openMarkModal('{{ $group->code }}', '{{ $criterion->code }}')"
                                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                                            >
                                                Создать отметку
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-gray-600">Шаблон не найден или не содержит групп критериев</p>
                    @endif
                </div>

                <!-- Список отметок -->
                <div class="bg-white shadow-md rounded-lg p-4">
                    <h3 class="text-lg font-semibold mb-4">Созданные отметки</h3>
                    @if($marks->count() > 0)
                        <div class="space-y-4">
                            @foreach($marks as $mark)
                                <div class="border rounded p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <p class="font-medium">Группа: {{ $mark->criteria_group_code }}</p>
                                            <p class="font-medium">Критерий: {{ $mark->criterion_code }}</p>
                                            @if($mark->description)
                                                <p class="text-gray-600 mt-1">{{ $mark->description }}</p>
                                            @endif
                                        </div>
                                        <button 
                                            wire:click="openStageModal({{ $mark->id }})"
                                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
                                        >
                                            Добавить этап
                                        </button>
                                    </div>

                                    @if($mark->stages->count() > 0)
                                        <div class="mt-4 space-y-4">
                                            @foreach($mark->stages as $stage)
                                                <div class="bg-gray-50 p-3 rounded">
                                                    <div class="flex items-center gap-2 mb-2">
                                                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                                            {{ match($stage->status) {
                                                                'corresponds' => 'bg-green-100 text-green-800',
                                                                'partially' => 'bg-yellow-100 text-yellow-800',
                                                                'not_corresponds' => 'bg-red-100 text-red-800',
                                                                'needs_clarification' => 'bg-blue-100 text-blue-800',
                                                                'not_applicable' => 'bg-gray-100 text-gray-800',
                                                                default => 'bg-gray-100 text-gray-800'
                                                            } }}">
                                                            {{ $stage->status_label }}
                                                        </span>
                                                        <span class="text-sm text-gray-600">
                                                            {{ $stage->fixation_date->format('d.m.Y H:i') }}
                                                        </span>
                                                    </div>
                                                    <p class="text-gray-700">{{ $stage->state }}</p>
                                                    @if($stage->regulation_date)
                                                        <p class="text-sm text-gray-600 mt-1">
                                                            Регламентная дата: {{ $stage->regulation_date->format('d.m.Y') }}
                                                        </p>
                                                    @endif
                                                    @if($stage->photos->count() > 0)
                                                        <div class="mt-2 grid grid-cols-3 gap-2">
                                                            @foreach($stage->photos as $photo)
                                                                <img src="{{ Storage::url($photo->path) }}" alt="Фото" class="w-full h-24 object-cover rounded">
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600">Нет созданных отметок</p>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Модальное окно создания отметки -->
    <div x-data="{ show: @entangle('showMarkModal') }" x-show="show" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form wire:submit.prevent="createMark">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Группа критериев
                            </label>
                            <input type="text" wire:model="mark.criteria_group_code" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" readonly>
                            @error('mark.criteria_group_code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Критерий
                            </label>
                            <input type="text" wire:model="mark.criterion_code" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" readonly>
                            @error('mark.criterion_code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Описание
                            </label>
                            <textarea wire:model="mark.description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" rows="3"></textarea>
                            @error('mark.description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Создать
                        </button>
                        <button type="button" wire:click="$set('showMarkModal', false)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Отмена
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Модальное окно создания этапа -->
    <div x-data="{ show: @entangle('showStageModal') }" x-show="show" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form wire:submit.prevent="createStage">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="status">
                                Статус *
                            </label>
                            <select wire:model="stage.status" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="status">
                                <option value="">Выберите статус</option>
                                <option value="corresponds">Соответствует</option>
                                <option value="partially">Частично соответствует</option>
                                <option value="not_corresponds">Не соответствует</option>
                                <option value="needs_clarification">Нужно уточнение</option>
                                <option value="not_applicable">Неприменимо</option>
                            </select>
                            @error('stage.status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="fixation_date">
                                Дата фиксации *
                            </label>
                            <input type="datetime-local" wire:model="stage.fixation_date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="fixation_date">
                            @error('stage.fixation_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="regulation_date">
                                Регламентная дата устранения
                            </label>
                            <input type="date" wire:model="stage.regulation_date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="regulation_date">
                            @error('stage.regulation_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="state">
                                Состояние *
                            </label>
                            <textarea wire:model="stage.state" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="state" rows="3"></textarea>
                            @error('stage.state') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="photos">
                                Фотографии
                            </label>
                            <input type="file" wire:model="photos" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="photos" multiple>
                            @error('photos.*') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            <p class="text-gray-500 text-xs mt-1">Можно выбрать несколько файлов. Максимальный размер каждого файла - 10MB</p>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Создать
                        </button>
                        <button type="button" wire:click="$set('showStageModal', false)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Отмена
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> 