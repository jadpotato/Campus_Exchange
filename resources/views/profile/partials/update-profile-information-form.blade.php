<section>
    <header>
        <h2 class="text-lg font-bold text-black">
            基本信息
        </h2>
        <p class="mt-1 text-sm text-black">
            更新您的账号个人资料信息、头像和电子邮箱地址。
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="avatar" value="用户头像" class="text-black font-bold" />
            <img src="{{ $user->avatar_url }}" class="w-20 h-20 rounded-full object-cover my-2 border border-gray-200" alt="Avatar">
            <input id="avatar" name="avatar" type="file" class="mt-1 block w-full text-sm text-black bg-white border border-gray-300 rounded p-1">
            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
        </div>
        <div>
            <x-input-label for="name" value="用户名" class="text-black font-bold" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full text-black bg-white border border-gray-300" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" value="电子邮箱" class="text-black font-bold" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full text-black bg-white border border-gray-300" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-black">
                        您的邮箱地址未验证。
                        <button form="send-verification" class="underline text-sm text-indigo-600 hover:text-indigo-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            点击此处重新发送验证邮件。
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            新的验证链接已发送至您的邮箱。
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>保存修改</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-600 font-medium"
                >已保存。</p>
            @endif
        </div>
    </form>
</section>