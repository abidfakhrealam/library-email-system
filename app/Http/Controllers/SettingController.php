<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserSettingsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('settings.index', compact('user'));
    }

    public function update(UpdateUserSettingsRequest $request)
    {
        $user = Auth::user();
        
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'notification_preferences' => $request->notification_preferences
        ]);

        if ($request->has('password')) {
            $user->update(['password' => bcrypt($request->password)]);
        }

        return back()->with('success', 'Settings updated successfully');
    }

    public function updateEmailPreferences(Request $request)
    {
        $validated = $request->validate([
            'email_notifications' => 'required|boolean',
            'slack_notifications' => 'required|boolean',
            'sms_notifications' => 'required|boolean'
        ]);

        Auth::user()->update($validated);

        return response()->json(['message' => 'Notification preferences updated']);
    }
}
