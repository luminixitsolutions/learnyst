<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reading — {{ $item->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-white min-h-screen">
    <div class="max-w-5xl mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h1 class="text-lg font-semibold">{{ $item->title }}</h1>
                <p class="text-xs text-slate-400">Online reader · downloads {{ $item->allow_download ? 'allowed' : 'blocked' }}</p>
            </div>
            <a href="{{ route('learner.library.show', $item) }}" class="text-sm text-emerald-400">Close</a>
        </div>
        @php $url = $item->fileUrl(); $ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION)); @endphp
        @if(in_array($ext, ['pdf'], true) || str_contains((string)$url, '.pdf'))
            <iframe src="{{ $url }}#toolbar={{ $item->allow_download ? 1 : 0 }}" class="w-full h-[80vh] rounded-xl bg-white"></iframe>
        @elseif(in_array($ext, ['png','jpg','jpeg','gif','webp'], true))
            <img src="{{ $url }}" alt="{{ $item->title }}" class="max-w-full mx-auto rounded-xl">
        @else
            <div class="rounded-xl border border-slate-700 p-8 text-center">
                <p class="text-slate-300 mb-4">Preview not available for this file type.</p>
                <a href="{{ $url }}" target="_blank" class="text-emerald-400">Open file</a>
            </div>
        @endif
    </div>
</body>
</html>
