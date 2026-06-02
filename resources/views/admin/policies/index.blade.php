@extends('layouts.app')

@section('title', 'Compliance Categories')

@section('content')
    <div class="space-y-6">

        <div>
            <h1 class="text-2xl font-bold text-gray-900">Compliance Categories</h1>
            <p class="text-sm text-gray-500 mt-1">Manage checklist items for each audit category.</p>
        </div>

        @if (session('success'))
            <div class="p-4 rounded-lg bg-green-50 border border-green-200 text-green-800 text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="p-4 rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="space-y-3">
            @foreach ($policies as $policy)
                @php $isOpen = request('open') == $policy->id; @endphp

                <details {{ $isOpen ? 'open' : '' }}
                    class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden group">

                    <summary
                        class="flex items-center justify-between px-5 py-4 cursor-pointer hover:bg-gray-50 transition list-none select-none">
                        <div class="flex items-center gap-3">
                            <span
                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold text-white"
                                style="background-color:#1b6840">
                                {{ $policy->code }}
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">{{ $policy->name }}</p>
                                <p class="text-xs text-gray-400">
                                    Due: D+{{ $policy->due_date_offset_days }} &bull;
                                    Score: {{ $policy->score }} &bull;
                                    <span class="font-medium text-gray-600">{{ $policy->items->count() }}
                                        item{{ $policy->items->count() !== 1 ? 's' : '' }}</span>
                                </p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 transition-transform group-open:rotate-180" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </summary>

                    <div class="border-t border-gray-100 px-5 py-4 space-y-3">

                        @if ($policy->items->count() > 0)
                            <ul class="space-y-1.5">
                                @foreach ($policy->items->sortBy('sort_order') as $item)
                                    <li class="flex items-center gap-3 group/item">
                                        <span
                                            class="text-gray-400 text-xs w-5 text-right shrink-0">{{ $loop->iteration }}.</span>

                                        <form method="POST"
                                            action="{{ route('admin.policies.items.update', [$policy, $item]) }}"
                                            class="flex-1 flex items-center gap-2">
                                            @csrf @method('PUT')
                                            <input type="text" name="text" value="{{ $item->text }}"
                                                class="flex-1 text-sm px-2 py-1 rounded-lg border border-transparent bg-transparent transition
                                          hover:border-gray-200 hover:bg-gray-50
                                          focus:border-green-400 focus:ring-2 focus:ring-green-300 focus:bg-white">
                                            <button type="submit"
                                                class="shrink-0 px-2.5 py-1 text-xs font-medium text-green-700 bg-green-50 border border-green-300 rounded-lg hover:bg-green-100
                                           opacity-0 group-hover/item:opacity-100 focus:opacity-100 transition">
                                                Save
                                            </button>
                                        </form>

                                        <form method="POST"
                                            action="{{ route('admin.policies.items.destroy', [$policy, $item]) }}"
                                            onsubmit="return confirm('Hapus item ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 opacity-0 group-hover/item:opacity-100 focus:opacity-100 transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-gray-400 italic py-1">Belum ada item untuk kategori ini.</p>
                        @endif

                        <form method="POST" action="{{ route('admin.policies.items.store', $policy) }}"
                            class="flex items-center gap-2 pt-3 border-t border-dashed border-gray-200">
                            @csrf
                            <input type="text" name="text" placeholder="Tambah item checklist baru…" required
                                class="flex-1 text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-400 focus:border-green-400">
                            <button type="submit"
                                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 shrink-0"
                                style="background-color:#1b6840">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Add Item
                            </button>
                        </form>

                    </div>
                </details>
            @endforeach
        </div>

    </div>
@endsection
