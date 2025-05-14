<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">Шаблоны</h2>
        <button wire:click="$set('showTemplateModal', true)" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Создать шаблон
        </button>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        @foreach($templates as $template)
            <div class="border-b p-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold">{{ $template->name }}</h3>
                        <p class="text-gray-600">Код: {{ $template->code }}</p>
                        @if($template->description)
                            <p class="text-gray-600 mt-1">{{ $template->description }}</p>
                        @endif
                    </div>
                    <button wire:click="openGroupModal({{ $template->id }})" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Добавить группу
                    </button>
                </div>

                @if($template->criteriaGroups->count() > 0)
                    <div class="mt-4 ml-4">
                        @foreach($template->criteriaGroups as $group)
                            <div class="border-l-2 border-gray-200 pl-4 mb-4">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h4 class="font-medium">{{ $group->name }}</h4>
                                        <p class="text-gray-600">Код: {{ $group->code }}</p>
                                    </div>
                                    <button wire:click="openCriterionModal({{ $group->id }})" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                        Добавить критерий
                                    </button>
                                </div>

                                @if($group->criteria->count() > 0)
                                    <div class="mt-2 ml-4">
                                        @foreach($group->criteria as $criterion)
                                            <div class="border-l-2 border-gray-200 pl-4 mb-2">
                                                <h5 class="font-medium">{{ $criterion->name }}</h5>
                                                <p class="text-gray-600">Код: {{ $criterion->code }}</p>
                                                @if($criterion->description)
                                                    <p class="text-gray-600 mt-1">{{ $criterion->description }}</p>
                                                @endif
                                            </div>
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

    <div class="mt-4">
        {{ $templates->links() }}
    </div>

    <!-- Модальное окно создания шаблона -->
    <div x-data="{ show: @entangle('showTemplateModal') }" x-show="show" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form wire:submit.prevent="createTemplate">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="code">
                                Код
                            </label>
                            <input wire:model="template.code" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="code" type="text">
                            @error('template.code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                                Наименование
                            </label>
                            <input wire:model="template.name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="name" type="text">
                            @error('template.name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                                Описание
                            </label>
                            <textarea wire:model="template.description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="description" rows="3"></textarea>
                            @error('template.description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Создать
                        </button>
                        <button type="button" wire:click="$set('showTemplateModal', false)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Отмена
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Модальное окно создания группы -->
    <div x-data="{ show: @entangle('showGroupModal') }" x-show="show" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form wire:submit.prevent="createGroup">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="group_code">
                                Код
                            </label>
                            <input wire:model="group.code" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="group_code" type="text">
                            @error('group.code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="group_name">
                                Наименование
                            </label>
                            <input wire:model="group.name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="group_name" type="text">
                            @error('group.name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Создать
                        </button>
                        <button type="button" wire:click="$set('showGroupModal', false)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Отмена
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Модальное окно создания критерия -->
    <div x-data="{ show: @entangle('showCriterionModal') }" x-show="show" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form wire:submit.prevent="createCriterion">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="criterion_code">
                                Код
                            </label>
                            <input wire:model="criterion.code" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="criterion_code" type="text">
                            @error('criterion.code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="criterion_name">
                                Наименование
                            </label>
                            <input wire:model="criterion.name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="criterion_name" type="text">
                            @error('criterion.name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="criterion_description">
                                Описание
                            </label>
                            <textarea wire:model="criterion.description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="criterion_description" rows="3"></textarea>
                            @error('criterion.description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-yellow-600 text-base font-medium text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Создать
                        </button>
                        <button type="button" wire:click="$set('showCriterionModal', false)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Отмена
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> 