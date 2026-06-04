@extends('layouts.app')

@section('title', 'Tambah Temuan NC')

@section('content')
    <div class="max-w-2xl mx-auto space-y-6">

        <div class="flex items-center gap-3">
            <a href="{{ route('inspections.show', $inspection) }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Tambah Temuan NC</h1>
                <p class="text-sm text-gray-500">
                    {{ $inspection->title }} &bull; {{ $inspection->outlet->name }}
                </p>
            </div>
        </div>

        @if ($errors->any())
            <div class="p-4 rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('findings.store', $inspection) }}" enctype="multipart/form-data"
            class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-5">
            @csrf

            {{-- Policy (Category) --}}
            @if ($policy)
                <input type="hidden" name="inspection_policy_id" value="{{ $policy->id }}">
                <div class="p-3 rounded-lg border border-red-200 bg-red-50">
                    <p class="text-xs text-red-700 font-semibold uppercase tracking-wide">Kategori Non-Compliant</p>
                    <p class="text-sm font-semibold text-gray-900 mt-0.5">{{ $policy->name }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">Jatuh tempo: {{ $policy->due_label }} dari tanggal inspeksi</p>
                </div>
            @else
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <select name="inspection_policy_id" required
                        class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-400">
                        <option value="">Pilih kategori…</option>
                        @foreach (\App\Models\InspectionPolicy::orderBy('sort_order')->get() as $pol)
                            <option value="{{ $pol->id }}"
                                {{ old('inspection_policy_id') == $pol->id ? 'selected' : '' }}>
                                {{ $pol->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            {{-- Predefined options as checkboxes + Add Item --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    @if ($policy && $policy->items->count() > 0)
                        Pilihan <span class="text-red-500">*</span>
                        <span class="font-normal text-gray-400 text-xs">(pilih semua yang berlaku)</span>
                    @else
                        Item Temuan
                    @endif
                </label>

                @if ($policy && $policy->items->count() > 0)
                    @php
                        $lainLainId = $policy->items->first(fn($i) => strtolower(trim($i->text)) === 'lain-lain')?->id;
                        $showCustomArea =
                            ($lainLainId &&
                                is_array(old('findings.0.selected_item_ids')) &&
                                in_array($lainLainId, old('findings.0.selected_item_ids'))) ||
                            (is_array(old('findings.0.custom_items')) &&
                                count(array_filter(old('findings.0.custom_items', []))));
                    @endphp
                    <div class="space-y-2 mb-2">
                        @foreach ($policy->items as $item)
                            @php $isLainLain = strtolower(trim($item->text)) === 'lain-lain'; @endphp
                            <label
                                class="flex items-start gap-3 p-3 rounded-lg border border-gray-200
                                       cursor-pointer hover:border-red-400 hover:bg-red-50
                                       has-[:checked]:border-red-500 has-[:checked]:bg-red-50">
                                <input type="checkbox" name="findings[0][selected_item_ids][]" value="{{ $item->id }}"
                                    @if ($isLainLain) onchange="toggleLainLain(this, 'lainlain-area-create', 'custom-items-create')" @endif
                                    @if (is_array(old('findings.0.selected_item_ids')) && in_array($item->id, old('findings.0.selected_item_ids'))) checked @endif
                                    class="mt-0.5 rounded text-red-600 focus:ring-red-500">
                                <span class="text-sm text-gray-700">{{ $item->text }}</span>
                            </label>
                        @endforeach
                    </div>

                    {{-- Lain-lain custom detail area --}}
                    <div id="lainlain-area-create"
                        class="{{ $showCustomArea ? '' : 'hidden' }} mb-2 p-3 rounded-lg border border-dashed border-red-300 bg-red-50">
                        <p class="text-sm font-semibold text-red-700 mb-2">Spesifikasikan temuan Lain-lain:</p>
                        <div id="custom-items-create" class="space-y-2 mb-2">
                            @if (is_array(old('findings.0.custom_items')))
                                @foreach (old('findings.0.custom_items') as $ci)
                                    <div class="flex items-center gap-2">
                                        <input type="text" name="findings[0][custom_items][]"
                                            value="{{ $ci }}" placeholder="Spesifikasikan temuan Lain-lain…"
                                            class="flex-1 text-sm border border-red-200 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-red-400 bg-white">
                                        <button type="button" onclick="this.closest('div').remove()"
                                            class="text-gray-400 hover:text-red-500 text-sm leading-none">&#x2715;</button>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <button type="button" onclick="addCustomItem('custom-items-create')"
                            class="inline-flex items-center gap-1 text-sm font-medium text-red-600 hover:text-red-800">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah baris
                        </button>
                    </div>
                @else
                    {{-- No predefined items: always show custom area --}}
                    <div class="mb-2 p-3 rounded-lg border border-dashed border-red-300 bg-red-50">
                        <p class="text-sm font-semibold text-red-700 mb-2">Tambahkan item temuan:</p>
                        <div id="custom-items-create" class="space-y-2 mb-2">
                            @if (is_array(old('findings.0.custom_items')))
                                @foreach (old('findings.0.custom_items') as $ci)
                                    <div class="flex items-center gap-2">
                                        <input type="text" name="findings[0][custom_items][]"
                                            value="{{ $ci }}" placeholder="Tulis item temuan…"
                                            class="flex-1 text-sm border border-red-200 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-red-400 bg-white">
                                        <button type="button" onclick="this.closest('div').remove()"
                                            class="text-gray-400 hover:text-red-500 text-sm leading-none">&#x2715;</button>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <button type="button" onclick="addCustomItem('custom-items-create')"
                            class="inline-flex items-center gap-1 text-sm font-medium text-red-600 hover:text-red-800">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah item
                        </button>
                    </div>
                @endif
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Deskripsi <span class="text-red-500">*</span>
                </label>
                <textarea name="findings[0][description]" rows="3" placeholder="Jelaskan temuan secara spesifik…" required
                    class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-400">{{ old('findings.0.description') }}</textarea>
            </div>

            {{-- Root Cause --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Root Cause <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach (['people' => 'People (Behaviour)', 'facilities' => 'Facilities', 'training' => 'Training', 'others' => 'Others'] as $val => $lbl)
                        <label
                            class="flex items-center gap-2 p-2.5 rounded-lg border border-gray-200
                              cursor-pointer hover:bg-gray-50 has-[:checked]:border-red-500 has-[:checked]:bg-red-50">
                            <input type="radio" name="findings[0][root_cause]" value="{{ $val }}"
                                {{ old('findings.0.root_cause') === $val ? 'checked' : '' }}
                                class="text-red-600 focus:ring-red-500">
                            <span class="text-sm text-gray-700">{{ $lbl }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Department --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Departemen Responsible <span class="text-red-500">*</span>
                </label>
                <select name="findings[0][department_id]" required
                    class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-400">
                    <option value="">Pilih departemen…</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}"
                            {{ old('findings.0.department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Documentation --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Documentation (Foto Bukti) <span class="text-red-500">*</span>
                </label>
                <input type="file" name="findings[0][photo]" accept="image/*" required
                    class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2
                          file:mr-3 file:py-1 file:px-3 file:rounded file:border-0
                          file:text-sm file:font-medium file:bg-red-50 file:text-red-700
                          hover:file:bg-red-100">
                <p class="text-xs text-gray-400 mt-1">Maks. 5 MB. Format: JPG, PNG, WebP.</p>
            </div>

            {{-- Notes --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Notes</label>
                <input type="text" name="findings[0][keterangan]" value="{{ old('findings.0.keterangan') }}"
                    placeholder="Catatan tambahan (opsional)…"
                    class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-400">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                    class="flex-1 py-2.5 text-sm font-semibold text-white rounded-lg bg-red-600 hover:bg-red-700">
                    Simpan Temuan NC
                </button>
                <a href="{{ route('inspections.show', $inspection) }}"
                    class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Batal
                </a>
            </div>
        </form>
    </div>

    <script>
        function addCustomItem(containerId) {
            const container = document.getElementById(containerId);
            const div = document.createElement('div');
            div.className = 'flex items-center gap-2';
            div.innerHTML = `
                <input type="text" name="findings[0][custom_items][]"
                       placeholder="Spesifikasikan temuan Lain-lain…"
                       class="flex-1 text-sm border border-red-200 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-red-400 bg-white">
                <button type="button" onclick="this.closest('div').remove()"
                        class="text-gray-400 hover:text-red-500 text-sm leading-none">&#x2715;</button>
            `;
            container.appendChild(div);
            div.querySelector('input').focus();
        }

        function toggleLainLain(checkbox, areaId, itemsId) {
            const area = document.getElementById(areaId);
            if (checkbox.checked) {
                area.classList.remove('hidden');
                const items = document.getElementById(itemsId);
                if (items.querySelectorAll('input[type="text"]').length === 0) {
                    addCustomItem(itemsId);
                }
            } else {
                area.classList.add('hidden');
            }
        }
    </script>
@endsection
