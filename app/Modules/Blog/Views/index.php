<div class="max-w-4xl mx-auto py-8">
    <div class="text-center mb-10">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 mb-3">
            <span class="w-2 h-2 rounded-full bg-indigo-400 animate-pulse"></span>
            Modüler Mimari (Module: Blog)
        </div>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-white">Blog Modülü Örneği</h1>
        <p class="text-sm text-slate-400 mt-2">
            Bu sayfa <code class="text-indigo-300">app/Modules/Blog</code> altındaki bağımsız modül tarafından yönetilmektedir.
        </p>
    </div>

    <!-- Blog Posts Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <?php foreach ($posts as $post): ?>
            <article class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 hover:border-indigo-500/40 transition flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                            <?= e($post['tag']) ?>
                        </span>
                        <span class="text-xs text-slate-500"><?= e($post['date']) ?></span>
                    </div>

                    <h2 class="text-xl font-bold text-white group-hover:text-indigo-400 transition mb-2">
                        <a href="<?= url('/blog/' . $post['id']) ?>">
                            <?= e($post['title']) ?>
                        </a>
                    </h2>

                    <p class="text-sm text-slate-400 leading-relaxed mb-4">
                        <?= e($post['summary']) ?>
                    </p>
                </div>

                <div class="pt-4 border-t border-slate-800/80 flex items-center justify-between text-xs">
                    <span class="text-slate-400 font-medium"><?= e($post['author']) ?></span>
                    <a href="<?= url('/blog/' . $post['id']) ?>" class="text-indigo-400 font-semibold group-hover:translate-x-1 transition inline-flex items-center gap-1">
                        Devamını Oku &rarr;
                    </a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <!-- Modül Bilgilendirme Kutusu -->
    <div class="mt-12 p-6 rounded-2xl bg-slate-900/40 border border-slate-800">
        <h3 class="text-sm font-bold text-white mb-2 flex items-center gap-2">
            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Modül Mimarisi Nasıl Çalışır?
        </h3>
        <p class="text-xs text-slate-400 leading-relaxed">
            Yeni bir modül oluşturmak için <code class="text-indigo-300">app/Modules/ModulAdi/</code> klasörünü açıp içine <code class="text-indigo-300">routes.php</code>, <code class="text-indigo-300">Controllers/</code>, <code class="text-indigo-300">Models/</code> ve <code class="text-indigo-300">Views/</code> ekleyin. Ardından <code class="text-indigo-300">app/Config/app.php</code> dosyasındaki <code class="text-indigo-300">modules</code> dizisine modül adını eklemeniz yeterlidir.
        </p>
    </div>
</div>
