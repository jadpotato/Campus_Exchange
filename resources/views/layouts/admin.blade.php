<form method="POST" action="{{ route('admin.logout') }}">
    @csrf
    <button type="submit" class="block w-full text-left px-4 py-2 hover:bg-gray-700 mt-8 border-t border-gray-700">
        退出登录
    </button>
</form>