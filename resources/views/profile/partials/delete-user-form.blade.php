<section class="space-y-6">
    <header>
        <h2 class="text-lg font-bold font-display text-gray-900">{{ __('Delete Account') }}</h2>
        <p class="mt-1 text-sm text-gray-500">{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}</p>
    </header>

    <button type="button" class="btn-danger" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">{{ __('Delete Account') }}</button>

    <div x-data="{ show: false }" x-init="$watch('show', val => { if (!val) document.body.classList.remove('overflow-hidden') })" x-show="show" x-on:open-modal.window="if ($event.detail === 'confirm-user-deletion') { show = true; document.body.classList.add('overflow-hidden') }" x-on:keydown.escape.window="show = false" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
        <div x-show="show" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/40" x-on:click="show = false"></div>
        <div x-show="show" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-4 scale-95" class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')

                <h2 class="text-lg font-bold font-display text-gray-900">{{ __('Are you sure you want to delete your account?') }}</h2>
                <p class="mt-2 text-sm text-gray-500">{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}</p>

                <div class="mt-6">
                    <label for="delete-password" class="sr-only">{{ __('Password') }}</label>
                    <input id="delete-password" name="password" type="password" class="form-input-custom" placeholder="{{ __('Password') }}" />
                    @error('password', 'userDeletion')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" class="btn-secondary" x-on:click="show = false">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn-danger">{{ __('Delete Account') }}</button>
                </div>
            </form>
        </div>
    </div>
</section>
