{{--
    Reusable comments partial.
    Required variables:
        $comments       - Collection of top-level comments (whereNull('parent_id'))
        $inspectionId   - inspection_id for the store form
        $findingId      - (optional) finding_id for the store form
        $mentionUsers   - Collection of users for @mention autocomplete
--}}

@php $findingId = $findingId ?? null; @endphp

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
    <h3 class="text-base font-semibold text-gray-800 mb-4">Discussion</h3>

    {{-- Add comment form --}}
    <form method="POST" action="{{ route('comments.store') }}" class="mb-6">
        @csrf
        <input type="hidden" name="inspection_id" value="{{ $inspectionId }}">
        @if($findingId)
            <input type="hidden" name="finding_id" value="{{ $findingId }}">
        @endif
        <div class="relative">
            <textarea name="body" rows="2" required
                class="mention-input w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                placeholder="Add a comment... type @ to mention someone"></textarea>
            <div class="mention-dropdown hidden absolute z-20 bg-white border border-gray-200 rounded-lg shadow-lg mt-1 w-56 max-h-48 overflow-y-auto"></div>
        </div>
        <div class="flex justify-end mt-2">
            <button type="submit"
                class="px-4 py-2 text-sm font-medium text-white rounded-lg"
                style="background-color:#1b6840">
                Post
            </button>
        </div>
    </form>

    {{-- Comments list --}}
    @if ($comments->whereNull('parent_id')->count() > 0)
        <div class="space-y-4">
            @foreach ($comments->whereNull('parent_id') as $comment)
                <div class="border border-gray-100 rounded-lg p-4 bg-gray-50">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-700 text-xs font-bold shrink-0">
                            {{ substr($comment->user->name, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-sm font-medium text-gray-800">{{ $comment->user->name }}</span>
                                <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                                @if ($comment->finding_id)
                                    <span class="text-xs text-gray-400">· Finding #{{ $comment->finding->number ?? '' }}</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-700 whitespace-pre-wrap">{!! \App\Helpers\MentionHelper::render(e($comment->body)) !!}</p>

                            {{-- Replies --}}
                            @if ($comment->children->count() > 0)
                                <div class="mt-3 ml-4 space-y-3 border-l-2 border-gray-200 pl-3">
                                    @foreach ($comment->children as $reply)
                                        <div class="flex items-start gap-2">
                                            <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 text-xs font-bold shrink-0">
                                                {{ substr($reply->user->name, 0, 1) }}
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 mb-0.5">
                                                    <span class="text-xs font-medium text-gray-700">{{ $reply->user->name }}</span>
                                                    <span class="text-xs text-gray-400">{{ $reply->created_at->diffForHumans() }}</span>
                                                </div>
                                                <p class="text-xs text-gray-600 whitespace-pre-wrap">{!! \App\Helpers\MentionHelper::render(e($reply->body)) !!}</p>
                                                @if ($reply->user_id === auth()->id() || in_array(auth()->user()->role, ['admin', 'auditor']))
                                                    <form method="POST" action="{{ route('comments.destroy', $reply) }}" class="inline" onsubmit="return confirm('Delete this reply?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="text-xs text-red-400 hover:underline mt-0.5">Delete</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Reply form --}}
                            <div class="mt-3">
                                <button type="button" onclick="toggleReplyForm({{ $comment->id }})"
                                    class="inline-flex items-center gap-1 text-xs font-medium text-green-700 border border-green-200 bg-green-50 hover:bg-green-100 px-2 py-1 rounded-md transition">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                    Reply
                                </button>
                                <div id="reply-form-{{ $comment->id }}" class="hidden mt-2">
                                    <form method="POST" action="{{ route('comments.store') }}">
                                        @csrf
                                        <input type="hidden" name="inspection_id" value="{{ $inspectionId }}">
                                        @if($findingId)
                                            <input type="hidden" name="finding_id" value="{{ $findingId }}">
                                        @endif
                                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                        <div class="relative flex gap-2">
                                            <div class="flex-1 relative">
                                                <input type="text" name="body" required
                                                    class="mention-input w-full text-xs border border-gray-300 rounded-lg px-2 py-1.5 focus:ring-2 focus:ring-green-500"
                                                    placeholder="Reply... type @ to mention">
                                                <div class="mention-dropdown hidden absolute z-20 bg-white border border-gray-200 rounded-lg shadow-lg mt-1 w-56 max-h-48 overflow-y-auto"></div>
                                            </div>
                                            <button type="submit"
                                                class="px-3 py-1.5 text-xs font-medium text-white rounded-lg self-start"
                                                style="background-color:#1b6840">
                                                Reply
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            {{-- Delete --}}
                            @if ($comment->user_id === auth()->id() || in_array(auth()->user()->role, ['admin', 'auditor']))
                                <form method="POST" action="{{ route('comments.destroy', $comment) }}"
                                    class="mt-2 inline"
                                    onsubmit="return confirm('Delete this comment and its replies?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-red-400 hover:underline">Delete</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-sm text-gray-400 text-center py-4">No comments yet. Start the discussion.</p>
    @endif
</div>

@push('scripts')
<script>
    const mentionUsers = @json($mentionUsers->map(function($u) { return ['id' => $u->id, 'name' => $u->name]; })->values()->all());

    function toggleReplyForm(id) {
        const form = document.getElementById('reply-form-' + id);
        if (form) form.classList.toggle('hidden');
    }

    function initMentionInput(input) {
        const dropdown = input.parentElement.querySelector('.mention-dropdown');
        if (!dropdown) return;

        let mentionStart = -1;

        input.addEventListener('input', function () {
            const val = input.value;
            const pos = input.selectionStart;
            const lastAt = val.lastIndexOf('@', pos - 1);

            if (lastAt === -1 || (lastAt > 0 && !/\s/.test(val[lastAt - 1]))) {
                dropdown.classList.add('hidden');
                return;
            }

            const query = val.slice(lastAt + 1, pos).toLowerCase();
            const matches = mentionUsers.filter(u => u.name.toLowerCase().includes(query)).slice(0, 8);

            if (matches.length === 0) {
                dropdown.classList.add('hidden');
                return;
            }

            mentionStart = lastAt;
            dropdown.innerHTML = matches.map(u =>
                '<div class="mention-item px-3 py-2 text-sm text-gray-700 cursor-pointer hover:bg-green-50 hover:text-green-800" data-name="' + u.name + '">' +
                '<span class="font-medium">' + u.name + '</span>' +
                '</div>'
            ).join('');
            dropdown.classList.remove('hidden');

            dropdown.querySelectorAll('.mention-item').forEach(item => {
                item.addEventListener('click', function () {
                    const name = this.dataset.name;
                    const before = val.slice(0, mentionStart);
                    const after = val.slice(pos);
                    input.value = before + '@' + name + ' ' + after;
                    input.focus();
                    dropdown.classList.add('hidden');
                });
            });
        });

        input.addEventListener('blur', function () {
            setTimeout(() => dropdown.classList.add('hidden'), 150);
        });
    }

    document.querySelectorAll('.mention-input').forEach(initMentionInput);
</script>
@endpush
