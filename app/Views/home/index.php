<div class="py-12 sm:py-16 text-center">
    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 mb-6">
        <span class="w-2 h-2 rounded-full bg-indigo-400"></span>
        PHP 7.3+ &bull; %100 Static Core Architecture
    </div>

    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white mb-6">
        Her Şey Hazır, <br class="hidden sm:inline">
        <span class="bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400 bg-clip-text text-transparent">Kodlamaya Başlayabilirsiniz!</span>
    </h1>

    <p class="max-w-2xl mx-auto text-base sm:text-lg text-slate-400 leading-relaxed mb-10">
        Gereksiz katmanlar ve ağır bağımlılıklar olmadan; sadece saf, yüksek performanslı ve sürdürülebilir MVC mimarisi.
    </p>

    <div class="flex flex-wrap items-center justify-center gap-4">
        <a href="<?= url('/api/status') ?>" target="_blank" class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm shadow-lg shadow-indigo-600/30 transition-all">
            Sistem Durumu (JSON API)
        </a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
    <!-- Card 1 -->
    <div class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800/80 hover:border-indigo-500/40 transition group">
        <div class="w-12 h-12 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
        </div>
        <h3 class="text-lg font-bold text-white mb-2">Static Core Mimari</h3>
        <p class="text-sm text-slate-400 leading-relaxed">
            <code class="text-xs text-indigo-300 bg-slate-800 px-1.5 py-0.5 rounded">Database::table()</code>, <code class="text-xs text-indigo-300 bg-slate-800 px-1.5 py-0.5 rounded">Session::set()</code>, <code class="text-xs text-indigo-300 bg-slate-800 px-1.5 py-0.5 rounded">Router::get()</code> gibi tüm çekirdek kütüphaneleri <code class="text-xs text-indigo-300 bg-slate-800 px-1.5 py-0.5 rounded">new</code> olmadan doğrudan kullanın.
        </p>
    </div>

    <!-- Card 2 -->
    <div class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800/80 hover:border-emerald-500/40 transition group">
        <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
        </div>
        <h3 class="text-lg font-bold text-white mb-2">Akıcı QueryBuilder</h3>
        <p class="text-sm text-slate-400 leading-relaxed">
            PDO tabanlı, SQL Injection korumalı, prepared statements sorgular. <code class="text-xs text-emerald-300 bg-slate-800 px-1.5 py-0.5 rounded">where</code>, <code class="text-xs text-emerald-300 bg-slate-800 px-1.5 py-0.5 rounded">joins</code>, <code class="text-xs text-emerald-300 bg-slate-800 px-1.5 py-0.5 rounded">insert</code>, <code class="text-xs text-emerald-300 bg-slate-800 px-1.5 py-0.5 rounded">update</code> tam desteklidir.
        </p>
    </div>

    <!-- Card 3 -->
    <div class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800/80 hover:border-rose-500/40 transition group">
        <div class="w-12 h-12 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
        </div>
        <h3 class="text-lg font-bold text-white mb-2">Dahili Güvenlik</h3>
        <p class="text-sm text-slate-400 leading-relaxed">
            CSRF Token koruması (<code class="text-xs text-rose-300 bg-slate-800 px-1.5 py-0.5 rounded">csrf_field()</code>), XSS filtreleme (<code class="text-xs text-rose-300 bg-slate-800 px-1.5 py-0.5 rounded">e()</code>), bcrypt şifreleme ve güvenli Session yönetimi.
        </p>
    </div>
</div>
