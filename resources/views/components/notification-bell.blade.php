@php
    $notificationsReady = \Illuminate\Support\Facades\Schema::hasTable('notifications');
    $unreadCount = $notificationsReady ? Auth::user()->unreadNotifications()->count() : 0;
    $items = $notificationsReady
        ? Auth::user()->notifications()->orderBy('created_at', 'desc')->limit(12)->get()
        : collect();
@endphp
<div style="position:relative;" x-data="{ open: false }" @click.outside="open = false">
    <button type="button"
            @click="open = !open"
            aria-label="Notifications"
            style="position:relative; cursor:pointer; border:none; background:none; padding:6px; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#111;"
            onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" style="width:26px; height:26px;">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
        </svg>
        @if($unreadCount > 0)
            <span style="position:absolute; top:2px; right:2px; min-width:18px; height:18px; padding:0 5px; background:#ef4444; color:#fff; font-size:11px; font-weight:700; border-radius:999px; display:flex; align-items:center; justify-content:center; line-height:1; border:2px solid #fff;">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         style="display:none; position:absolute; right:0; top:100%; margin-top:8px; background:white; border-radius:10px; box-shadow:0 8px 24px rgba(0,0,0,0.12); width:min(340px, calc(100vw - 32px)); max-height:380px; overflow:hidden; z-index:1001;">
        <div style="padding:12px 14px; border-bottom:1px solid #eee; display:flex; align-items:center; justify-content:space-between;">
            <span style="font-weight:700; font-size:14px; color:#111;">Notifications</span>
            @if($unreadCount > 0)
                <form action="{{ route('notifications.read-all') }}" method="POST" style="margin:0;">
                    @csrf
                    <button type="submit" style="background:none; border:none; color:#19c2b8; font-size:13px; font-weight:600; cursor:pointer; padding:0;">
                        Mark all read
                    </button>
                </form>
            @endif
        </div>
        <div style="max-height:300px; overflow-y:auto;">
            @forelse($items as $n)
                @php
                    $data = $n->data;
                    $title = is_array($data) ? ($data['title'] ?? 'Update') : 'Update';
                    $message = is_array($data) ? ($data['message'] ?? '') : '';
                    $isUnread = $n->read_at === null;
                @endphp
                <form action="{{ route('notifications.read', $n->id) }}" method="POST" style="margin:0; border-bottom:1px solid #f3f4f6;">
                    @csrf
                    <button type="submit"
                            style="width:100%; text-align:left; padding:12px 14px; border:none; background:{{ $isUnread ? '#f0fdf9' : '#fff' }}; cursor:pointer;"
                            onmouseover="this.style.backgroundColor='#f5f5f5'" onmouseout="this.style.backgroundColor='{{ $isUnread ? '#f0fdf9' : '#fff' }}'">
                        <div style="font-weight:{{ $isUnread ? '700' : '600' }}; font-size:13px; color:#111; margin-bottom:4px;">{{ $title }}</div>
                        @if($message)
                            <div style="font-size:12px; color:#555; line-height:1.4;">{{ \Illuminate\Support\Str::limit($message, 120) }}</div>
                        @endif
                        <div style="font-size:11px; color:#888; margin-top:6px;">{{ $n->created_at->diffForHumans() }}</div>
                    </button>
                </form>
            @empty
                <div style="padding:24px 16px; text-align:center; color:#888; font-size:14px;">
                    No notifications yet.
                </div>
            @endforelse
        </div>
    </div>
</div>
