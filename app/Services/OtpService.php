<?php

namespace App\Services;

use App\Contracts\SmsProviderInterface;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class OtpService
{
    public function __construct(protected SmsProviderInterface $sms) {}

    public function issue(?User $user, string $channel, string $destination, string $purpose, int $ttlMinutes = 10): string
    {
        $code = (string) random_int(100000, 999999);

        OtpCode::create([
            'user_id' => $user?->id,
            'channel' => $channel,
            'destination' => $destination,
            'purpose' => $purpose,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes($ttlMinutes),
            'ip_address' => request()->ip(),
        ]);

        if ($channel === 'email') {
            Mail::raw("Your StudyNest verification code is {$code}. It expires in {$ttlMinutes} minutes.", function ($message) use ($destination, $purpose) {
                $message->to($destination)->subject('Verification code — '.$purpose);
            });
        } else {
            $this->sms->send($destination, "StudyNest code: {$code}");
        }

        return $code; // returned only for tests/local; controllers should not expose
    }

    public function verify(string $destination, string $purpose, string $code, ?User $user = null): bool
    {
        $query = OtpCode::query()
            ->where('destination', $destination)
            ->where('purpose', $purpose)
            ->whereNull('consumed_at')
            ->where('expires_at', '>', now())
            ->latest();

        if ($user) {
            $query->where(fn ($q) => $q->where('user_id', $user->id)->orWhereNull('user_id'));
        }

        $otp = $query->first();
        if (! $otp) {
            throw ValidationException::withMessages(['otp' => 'Invalid or expired code.']);
        }

        if ($otp->attempts >= 5) {
            throw ValidationException::withMessages(['otp' => 'Too many attempts. Request a new code.']);
        }

        $otp->increment('attempts');

        if (! Hash::check($code, $otp->code_hash)) {
            throw ValidationException::withMessages(['otp' => 'Incorrect verification code.']);
        }

        $otp->update(['consumed_at' => now()]);

        return true;
    }
}
