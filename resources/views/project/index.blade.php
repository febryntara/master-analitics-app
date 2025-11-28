<x-app-layout>
    <x-slot name="header">
        <div class="flex gap-2 items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Projects') }} |
            </h2>

            <x-nav-link :href="route('projects.create')" :active="request()->routeIs('projects.create')">
                {{ __('Insert Project') }}
            </x-nav-link>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 flex items-center gap-2">
                    @foreach ($projects as $project)
                        <x-nav-link
                            :href="route('projects.show', ['project' => $project])">{{ $project->name . ($projects->count() != $loop->iteration ? ',' : null) }}</x-nav-link>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
