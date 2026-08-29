<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? config('app.name', 'PHP 7.3 Static MVC')) ?></title>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>
    <!-- Navbar -->
    <header class="navbar">
        <div class="nav-container">
            <a href="<?= url('/') ?>" class="nav-brand">
                <div class="brand-badge">PHP 7.3</div>
                <span>Static MVC</span>
            </a>

            <nav class="nav-links">
                <a href="<?= url('/') ?>" class="nav-link">Ana Sayfa</a>
                <a href="<?= url('/products') ?>" class="nav-link">Ürünler (Modül)</a>
                <a href="<?= url('/about') ?>" class="nav-link">Mimarisi</a>
                <a href="<?= url('/api/status') ?>" target="_blank" class="nav-link badge-link">API Status</a>
            </nav>
        </div>
    </header>

    <!-- Flash Messages Container -->
    <div class="flash-container">
        <?php if ($success = flash('success')): ?>
            <div class="alert alert-success">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span><?= e($success) ?></span>
            </div>
        <?php endif; ?>

        <?php if ($error = flash('error')): ?>
            <div class="alert alert-danger">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                <span><?= e($error) ?></span>
            </div>
        <?php endif; ?>

        <?php if ($errors = flash('errors')): ?>
            <div class="alert alert-danger">
                <ul class="error-list">
                    <?php foreach ($errors as $field => $errList): ?>
                        <?php foreach ((array)$errList as $err): ?>
                            <li><?= e($err) ?></li>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>

    <!-- Main Content Yield -->
    <main class="main-content">
        <?= $content ?>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <p>&copy; <?= date('Y') ?> <strong>PHP 7.3 Static MVC Framework</strong>. Basit, Hızlı, Modüler & Profesyonel Çekirdek.</p>
            <p class="footer-meta">Bellek Kullanımı: <?= round(memory_get_usage() / 1024 / 1024, 2) ?> MB &bull; PHP <?= phpversion() ?></p>
        </div>
    </footer>

    <script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
