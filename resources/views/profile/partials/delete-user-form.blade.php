<section class="space-y-6">
    <header>
        <h2 class="text-lg font-bold text-black">
            注销账号
        </h2>
        <p class="mt-1 text-sm text-black">
            账号一旦注销，该账号下的所有资源和数据将被永久删除。在注销前，请备份您所有需要保留的数据。
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >注销个人账号</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 bg-white border border-gray-200 rounded">
            @csrf
            @method('delete')

            <h2 class="text-lg font-bold text-black">
                您确定要注销此账号吗？
            </h2>

            <p class="mt-1 text-sm text-black">
                账号一旦注销，其所有资源和数据都将被彻底清除。请输入您的密码以确认您确实想要永久注销。
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="验证密码" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4 text-black bg-white border border-gray-300"
                    placeholder="请输入您的密码"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')" class="text-black bg-white border border-gray-300">
                    取消
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    确认注销账号
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>