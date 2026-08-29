<div class="hero-section">
    <div class="hero-badge">Hafif, Modüler & %100 Static MVC Çekirdeği</div>
    <h1 class="hero-title">Saf PHP 7.3 Gücü ile Modern Mimari</h1>
    <p class="hero-subtitle">
        Gereksiz katmanlar, ağır bağımlılıklar olmadan; sadece saf, profesyonel, sürdürülebilir static core mimarisi.
    </p>

    <div class="hero-actions">
        <a href="<?= url('/products') ?>" class="btn btn-primary">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            Ürünler Modülünü İncele (CRUD)
        </a>
        <a href="<?= url('/about') ?>" class="btn btn-secondary">
            Mimarisi ve Kuralları Gör
        </a>
    </div>
</div>

<div class="features-grid">
    <div class="feature-card">
        <div class="feature-icon text-indigo">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
        </div>
        <h3 class="feature-title">Static Core Libraries</h3>
        <p class="feature-desc">
            <code>Database::table()</code>, <code>Session::set()</code>, <code>Router::get()</code>, <code>Upload::setPath()</code> gibi tüm çekirdek kütüphaneler <code>new</code> olmadan doğrudan static çağrılır.
        </p>
    </div>

    <div class="feature-card">
        <div class="feature-icon text-emerald">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
        </div>
        <h3 class="feature-title">Gelişmiş QueryBuilder</h3>
        <p class="feature-desc">
            PDO tabanlı, SQL Injection korumalı, zincirleme sorgu inşa edici. <code>where</code>, <code>joins</code>, <code>orderBy</code>, <code>insert</code>, <code>update</code>, <code>delete</code> tam destekli.
        </p>
    </div>

    <div class="feature-card">
        <div class="feature-icon text-amber">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
        </div>
        <h3 class="feature-title">Modüler Mimari</h3>
        <p class="feature-desc">
            Her modül kendi Controller, Model, View ve <code>routes.php</code> dosyasına sahiptir. <code>app/Modules/</code> altında izole ve taşınabilir çalışır.
        </p>
    </div>

    <div class="feature-card">
        <div class="feature-icon text-rose">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
        </div>
        <h3 class="feature-title">Dahili Güvenlik</h3>
        <p class="feature-desc">
            CSRF Token doğrulama (<code>csrf_field()</code>), XSS temizleme (<code>e()</code>), bcrypt şifreleme ve güvenli Session yönetimi.
        </p>
    </div>
</div>

<div class="code-preview-section">
    <div class="code-header">
        <div class="dots"><span class="dot red"></span><span class="dot yellow"></span><span class="dot green"></span></div>
        <div class="code-title">Örnek Controller Kullanımı</div>
    </div>
    <pre class="code-block"><code><span class="kw">namespace</span> App\Controllers;

<span class="kw">use</span> Core\Controller;
<span class="kw">use</span> Core\Libraries\Database\Database;
<span class="kw">use</span> Core\Libraries\Session\Session;

<span class="kw">class</span> <span class="cls">ProductController</span> <span class="kw">extends</span> <span class="cls">Controller</span>
{
    <span class="kw">public function</span> <span class="fn">index</span>()
    {
        <span class="var">$products</span> = Database::table(<span class="str">'products'</span>)
            ->where(<span class="str">'status'</span>, 1)
            ->orderBy(<span class="str">'id'</span>, <span class="str">'DESC'</span>)
            ->get();

        <span class="kw">return</span> <span class="var">$this</span>->view(<span class="str">'products.index'</span>, [
            <span class="str">'products'</span> => <span class="var">$products</span>
        ]);
    }
}</code></pre>
</div>
