@php
    /** @var string $authToken - CSRF token for approve/deny (Str::random()) */
    /** @var \Laravel\Passport\Client $client */
    /** @var \Illuminate\Foundation\Auth\User $user */
    /** @var \Laravel\Passport\Scope[] $scopes */
    /** @var \Illuminate\Http\Request $request */
    $state = $request->input('state', '');
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Authorize Application • MixuAuth SSO</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 dark:bg-gray-900 min-h-screen flex items-center justify-center px-4">
    <div class="max-w-lg w-full">
        <div class="mb-6 text-center">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-indigo-600/10 text-indigo-500 mb-4">
                <span class="text-2xl font-bold">M</span>
            </div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 tracking-tight">
                MixuAuth Single Sign-On
            </h1>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                Aplikasi berikut meminta izin untuk mengakses akun Anda.
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl border border-gray-100/80 dark:border-gray-700/60 overflow-hidden">
            <div class="px-6 pt-6 pb-4 border-b border-gray-100 dark:border-gray-700/60">
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-indigo-500 mb-2">
                    Permintaan Akses
                </p>
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                            Aplikasi
                        </p>
                        <p class="text-base font-semibold text-gray-900 dark:text-gray-50">
                            {{ $client->name }}
                        </p>
                        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                            Login sebagai <span class="font-medium text-gray-700 dark:text-gray-200">{{ $user->email }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="px-6 py-5 space-y-4">
                @if (count($scopes) > 0)
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-400 dark:text-gray-500 mb-3">
                            Hak akses yang diminta
                        </p>
                        <ul class="space-y-2">
                            @foreach ($scopes as $scope)
                                <li class="flex items-start gap-3">
                                    <span class="mt-1 w-2 h-2 rounded-full bg-indigo-500/80"></span>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $scope->description ?? $scope->id }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            Scope: {{ $scope->id }}
                                        </p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Aplikasi ini hanya akan mengidentifikasi Anda (id, nama, email) tanpa hak akses tambahan.
                    </p>
                @endif
            </div>

            <div class="px-6 pt-4 pb-5 bg-gray-50 dark:bg-gray-900/60 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="text-[11px] text-gray-400 dark:text-gray-500">
                    Anda dapat mencabut akses ini kapan saja melalui dashboard MixuAuth.
                </div>

                <div class="flex items-center gap-2 justify-end">
                    <form method="POST" action="{{ route('passport.authorizations.deny') }}">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="state" value="{{ $state }}">
                        <input type="hidden" name="client_id" value="{{ $client->id }}">
                        <input type="hidden" name="auth_token" value="{{ $authToken }}">

                        <button type="submit" class="inline-flex items-center px-4 py-2 rounded-full text-xs font-medium text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                            Tolak
                        </button>
                    </form>

                    <form method="POST" action="{{ route('passport.authorizations.approve') }}">
                        @csrf
                        <input type="hidden" name="state" value="{{ $state }}">
                        <input type="hidden" name="client_id" value="{{ $client->id }}">
                        <input type="hidden" name="auth_token" value="{{ $authToken }}">

                        <button type="submit" class="inline-flex items-center px-5 py-2 rounded-full text-xs font-semibold tracking-wide text-white bg-indigo-600 hover:bg-indigo-500 shadow-sm transition">
                            Setujui &amp; lanjutkan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

