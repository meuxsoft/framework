<div class="py-10 sm:py-14 text-center">
    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 mb-6">
        <span class="w-2 h-2 rounded-full bg-indigo-400"></span>
        PHP 7.3+ &bull; %100 Static Core Architecture
    </div>

    <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight text-white mb-6">
        Her Şey Hazır, <br class="hidden sm:inline">
        <span class="bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400 bg-clip-text text-transparent">Kodlamaya Başlayabilirsiniz!</span>
    </h1>

    <p class="max-w-2xl mx-auto text-base sm:text-lg text-slate-400 leading-relaxed mb-8">
        Hafif, modüler ve güvenli static core mimarisi. Aşağıda <code class="text-xs text-indigo-300 bg-slate-800 px-1.5 py-0.5 rounded">User</code> modeli üzerinden çekilen örnek veritabanı kullanımını inceleyebilirsiniz.
    </p>

    <div class="flex flex-wrap items-center justify-center gap-4">
        <a href="<?= url('/api/status') ?>" target="_blank" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm shadow-lg shadow-indigo-600/30 transition-all">
            Sistem Durumu (JSON API)
        </a>
    </div>
</div>

<!-- Model Sample Section -->
<div class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800/80 mb-10">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-2.5">
                <span class="w-2.5 h-2.5 rounded-full <?= $dbConnected ? 'bg-emerald-400 animate-pulse' : 'bg-amber-400' ?>"></span>
                <h2 class="text-lg font-bold text-white">Örnek Model Kullanımı: <code class="text-indigo-400 font-mono text-base">User::getActiveUsers()</code></h2>
            </div>
            <p class="text-xs text-slate-400 mt-1">Veritabanından <code class="text-slate-300">App\Models\User</code> modeli ile çekilen dinamik veriler:</p>
        </div>
        <span class="text-xs font-mono text-slate-400 bg-slate-800/80 px-3 py-1.5 rounded-lg border border-slate-700/60 self-start sm:self-auto">
            Toplam: <?= count($users) ?> Kayıt
        </span>
    </div>

    <?php if (!empty($users)): ?>
        <div class="overflow-x-auto mt-4">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="text-xs uppercase bg-slate-800/50 text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4"># ID</th>
                        <th class="py-3 px-4">Ad Soyad</th>
                        <th class="py-3 px-4">E-Posta</th>
                        <th class="py-3 px-4">Durum</th>
                        <th class="py-3 px-4">Oluşturulma</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php foreach ($users as $u): ?>
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3.5 px-4 font-mono text-xs text-indigo-400">#<?= e($u['id']) ?></td>
                            <td class="py-3.5 px-4 font-medium text-white"><?= e($u['name']) ?></td>
                            <td class="py-3.5 px-4 text-slate-400 font-mono text-xs"><?= e($u['email']) ?></td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                    Aktif
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-xs text-slate-500"><?= e($u['created_at'] ?? 'Yeni') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="py-8 text-center">
            <div class="inline-flex p-3 rounded-xl bg-indigo-500/10 text-indigo-400 mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
            </div>
            <p class="text-sm font-medium text-slate-300">Henüz veritabanında kayıtlı kullanıcı bulunmuyor.</p>
            <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                <code class="text-indigo-300">database/schema.sql</code> dosyasındaki SQL sorgusunu veritabanınızda çalıştırarak örnek verileri anında ekleyebilirsiniz.
            </p>
        </div>
    <?php endif; ?>
</div>

<!-- Architecture Highlights -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800/80">
        <div class="w-10 h-10 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center mb-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
        </div>
        <h3 class="text-base font-bold text-white mb-1.5">Model Mimarisi</h3>
        <p class="text-xs text-slate-400 leading-relaxed">
            <code>User::find($id)</code>, <code>User::create($data)</code>, <code>User::where()</code> metotları doğrudan hazır gelir.
        </p>
    </div>

    <div class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800/80">
        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center mb-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
        </div>
        <h3 class="text-base font-bold text-white mb-1.5">QueryBuilder & PDO</h3>
        <p class="text-xs text-slate-400 leading-relaxed">
            Prepared statements ile %100 SQL Injection korumalı akıcı sorgu inşa edici.
        </p>
    </div>

    <div class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800/80">
        <div class="w-10 h-10 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center justify-center mb-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
        </div>
        <h3 class="text-base font-bold text-white mb-1.5">Dahili Güvenlik</h3>
        <p class="text-xs text-slate-400 leading-relaxed">
            CSRF koruması (<code>csrf_field()</code>), XSS filtreleme (<code>e()</code>), bcrypt şifreleme ve güvenli çerezler.
        </p>
    </div>
</div>
