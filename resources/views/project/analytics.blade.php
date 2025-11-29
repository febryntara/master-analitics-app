<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Project Analytics') }} |
            </h2>

            <x-nav-link :href="route('projects.index')">
                {{ __('Back') }}
            </x-nav-link>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="container p-6 text-gray-900">

                    <h2 class="mb-4 text-2xl font-semibold">
                        Analytics Report — {{ $project->name }}
                    </h2>

                    {{-- TABS --}}
                    <div x-data="{ tab: 'overview' }">

                        {{-- TAB HEADER --}}
                        <div class="flex border-b border-gray-200">
                            <button class="px-4 py-2 mr-4 -mb-px text-sm font-medium border-b-2"
                                :class="tab === 'overview' ? 'border-indigo-600 text-indigo-600' :
                                    'border-transparent text-gray-600'"
                                @click="tab = 'overview'">
                                Overview
                            </button>

                            <button class="px-4 py-2 mr-4 -mb-px text-sm font-medium border-b-2"
                                :class="tab === 'words' ? 'border-indigo-600 text-indigo-600' :
                                    'border-transparent text-gray-600'"
                                @click="tab = 'words'">
                                Words & Wordcloud
                            </button>

                            <button class="px-4 py-2 mr-4 -mb-px text-sm font-medium border-b-2"
                                :class="tab === 'api' ? 'border-indigo-600 text-indigo-600' :
                                    'border-transparent text-gray-600'"
                                @click="tab = 'api'">
                                API Performance
                            </button>

                            <button class="px-4 py-2 mr-4 -mb-px text-sm font-medium border-b-2"
                                :class="tab === 'errors' ? 'border-indigo-600 text-indigo-600' :
                                    'border-transparent text-gray-600'"
                                @click="tab = 'errors'">
                                Errors
                            </button>

                            <button class="px-4 py-2 mr-4 -mb-px text-sm font-medium border-b-2"
                                :class="tab === 'samples' ? 'border-indigo-600 text-indigo-600' :
                                    'border-transparent text-gray-600'"
                                @click="tab = 'samples'">
                                Samples
                            </button>
                        </div>

                        {{-- TAB PANELS --}}
                        <div class="mt-6">
                            <div x-show="tab === 'overview'">
                                @include('project.partials.analytics.overview')
                            </div>

                            <div x-show="tab === 'words'">
                                @include('project.partials.analytics.word_n_worldcloud')
                            </div>

                            <div x-show="tab === 'api'">
                                @include('project.partials.analytics.api_performance')
                            </div>

                            <div x-show="tab === 'errors'">
                                @include('project.partials.analytics.errors')
                            </div>

                            <div x-show="tab === 'samples'">
                                @include('project.partials.analytics.samples')
                            </div>
                        </div>

                    </div>
                    {{-- END TABS --}}

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
