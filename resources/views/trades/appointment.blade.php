<x-app-layout>
<x-slot name="header">预约交易</x-slot>
<div class="py-12 max-w-2xl mx-auto">
    <form method="POST" class="bg-white p-6 rounded">
        @csrf
        <div class="mb-3">预约时间<input type="datetime-local" name="appoint_time" class="w-full p-2 border" required></div>
        <div class="mb-3">预约地点<select name="appoint_location" class="w-full p-2 border">
            @foreach($locations as $loc)<option value="{{$loc}}">{{$loc}}</option>@endforeach
        </select></div>
        <button class="bg-indigo-600 text-white p-2 w-full">确认预约</button>
        <a href="{{route('trades.show',$trade)}}" class="block mt-2 text-center">返回</a>
    </form>
</div>
</x-app-layout>