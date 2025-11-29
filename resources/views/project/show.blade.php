<x-app-layout>
    <x-slot name="header">
        <div class="flex gap-2 items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Project') }} |
            </h2>

            <x-nav-link :href="route('projects.index')">
                {{ __('Back') }}
            </x-nav-link>

            <x-nav-link :href="route('projects.edit', ['project' => $project])">
                {{ __('Edit') }}
            </x-nav-link>

            <x-danger-button x-data=""
                x-on:click.prevent="$dispatch('open-modal', 'confirm-project-deletion')">{{ __('Delete Project') }}</x-danger-button>

            <x-modal name="confirm-project-deletion" :show="$errors->projectDeletion->isNotEmpty()" focusable>
                <form method="post" action="{{ route('projects.destroy', ['project' => $project]) }}" class="p-6">
                    @csrf
                    @method('delete')

                    <h2 class="text-lg font-medium text-gray-900">
                        {{ __('Are you sure you want to delete your project?') }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ __('Once your project is deleted, all of its resources and data will be permanently deleted.') }}
                    </p>

                    <div class="mt-6 flex justify-end">
                        <x-secondary-button x-on:click="$dispatch('close')">
                            {{ __('Cancel') }}
                        </x-secondary-button>

                        <x-danger-button class="ms-3">
                            {{ __('Delete Project') }}
                        </x-danger-button>
                    </div>
                </form>
            </x-modal>
        </div>
    </x-slot>

    {{-- Project Info --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-lg font-medium text-gray-900">{{ __('Project Information') }}</h2>
                    <p class="mt-1 text-sm text-gray-600">{{ __('Detail project data for ' . $project->name) }}</p>

                    <form class="mt-6 space-y-6">
                        @csrf
                        <div>
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                :value="old('name', $project->name)" disabled />
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <x-text-input id="description" name="description" type="text" class="mt-1 block w-full"
                                :value="$project->description ?? 'none'" disabled />
                        </div>

                        <div>
                            <x-input-label for="raw_text_label" :value="__('Raw Text Label')" />
                            <x-text-input id="raw_text_label" name="raw_text_label" type="text"
                                class="mt-1 block w-full" :value="$project->raw_text_label" disabled />
                        </div>

                        <div>
                            <x-input-label for="raw_id_label" :value="__('Raw Id Label')" />
                            <x-text-input id="raw_id_label" name="raw_id_label" type="text" class="mt-1 block w-full"
                                :value="$project->raw_id_label" disabled />
                        </div>

                        <div>
                            <x-input-label for="status" :value="__('Status')" />
                            <x-text-input id="status" name="status" type="text" class="mt-1 block w-full"
                                :value="$project->status" disabled />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Batch Analysis --}}
    <div class="pt-1 pb-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-lg font-medium text-gray-900">Data Count: {{ $projectDataCount }}</h2>
                    <p class="mt-1 text-sm text-gray-600">
                        {{ __('total data for project ' . $project->name) }} <br>
                        {{ __('(minimum 100 data required to analyze)') }}
                    </p>

                    {{-- Jika taskLog ada, tampilkan progress bar --}}

                    @if ($taskLog)
                        <div class="progress mt-2">
                            <div id="progressBar" class="progress-bar" role="progressbar" style="width:0%">0%</div>
                        </div>
                        <p id="progressText">0 / {{ $taskLog->total_rows }}</p>
                    @endif

                    {{-- Tombol Analyze --}}
                    <form method="post" action="{{ route('projects.startProcessing', ['project' => $project]) }}"
                        class="mt-6 space-y-6">
                        @csrf
                        @method('post')

                        @if ($projectDataCount >= 100)
                            <x-primary-button>Analyze</x-primary-button>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Raw JSON Input --}}
    <div class="pt-1 pb-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-lg font-medium text-gray-900">{{ __('Raw File Input') }}</h2>
                    <p class="mt-1 text-sm text-gray-600">{{ __('input project data for ' . $project->name) }}</p>

                    <form method="post" action="{{ route('projects.upload-json', ['project' => $project]) }}"
                        class="mt-6 space-y-6" enctype="multipart/form-data">
                        @csrf
                        @method('post')

                        <div class="w-[50%]">
                            <x-input-label for="data_json_input" :value="__('Data JSON input')" />
                            <x-text-input id="data_json_input" name="data_json_input" type="file"
                                class="mt-1 block w-full" />
                            <x-input-error class="mt-2" :messages="$errors->get('data_json_input')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Upload') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- JS untuk progress realtime --}}
    <script>
        /* <![CDATA[ */
        const projectId = {{ $project->id }};
        let last = 0;

        const src = new EventSource(`/dashboard/projects/${projectId}/progress-sse`);
        src.onmessage = e => {
            const d = JSON.parse(e.data);
            const percent = Math.round((d.processed + d.failed) / d.total * 100);

            document.getElementById('progressBar').style.width = percent + '%';
            document.getElementById('progressBar').textContent = percent + '%';
            document.getElementById('progressText').textContent =
                `${d.processed + d.failed} / ${d.total}`;

            if (d.status === 'completed' || d.status === 'failed') src.close();
        };
        src.onerror = () => src.close();
        /* ]]> */
    </script>
</x-app-layout>
