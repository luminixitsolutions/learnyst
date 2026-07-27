<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class NotificationCenterController extends Controller
{
    public function index()
    {
        $channels = [
            'email_enabled' => Setting::get('notify_email_enabled', '1', 'notifications') === '1',
            'sms_enabled' => Setting::get('notify_sms_enabled', '0', 'notifications') === '1',
            'whatsapp_enabled' => Setting::get('notify_whatsapp_enabled', '0', 'notifications') === '1',
            'push_enabled' => Setting::get('notify_push_enabled', '0', 'notifications') === '1',
            'certificate_expiry_email' => Setting::get('notify_certificate_expiry_email', '1', 'notifications') === '1',
            'certificate_expiry_in_app' => Setting::get('notify_certificate_expiry_in_app', '1', 'notifications') === '1',
        ];

        return view('admin.notifications.index', compact('channels'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'email_enabled' => ['boolean'],
            'sms_enabled' => ['boolean'],
            'whatsapp_enabled' => ['boolean'],
            'push_enabled' => ['boolean'],
            'certificate_expiry_email' => ['boolean'],
            'certificate_expiry_in_app' => ['boolean'],
        ]);

        $map = [
            'notify_email_enabled' => 'email_enabled',
            'notify_sms_enabled' => 'sms_enabled',
            'notify_whatsapp_enabled' => 'whatsapp_enabled',
            'notify_push_enabled' => 'push_enabled',
            'notify_certificate_expiry_email' => 'certificate_expiry_email',
            'notify_certificate_expiry_in_app' => 'certificate_expiry_in_app',
        ];

        foreach ($map as $key => $input) {
            Setting::updateOrCreate(['group' => 'notifications', 'key' => $key], [
                'value' => $request->boolean($input) ? '1' : '0',
                'type' => 'boolean',
            ]);
        }

        ActivityLogger::log('notification_settings_updated', 'Notification center channels updated');

        return back()->with('success', 'Notification settings saved.');
    }
}
