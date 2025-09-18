<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('Delete Account') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <div class="p-6 text-center">
            <div class="mx-auto w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            
            <h2 class="text-xl font-semibold text-gray-900 mb-4">
                {{ __('Delete Account') }}
            </h2>

            <p class="text-base text-gray-600 mb-6">
                {{ __('Are you sure you want to delete your account? Once deleted, all data will be permanently removed.') }}
            </p>

            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')

                <div class="mb-6">
                    <x-text-input
                        id="password"
                        name="password"
                        type="password"
                        class="w-full"
                        placeholder="{{ __('Enter your password to confirm') }}"
                    />
                    <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                </div>

                <div class="flex space-x-3">
                    <button type="button" x-on:click="$dispatch('close')" class="flex-1 bg-gray-500 text-white px-4 py-3 rounded-md text-base font-medium hover:bg-gray-600 transition-colors">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="flex-1 bg-red-600 text-white px-4 py-3 rounded-md text-base font-medium hover:bg-red-700 transition-colors">
                        {{ __('Delete Account') }}
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
</section>
