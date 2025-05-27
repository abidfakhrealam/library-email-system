<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Email Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">Email Inbox</h3>
                        <div class="flex space-x-2">
                            <x-dropdown>
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Filter
                                        <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <form method="GET" action="{{ route('emails.index') }}" class="px-4 py-3">
                                        <div class="space-y-2">
                                            <div>
                                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                                <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                                    <option value="">All Statuses</option>
                                                    <option value="unassigned" {{ request('status') === 'unassigned' ? 'selected' : '' }}>Unassigned</option>
                                                    <option value="assigned" {{ request('status') === 'assigned' ? 'selected' : '' }}>Assigned</option>
                                                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                                                <input type="text" name="search" id="search" value="{{ request('search') }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                            </div>
                                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:text-sm">
                                                Apply Filters
                                            </button>
                                        </div>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">From</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Received</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($emails as $email)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $email->subject }}</div>
                                        <div class="text-sm text-gray-500">{{ Str::limit($email->body_preview, 50) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $email->from_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $email->from_email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $email->received_at->diffForHumans() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span @class([
                                            'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                            'bg-gray-100 text-gray-800' => $email->status === 'unassigned',
                                            'bg-blue-100 text-blue-800' => $email->status === 'assigned',
                                            'bg-yellow-100 text-yellow-800' => $email->status === 'in_progress',
                                            'bg-green-100 text-green-800' => $email->status === 'completed',
                                        ])>
                                            {{ Str::title(str_replace('_', ' ', $email->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $email->assignee->name ?? 'Unassigned' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div x-data="{ open: false }" class="relative inline-block text-left">
                                            <button @click="open = !open" type="button" class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                Actions
                                                <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>

                                            <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10">
                                                <div class="py-1">
                                                    @if($email->status !== 'completed')
                                                        @if($email->status === 'unassigned')
                                                            <form method="POST" action="{{ route('emails.assign', $email) }}" class="px-4 py-2">
                                                                @csrf
                                                                <div class="space-y-2">
                                                                    <select name="assigned_to" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                                                        @foreach($staff as $member)
                                                                            <option value="{{ $member->id }}">{{ $member->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-xs font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                                        Assign
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        @endif

                                                        @if(in_array($email->status, ['assigned', 'in_progress']))
                                                            <form method="POST" action="{{ route('emails.status', ['email' => $email, 'status' => 'in_progress']) }}" class="px-4 py-2">
                                                                @csrf
                                                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                                                                    Mark as In Progress
                                                                </button>
                                                            </form>
                                                        @endif

                                                        @if($email->status === 'in_progress')
                                                            <div x-data="{ replyOpen: false }" class="px-4 py-2">
                                                                <button @click="replyOpen = !replyOpen" class="w-full text-left text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                                                                    Reply
                                                                </button>
                                                                <div x-show="replyOpen" @click.away="replyOpen = false" class="mt-2">
                                                                    <form method="POST" action="{{ route('emails.reply', $email) }}">
                                                                        @csrf
                                                                        <textarea name="reply_content" rows="4" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
                                                                        <button type="submit" class="mt-2 inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-xs font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                                            Send Reply
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endif

                                                    <a href="{{ route('emails.show', $email) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                                                        View Details
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $emails->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
