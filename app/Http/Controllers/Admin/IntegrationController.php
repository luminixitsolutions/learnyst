<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use App\Services\IntegrationService;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IntegrationController extends Controller
{
    use ScopesToCurrentUser;

    public function __construct(
        protected IntegrationService $integrations,
        protected TelegramService $telegram,
    ) {}

    public function index()
    {
        $userId = Auth::id();
        $providers = [];
        foreach (IntegrationService::PROVIDERS as $key => $meta) {
            $providers[$key] = [
                'label' => $meta['label'],
                'keys' => $meta['keys'],
                'config' => $this->integrations->get($key, $userId),
                'status' => $this->integrations->status($key, $userId),
            ];
        }

        return view('admin.integrations.index', compact('providers'));
    }

    public function edit(string $provider)
    {
        abort_unless(isset(IntegrationService::PROVIDERS[$provider]), 404);
        $meta = IntegrationService::PROVIDERS[$provider];
        $config = $this->integrations->get($provider, Auth::id());
        $status = $this->integrations->status($provider, Auth::id());

        return view('admin.integrations.edit', compact('provider', 'meta', 'config', 'status'));
    }

    public function update(Request $request, string $provider)
    {
        abort_unless(isset(IntegrationService::PROVIDERS[$provider]), 404);
        $keys = IntegrationService::PROVIDERS[$provider]['keys'];
        $rules = [];
        foreach ($keys as $key) {
            $rules[$key] = ['nullable', 'string', 'max:1000'];
        }
        $data = $request->validate($rules);
        $data['enabled'] = $request->boolean('enabled');
        $this->integrations->save($provider, $data, Auth::id());
        ActivityLogger::log('integration_updated', "Updated integration {$provider}");

        return redirect()->route('admin.integrations.edit', $provider)->with('success', 'Integration saved.');
    }

    public function test(string $provider)
    {
        abort_unless(isset(IntegrationService::PROVIDERS[$provider]), 404);
        $result = $this->integrations->test($provider, Auth::id());

        return back()->with($result['ok'] ? 'success' : 'error', $result['message']);
    }

    public function testTelegram(Request $request)
    {
        $validated = $request->validate([
            'chat_id' => ['nullable', 'string', 'max:64'],
        ]);
        $result = $this->telegram->sendTest(Auth::id(), $validated['chat_id'] ?? null);

        return back()->with($result['ok'] ? 'success' : 'error', $result['message']);
    }
}
