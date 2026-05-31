<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        // 处理头像上传
        if ($request->hasFile('avatar')) {
            // 存储头像到 public/avatars 目录
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        $user->fill($data);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
    /**
     * 更新用户空闲时间
     */
    public function updateFreeTime(Request $request): RedirectResponse
    {
        $request->validate([
            'free_time' => ['nullable', 'array'],
            'free_time.*' => ['nullable', 'array'],
            'free_time.*.*' => ['string', 'in:12:00-13:30,17:00-19:00,20:00-22:00'],
        ]);

        $request->user()->update([
            'free_time' => $request->input('free_time'),
        ]);

        return Redirect::route('profile.edit')->with('status', 'free-time-updated');
    }
}
