<!DOCTYPE html>
<html lang="tr" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? config('app.name', 'Meuxsoft Framework')) ?></title>
    <!-- Tailwind CSS Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="flex flex-col min-h-full font-sans text-slate-100 antialiased selection:bg-indigo-500 selection:text-white">

    <!-- Top Navigation -->
    <header class="border-b border-slate-800/80 bg-slate-900/60 backdrop-blur sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="<?= url('/') ?>" class="flex items-center gap-3 group">
                <span class="px-2.5 py-1 text-xs font-bold bg-indigo-600 text-white rounded-md tracking-wider uppercase shadow-sm group-hover:bg-indigo-500 transition-colors">PHP 7.3+</span>
                <span class="font-bold text-lg text-white group-hover:text-indigo-400 transition-colors"><?= e(config('app.name', 'Meuxsoft Framework')) ?></span>
            </a>

            <nav class="flex items-center gap-4 text-sm font-medium">
                <a href="<?= url('/') ?>" class="text-slate-300 hover:text-white transition-colors">Ana Sayfa</a>
                <a href="<?= url('/login') ?>" class="text-slate-300 hover:text-white transition-colors">Giriş Yap (Özel Layout)</a>
                <a href="<?= url('/api/status') ?>" target="_blank" class="px-3 py-1.5 rounded-lg bg-slate-800 text-slate-300 hover:text-white hover:bg-slate-700 transition border border-slate-700/60 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    API Status
                </a>
            </nav>
        </div>
    </header>

    <!-- Flash Alerts -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 w-full mt-6">
        <?php if ($success = flash('success')): ?>
            <div class="p-4 rounded-xl bg-emerald-950/80 border border-emerald-800/60 text-emerald-300 text-sm flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span><?= e($success) ?></span>
            </div>
        <?php endif; ?>

        <?php if ($error = flash('error')): ?>
            <div class="p-4 rounded-xl bg-rose-950/80 border border-rose-800/60 text-rose-300 text-sm flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                <span><?= e($error) ?></span>
            </div>
        <?php endif; ?>
    </div>

    <!-- Main Content Area -->
    <main class="flex-grow max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 w-full py-8">
        <?= $this->content() ?>
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800/80 bg-slate-900/40 mt-auto py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
            <p>&copy; <?= date('Y') ?> <strong class="text-slate-400"><?= e(config('app.name')) ?></strong>. PHP 7.3+ Uyumlu Statik MVC Çekirdeği.</p>
            <p>Meux Soft &bull; Hafif, Güvenli ve Hızlı</p>
        </div>
    </footer>

</body>
</html>
