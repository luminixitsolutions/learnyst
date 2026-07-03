<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Learnyst</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body{font-family:'Plus Jakarta Sans',sans-serif}</style>
</head>
<body class="min-h-full flex items-center justify-center p-4" style="background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 50%, #f0f9ff 100%);">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-indigo-200/40 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-violet-200/40 rounded-full blur-3xl"></div>
    </div>

    <div class="relative w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-600 items-center justify-center shadow-xl shadow-indigo-500/30 mb-4">
                <span class="text-white font-bold text-2xl">L</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-800">Welcome to Learnyst</h1>
            <p class="text-slate-500 mt-2 text-sm">Sign in to your account</p>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-8 shadow-xl shadow-slate-200/50">
            @if($errors->any())
                <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Password</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400">
                </div>
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2 text-slate-600">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        Remember me
                    </label>
                    <a href="{{ route('password.request') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">Forgot password?</a>
                </div>
                <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-semibold hover:from-indigo-500 hover:to-violet-500 transition shadow-lg shadow-indigo-500/25">
                    Sign In
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-slate-100">
                <p class="text-xs text-slate-500 text-center">Demo: admin@learnyst.com / password</p>
            </div>
        </div>
    </div>
</body>
</html>
