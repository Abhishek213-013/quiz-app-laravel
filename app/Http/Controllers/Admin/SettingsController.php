<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'general' => [
                'appName' => config('app.name', 'Quiz Application'),
                'adminEmail' => config('mail.from.address', 'admin@quiz.com'),
                'timezone' => config('app.timezone', 'UTC'),
                'maintenanceMode' => false,
            ],
            'security' => [
                'twoFactorAuth' => Cache::get('settings.security.2fa', true),
                'passwordPolicy' => Cache::get('settings.security.password_policy', true),
                'sessionTimeout' => Cache::get('settings.security.session_timeout', 30),
                'allowedIPs' => Cache::get('settings.security.allowed_ips', ''),
            ],
            'notifications' => [
                'emailEnabled' => Cache::get('settings.notifications.email', true),
                'pushEnabled' => Cache::get('settings.notifications.push', false),
                'smsEnabled' => Cache::get('settings.notifications.sms', false),
                'types' => [
                    'newUser' => Cache::get('settings.notifications.types.new_user', true),
                    'quizAttempt' => Cache::get('settings.notifications.types.quiz_attempt', true),
                    'systemAlert' => Cache::get('settings.notifications.types.system_alert', true),
                    'securityAlert' => Cache::get('settings.notifications.types.security_alert', true),
                ]
            ]
        ];

        return Inertia::render('Admin/AdminSettings', [
            'settings' => $settings
        ]);
    }

    public function updateGeneral(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'appName' => 'required|string|max:255',
            'adminEmail' => 'required|email',
            'timezone' => 'required|string|timezone',
            'maintenanceMode' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        Cache::put('settings.general.app_name', $request->appName);
        Cache::put('settings.general.admin_email', $request->adminEmail);
        Cache::put('settings.general.timezone', $request->timezone);

        return back()->with([
            'message' => 'General settings updated successfully!',
            'type' => 'success'
        ]);
    }

    public function updateSecurity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'twoFactorAuth' => 'boolean',
            'passwordPolicy' => 'boolean',
            'sessionTimeout' => 'required|integer|min:5|max:480',
            'allowedIPs' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        Cache::put('settings.security.2fa', $request->twoFactorAuth);
        Cache::put('settings.security.password_policy', $request->passwordPolicy);
        Cache::put('settings.security.session_timeout', $request->sessionTimeout);
        Cache::put('settings.security.allowed_ips', $request->allowedIPs);

        return back()->with([
            'message' => 'Security settings updated successfully!',
            'type' => 'success'
        ]);
    }

    public function updateNotifications(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'emailEnabled' => 'boolean',
            'pushEnabled' => 'boolean',
            'smsEnabled' => 'boolean',
            'types.newUser' => 'boolean',
            'types.quizAttempt' => 'boolean',
            'types.systemAlert' => 'boolean',
            'types.securityAlert' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        Cache::put('settings.notifications.email', $request->emailEnabled);
        Cache::put('settings.notifications.push', $request->pushEnabled);
        Cache::put('settings.notifications.sms', $request->smsEnabled);
        Cache::put('settings.notifications.types.new_user', $request->types['newUser']);
        Cache::put('settings.notifications.types.quiz_attempt', $request->types['quizAttempt']);
        Cache::put('settings.notifications.types.system_alert', $request->types['systemAlert']);
        Cache::put('settings.notifications.types.security_alert', $request->types['securityAlert']);

        return back()->with([
            'message' => 'Notification settings updated successfully!',
            'type' => 'success'
        ]);
    }
}