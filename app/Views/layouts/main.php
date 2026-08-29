<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? config('app.name', 'Meuxsoft Framework')) ?></title>
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
                <span><?= e(config('app.name', 'Meuxsoft Framework')) ?></span>
            </a>

            <nav class="nav-links">
                <a href="<?= url('/') ?>" class="nav-link">Ana Sayfa</a>
                <a href="<?= url('/api/status') ?>" target="_blank" class="nav-link badge-link">API Status</a>
            </nav>
        </div>
    </header>

    <!-- Flash Messages Container -->
    <div class="flash-container">
        <?php if ($success = flash('success')): ?>
            <div class="alert alert-success">
                <span><?= e($success) ?></span>
            </div>
        <?php endif; ?>

        <?php if ($error = flash('error')): ?>
            <div class="alert alert-danger">
                <span><?= e($error) ?></span>
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
            <p>&copy; <?= date('Y') ?> <strong><?= e(config('app.name')) ?></strong>. PHP 7.3+ Uyumlu Statik MVC Çekirdeği.</p>
        </div>
    </footer>

    <script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
