<div class="max-w-3xl mx-auto py-8">
    <div class="mb-6">
        <a href="<?= url('/blog') ?>" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 transition inline-flex items-center gap-1.5 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Tüm Yazılara Dön (Blog Modülü)
        </a>

        <div class="flex items-center gap-3 mb-3">
            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                <?= e($post['tag']) ?>
            </span>
            <span class="text-xs text-slate-500"><?= e($post['date']) ?></span>
            <span class="text-xs text-slate-500">&bull;</span>
            <span class="text-xs text-slate-400 font-medium"><?= e($post['author']) ?></span>
        </div>

        <h1 class="text-3xl sm:text-4xl font-extrabold text-white leading-tight">
            <?= e($post['title']) ?>
        </h1>
    </div>

    <!-- Article Content -->
    <div class="p-8 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl space-y-4 text-slate-300 leading-relaxed text-base">
        <p class="text-lg text-slate-200 font-medium pb-4 border-b border-slate-800">
            <?= e($post['summary']) ?>
        </p>

        <p>
            <?= e($post['content']) ?>
        </p>

        <div class="mt-6 pt-6 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-500 font-mono">
            <span>View: <code class="text-indigo-400">app/Modules/Blog/Views/show.php</code></span>
            <span>Controller: <code class="text-indigo-400">BlogController@show</code></span>
        </div>
    </div>
</div>
