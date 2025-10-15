<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{asset('images/kleros-logo.svg')}}" type="image/svg+xml">
    <title>Login — {{ $appName }}</title>
    @vite(['resources/css/site.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex items-center justify-center bg-[#1a1821] text-[#f4f3f6] font-[Segoe_UI,Roboto,system-ui,-apple-system,Arial,sans-serif]">

    <div class="w-full max-w-sm mx-auto px-6 py-10 rounded-3xl border border-white/10 bg-white/5 shadow-2xl backdrop-blur-sm">
        <div class="text-center mb-8">
            <img src="{{ asset('images/kleros-logo.svg') }}" alt="Kleros" class="mx-auto h-10 w-auto mb-3">
            <h1 class="text-2xl font-semibold tracking-tight">Kleros</h1>
            <p class="text-sm text-white/60 mt-1">Acesso ao painel administrativo</p>
        </div>

        @if(session('error'))
            <div class="mb-6 rounded-xl border border-rose-500/30 bg-rose-500/10 text-rose-200 text-sm p-3">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 rounded-xl border border-rose-500/30 bg-rose-500/10 text-rose-200 text-sm p-3">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-white/60 mb-1">Usuário</label>
                <input type="text" id="name" name="name" required autofocus
                       class="w-full rounded-xl border border-white/15 bg-white/10 px-4 py-2 text-sm text-white placeholder-white/40
                              focus:border-[#6449a2] focus:ring-2 focus:ring-[#6449a2]/30 focus:outline-none">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-white/60 mb-1">Senha</label>
                <input type="password" id="password" name="password" required
                       class="w-full rounded-xl border border-white/15 bg-white/10 px-4 py-2 text-sm text-white placeholder-white/40
                              focus:border-[#6449a2] focus:ring-2 focus:ring-[#6449a2]/30 focus:outline-none">
            </div>

            <button type="submit"
                class="w-full mt-6 inline-flex justify-center items-center gap-2 rounded-xl border border-[#6449a2]/50
                       bg-gradient-to-r from-[#8d6add] to-[#6449a2] px-5 py-2.5 text-sm font-medium text-white
                       hover:from-[#9d7be8] hover:to-[#7653bf] transition-all duration-200">
                Entrar
            </button>
        </form>

        <p class="mt-8 text-center text-xs text-white/50">
            &copy; {{ now()->year }} Kleros — Todos os direitos reservados
        </p>
    </div>

</body>
</html>
