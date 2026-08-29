<!DOCTYPE html>
<html lang="tr" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Giriş Yap - ' . config('app.name')) ?></title>
    <!-- Tailwind CSS Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="flex flex-col min-h-full font-sans text-slate-100 antialiased items-center justify-center p-4 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-slate-900 via-slate-950 to-black">

    <div class="w-full max-w-md">
        <!-- Logo / Brand Header -->
        <div class="text-center mb-8">
            <a href="<?= url('/') ?>" class="inline-flex items-center gap-2 group">
                <span class="px-2.5 py-1 text-xs font-bold bg-indigo-600 text-white rounded-md uppercase tracking-wider group-hover:bg-indigo-500 transition-colors">PHP 7.3+</span>
                <span class="font-extrabold text-xl text-white group-hover:text-indigo-400 transition-colors"><?= e(config('app.name')) ?></span>
            </a>
            <p class="text-xs text-slate-400 mt-2 font-mono">Örnek Özel Düzen: <code class="text-indigo-400">layouts/auth.php</code></p>
        </div>

        <!-- Auth Content Card -->
        <div class="p-8 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-2xl backdrop-blur">
            <?= $content ?>
        </div>

        <!-- Back to Home -->
        <div class="text-center mt-6">
            <a href="<?= url('/') ?>" class="text-xs text-slate-400 hover:text-white transition inline-flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Ana Sayfaya Dön (Main Layout)
            </a>
        </div>
    </div>

</body>
</html>
