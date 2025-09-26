<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>


    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <!-- Profile Image Display and Upload -->
        <div class="flex flex-col items-center mb-6">
            <div class="relative">
                <img 
                    src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('images/avatar.svg') }}" 
                    alt="Profile" 
                    class="w-32 h-32 rounded-full object-cover border-4 border-teal-400 mb-2 {{ !$user->profile_image ? 'bg-gray-200 p-4' : '' }}"
                    id="profile-preview"
                >
                @if($user->profile_image)
                    <div class="absolute -top-2 -right-2">
                        <span class="bg-green-500 text-white text-xs px-2 py-1 rounded-full">✓</span>
                    </div>
                @endif
            </div>
            
            <div class="text-center">
                <label for="profile_image" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('Profile Image') }}
                </label>
                <input 
                    type="file" 
                    name="profile_image" 
                    id="profile_image"
                    accept="image/*" 
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100"
                    onchange="previewImage(this)"
                >
                <p class="text-xs text-gray-500 mt-1">Accepted formats: JPEG, PNG, JPG, GIF (max 2MB)</p>
                @error('profile_image')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

        </div>

        <div>
            <x-input-label for="phone" :value="__('Phone')" />
            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $user->phone)" required autocomplete="tel" />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profile-preview').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</section>
