@extends('layouts.app')
@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')
    <div class="max-w-lg">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span
                            class="text-red-400">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email <span
                            class="text-red-400">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">New Password <span
                                class="text-gray-400 text-xs">(leave blank to keep)</span></label>
                        <input type="password" name="password"
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input type="password" name="password_confirmation"
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Role <span
                                class="text-red-400">*</span></label>
                        <select name="role" required
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none">
                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="auditor" {{ $user->role === 'auditor' ? 'selected' : '' }}>Auditor</option>
                            <option value="auditee" {{ $user->role === 'auditee' ? 'selected' : '' }}>Department Head
                                (Auditee)</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Departments</label>
                    <div class="grid grid-cols-2 gap-2 border border-gray-300 rounded-lg p-3">
                        @php $userDeptIds = old('department_ids', $user->departments->pluck('id')->all()); @endphp
                        @forelse ($departments as $dept)
                            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                <input type="checkbox" name="department_ids[]" value="{{ $dept->id }}"
                                    {{ in_array($dept->id, $userDeptIds) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-green-700 focus:ring-green-600">
                                {{ $dept->name }}
                            </label>
                        @empty
                            <p class="text-sm text-gray-400 col-span-2">No departments available.</p>
                        @endforelse
                    </div>
                    @error('department_ids')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="px-5 py-2.5 rounded-lg text-white text-sm font-medium hover:opacity-90"
                        style="background:#1b6840">
                        Save Changes
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
