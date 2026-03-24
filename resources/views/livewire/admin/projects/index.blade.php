<div class="p-6">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Projects</h1>

        <a href="#" class="bg-black text-white px-4 py-2 rounded-lg">
            + New Project
        </a>
    </div>

    {{-- Filters --}}
    <div class="flex gap-4 mb-4">
        <input type="text" wire:model.live="search" placeholder="Search project..."
            class="border rounded-lg px-3 py-2 w-1/3">

        <select wire:model.live="status" class="border rounded-lg px-3 py-2">
            <option value="">All Status</option>
            @foreach ($statuses as $statusOption)
                <option value="{{ $statusOption->value }}">
                    {{ $statusOption->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Table --}}
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3">Title</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Featured</th>
                    <th class="p-3">Created</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($projects as $project)
                    <tr class="border-t">
                        <td class="p-3">
                            {{ $project->title }}
                        </td>

                        <td class="p-3">
                            {{ $project->status->value }}
                        </td>

                        <td class="p-3">
                            <button wire:click="toggleFeatured({{ $project->id }})"
                                class="px-2 py-1 rounded 
                                {{ $project->featured ? 'bg-green-500 text-white' : 'bg-gray-300' }}">
                                {{ $project->featured ? 'Yes' : 'No' }}
                            </button>
                        </td>

                        <td class="p-3">
                            {{ $project->created_at->format('Y-m-d') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="p-4">
            {{ $projects->links() }}
        </div>
    </div>

</div>
