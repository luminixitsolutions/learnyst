<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate — {{ $certificate->course?->title ?? 'StudyNest' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @include('certificates.partials.styles')
    <style>
        body {
            margin: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
        }
        .toolbar {
            position: sticky;
            top: 0;
            z-index: 10;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            padding: 0.85rem 1.25rem;
            background: rgba(255,255,255,0.92);
            border-bottom: 1px solid #e2e8f0;
            backdrop-filter: blur(8px);
        }
        .toolbar a, .toolbar button {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.55rem 1rem;
            border-radius: 0.7rem;
            font-size: 0.875rem;
            font-weight: 600;
            border: 1px solid transparent;
            cursor: pointer;
            text-decoration: none;
            transition: 0.15s ease;
        }
        .btn-muted { background: #fff; border-color: #e2e8f0; color: #475569; }
        .btn-muted:hover { background: #f8fafc; }
        .btn-primary { background: #059669; color: #fff; }
        .btn-primary:hover { background: #047857; }
        .stage { padding: 1.5rem 1rem 2.5rem; display: flex; justify-content: center; }
        .stage-inner { width: min(960px, 100%); }
        @media print {
            body { background: #fff; }
            .toolbar { display: none !important; }
            .stage { padding: 0; }
            .cert-sheet { box-shadow: none; width: 100%; min-height: 100vh; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <a href="{{ route('learner.courses.show', $certificate->course) }}" class="btn-muted">← Back to course</a>
        <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
            <button type="button" class="btn-muted" onclick="window.print()">Print</button>
            <button type="button" class="btn-primary" onclick="window.print()">Download Certificate</button>
        </div>
    </div>

    <div class="stage">
        <div class="stage-inner">
            @include('certificates.partials.sheet', [
                'layout' => $layout,
                'html' => $html,
                'replacements' => $replacements,
                'preview' => false,
                'showQr' => (bool) ($certificate->course?->settings?->certificate_config['qr_verification'] ?? true),
            ])
        </div>
    </div>
</body>
</html>
