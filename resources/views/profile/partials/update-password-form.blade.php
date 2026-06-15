<section>
    <header>
        <h2 class="text-lg font-bold text-black">
            修改密码
        </h2>
        <p class="mt-1 text-sm text-black">
            确保您的账号使用的是一个足够长且安全的随机密码。
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" value="当前密码" class="text-black font-bold" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full text-black bg-white border border-gray-300" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" value="新密码" class="text-black font-bold" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full text-black bg-white border border-gray-300" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" value="确认新密码" class="text-black font-bold" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full text-black bg-white border border-gray-300" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>更新密码</x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-600 font-medium"
                >密码更新成功。</p>
            @endif
        </div>
    </form>
</section>