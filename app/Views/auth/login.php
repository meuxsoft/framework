<h1 class="text-xl font-bold text-white mb-2">Hesabınıza Giriş Yapın</h1>
<p class="text-xs text-slate-400 mb-6">Bu sayfa <strong>auth.php</strong> layout'unu kullanmaktadır.</p>

<form action="<?= url('/login') ?>" method="POST" class="space-y-4">
    <?= csrf_field() ?>

    <div>
        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">E-Posta Adresi</label>
        <input type="email" name="email" value="<?= old('email') ?>" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/60 border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-indigo-500 transition" placeholder="ornek@meuxsoft.com.tr">
    </div>

    <div>
        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Şifre</label>
        <input type="password" name="password" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/60 border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-indigo-500 transition" placeholder="••••••••">
    </div>

    <div class="flex items-center justify-between text-xs pt-1">
        <label class="flex items-center gap-2 cursor-pointer text-slate-400">
            <input type="checkbox" name="remember" class="rounded bg-slate-950 border-slate-700 text-indigo-600 focus:ring-0">
            Beni Hatırla
        </label>
        <a href="#" class="text-indigo-400 hover:underline">Şifremi Unuttum?</a>
    </div>

    <button type="submit" class="w-full py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm shadow-lg shadow-indigo-600/30 transition mt-2">
        Giriş Yap
    </button>
</form>
