<p>Hello {{ $certificate->user?->name }},</p>
<p>Your certificate <strong>{{ $certificate->certificate_number }}</strong> for <strong>{{ $certificate->course?->title }}</strong> will expire in <strong>{{ $daysUntilExpiry }} days</strong> ({{ $certificate->expires_at?->format('M d, Y') }}).</p>
<p>Please renew to keep your credential valid.</p>
<p>— {{ config('app.name') }}</p>
