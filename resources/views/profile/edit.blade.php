<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- 1. 基本信息 + 头像 -->
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- 2. 空闲时间设置（新增，位置正确） -->
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">空闲时间设置</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">设置您的空闲时间，系统会自动推荐双方都方便的交易时间</p>
                    
                    <form method="POST" action="{{ route('profile.free-time.update') }}">
                        @csrf
                        @method('PATCH')

                        @php
                            $days = ['周一', '周二', '周三', '周四', '周五', '周六', '周日'];
                            $times = ['12:00-13:30', '17:00-19:00', '20:00-22:00'];
                            $userFreeTime = $user->free_time ?? [];
                        @endphp

                        <div class="space-y-4">
                            @foreach($days as $day)
                                <div class="border-b pb-4 dark:border-gray-700">
                                    <h4 class="font-medium mb-2">{{ $day }}</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($times as $time)
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" 
                                                       name="free_time[{{ $day }}][]" 
                                                       value="{{ $time }}"
                                                       {{ in_array($time, $userFreeTime[$day] ?? []) ? 'checked' : '' }}
                                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700">
                                                <span class="ml-2 text-sm dark:text-gray-300">{{ $time }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            <x-primary-button>保存空闲时间</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 3. 修改密码 -->
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- 4. 删除账号 -->
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>