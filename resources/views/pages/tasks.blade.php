<x-filament-panels::page>
    <div class="space-y-4">
        @if(count($tasks) > 0)
            <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Task ID</th>
                            <th class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Status</th>
                            <th class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Type</th>
                            <th class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Index</th>
                            <th class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Duration</th>
                            <th class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Enqueued</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($tasks as $task)
                        <tr class="bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-4 py-3 font-mono text-xs">{{ $task['uid'] }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $color = match($task['status'] ?? '') {
                                        'succeeded' => 'bg-green-100 text-green-800',
                                        'failed' => 'bg-red-100 text-red-800',
                                        'processing' => 'bg-yellow-100 text-yellow-800',
                                        'enqueued' => 'bg-blue-100 text-blue-800',
                                        default => 'bg-gray-100 text-gray-800',
                                    };
                                @endphp
                                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $color }}">
                                    {{ $task['status'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3">{{ $task['type'] }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $task['indexUid'] ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-500">
                                @php
                                    $duration = $task['duration'] ?? null;
                                    if ($duration && is_string($duration)) {
                                        try {
                                            $interval = new DateInterval($duration);
                                            $seconds = ($interval->h * 3600) + ($interval->i * 60) + $interval->s + ($interval->f);
                                            echo round($seconds, 2) . 's';
                                        } catch (\Exception $e) {
                                            echo e($duration);
                                        }
                                    } else {
                                        echo '-';
                                    }
                                @endphp
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $task['enqueuedAt'] ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-8 text-center">
                <p class="text-gray-500">No tasks found.</p>
            </div>
        @endif
    </div>
</x-filament-panels::page>
