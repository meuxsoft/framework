<div class="hero-section">
    <div class="hero-badge">PHP 7.3+ &bull; Static MVC Framework</div>
    <h1 class="hero-title">Her Şey Hazır, Kodlamaya Başlayabilirsiniz!</h1>
    <p class="hero-subtitle">
        Hafif, modüler, %100 static core mimarisi ile projelerinizi hızlı ve güvenli şekilde geliştirin.
    </p>

    <div class="hero-actions">
        <a href="<?= url('/api/status') ?>" target="_blank" class="btn btn-primary">
            Sistem Durumu (JSON API)
        </a>
    </div>
</div>

<div class="features-grid">
    <div class="feature-card">
        <div class="feature-icon text-indigo">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
        </div>
        <h3 class="feature-title">Static Core Mimari</h3>
        <p class="feature-desc">
            <code>Database::table()</code>, <code>Session::set()</code>, <code>Router::get()</code>, <code>Upload::setPath()</code> gibi tüm çekirdek kütüphaneleri <code>new</code> olmadan doğrudan kullanın.
        </p>
    </div>

    <div class="feature-card">
        <div class="feature-icon text-emerald">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
        </div>
        <h3 class="feature-title">Akıcı QueryBuilder</h3>
        <p class="feature-desc">
            PDO tabanlı, prepared statements korumalı sorgular. <code>where</code>, <code>joins</code>, <code>insert</code>, <code>update</code>, <code>delete</code> tam desteklidir.
        </p>
    </div>

    <div class="feature-card">
        <div class="feature-icon text-rose">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
        </div>
        <h3 class="feature-title">Dahili Güvenlik</h3>
        <p class="feature-desc">
            CSRF koruması (<code>csrf_field()</code>), XSS filtreleme (<code>e()</code>), bcrypt şifreleme ve güvenli Session yönetimi.
        </p>
    </div>
</div>
