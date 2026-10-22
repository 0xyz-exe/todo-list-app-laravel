<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Task Details') }}
            </h2>
            <a href="{{ route('tasks.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                ← Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-6">
                        <div class="flex-1">
                            <h1 class="text-2xl font-bold text-gray-900 mb-2 {{ $task->status === 'completed' ? 'line-through text-gray-500' : '' }}">
                                {{ $task->title }}
                            </h1>
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    {{ $task->priority === 'high' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $task->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $task->priority === 'low' ? 'bg-green-100 text-green-800' : '' }}">
                                    {{ ucfirst($task->priority) }} Priority
                                </span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    {{ $task->status === 'pending' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                    {{ ucfirst($task->status) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    @if($task->description)
                        <div class="mb-6">
                            <h3 class="text-sm font-semibold text-gray-700 mb-2">Description</h3>
                            <p class="text-gray-600 whitespace-pre-line">{{ $task->description }}</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 pb-6 border-b">
                        @if($task->due_date)
                            <div>
                                <h3 class="text-sm font-semibold text-gray-700 mb-1">Due Date</h3>
                                <p class="text-gray-900 {{ $task->status === 'pending' && $task->due_date < now() ? 'text-red-600 font-semibold' : '' }}">
                                    {{ $task->due_date->format('F d, Y') }}
                                    @if($task->status === 'pending' && $task->due_date < now())
                                        <span class="text-red-600">(Overdue)</span>
                                    @endif
                                </p>
                            </div>
                        @endif

                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 mb-1">Created</h3>
                            <p class="text-gray-900">{{ $task->created_at->format('F d, Y g:i A') }}</p>
                            <p class="text-sm text-gray-500">{{ $task->created_at->diffForHumans() }}</p>
                        </div>

                        @if($task->completed_at)
                            <div>
                                <h3 class="text-sm font-semibold text-gray-700 mb-1">Completed</h3>
                                <p class="text-gray-900">{{ $task->completed_at->format('F d, Y g:i A') }}</p>
                                <p class="text-sm text-gray-500">{{ $task->completed_at->diffForHumans() }}</p>
                            </div>
                        @endif

                        @if($task->updated_at && $task->updated_at != $task->created_at)
                            <div>
                                <h3 class="text-sm font-semibold text-gray-700 mb-1">Last Updated</h3>
                                <p class="text-gray-900">{{ $task->updated_at->format('F d, Y g:i A') }}</p>
                                <p class="text-sm text-gray-500">{{ $task->updated_at->diffForHumans() }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center gap-3">
                        @if($task->status === 'pending')
                            <form action="{{ route('tasks.complete', $task) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                    ✓ Mark as Completed
                                </button>
                            </form>
                        @else
                            <form action="{{ route('tasks.uncomplete', $task) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                    ↻ Mark as Pending
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('tasks.edit', $task) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                            Edit Task
                        </a>

                        <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this task?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                                Delete Task
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
