@if(Auth::user()->role === 'user')
<div style="position:relative;" x-data="{ open: false }" @click.outside="open = false">
    <button @click="open = !open" style="cursor:pointer; border:none; background:none; padding:0;">
        <img src="{{ Auth::user()->profile_image ? asset('storage/' . Auth::user()->profile_image) : asset('images/avatar.svg') }}" alt="User" style="width:44px; height:44px; border-radius:50%; object-fit:cover; border:2px solid #eee; {{ !Auth::user()->profile_image ? 'background:#f3f4f6; padding:8px;' : '' }}">
    </button>
    
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         style="position:absolute; right:0; top:100%; margin-top:8px; background:white; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.15); min-width:160px; z-index:1000; display:none;"
         @click="open = false">
        <div style="padding:8px 0;">
            <a href="{{ route('profile.edit') }}" style="display:block; padding:12px 16px; color:#333; text-decoration:none; font-size:14px; transition:background-color 0.2s;" onmouseover="this.style.backgroundColor='#f5f5f5'" onmouseout="this.style.backgroundColor='transparent'">
                Profile
            </a>
            <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                @csrf
                <button type="submit" style="display:block; width:100%; padding:12px 16px; color:#333; text-decoration:none; font-size:14px; background:none; border:none; text-align:left; cursor:pointer; transition:background-color 0.2s;" onmouseover="this.style.backgroundColor='#f5f5f5'" onmouseout="this.style.backgroundColor='transparent'">
                    Logout
                </button>
            </form>
        </div>
    </div>
</div>
@else
<a href="{{ route('profile.edit') }}">
    <img src="{{ Auth::user()->profile_image ? asset('storage/' . Auth::user()->profile_image) : asset('images/avatar.svg') }}" alt="User" style="width:44px; height:44px; border-radius:50%; object-fit:cover; border:2px solid #eee; {{ !Auth::user()->profile_image ? 'background:#f3f4f6; padding:8px;' : '' }}">
</a>
@endif
