<x-app-layout>
    <x-slot name="header">
        <div class="flex gap-2 items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Project') }} |
            </h2>

            <x-nav-link :href="route('projects.index')">
                {{ __('Back') }}
            </x-nav-link>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Project Information') }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-600">
                                {{ __('Edit project data for ' . $project->name) }}
                            </p>
                        </header>

                        <form method="post" action="{{ route('projects.update', ['project' => $project]) }}"
                            class="mt-6 space-y-6">
                            @csrf
                            @method('patch')

                            <div>
                                <x-input-label for="name" :value="__('Name')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                    :value="old('name', $project->name)" autofocus autocomplete="name" />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>

                            <div>
                                <x-input-label for="description" :value="__('Description')" />
                                <x-text-input id="description" name="description" type="text"
                                    class="mt-1 block w-full" :value="$project->description ?? 'none'" autofocus autocomplete="description" />
                                <x-input-error class="mt-2" :messages="$errors->get('description')" />
                            </div>

                            <div>
                                <x-input-label for="raw_text_label" :value="__('Raw Text Label')" />
                                <x-text-input id="raw_text_label" name="raw_text_label" type="text"
                                    class="mt-1 block w-full" :value="old('raw_text_label', $project->raw_text_label)" autofocus
                                    autocomplete="raw_text_label" />
                                <x-input-error class="mt-2" :messages="$errors->get('raw_text_label')" />
                                <p class="text-gray-500 text-xs mt-1">Fill with field name that you used for raw text.
                                    Eg: "review_text" if you have "review_text" field on
                                    each
                                    row
                                    data source</p>
                            </div>

                            <div>
                                <x-input-label for="raw_id_label" :value="__('Raw Id Label')" />
                                <x-text-input id="raw_id_label" name="raw_id_label" type="text"
                                    class="mt-1 block w-full" :value="old('raw_id_label', $project->raw_id_label)" autofocus
                                    autocomplete="raw_id_label" />
                                <x-input-error class="mt-2" :messages="$errors->get('raw_id_label')" />
                                <p class="text-gray-500 text-xs mt-1">Fill with field name that you used for raw id.
                                    Eg: "review_id" if you have "review_id" field on
                                    each
                                    row
                                    data source</p>
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>{{ __('Save') }}</x-primary-button>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
