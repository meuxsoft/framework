<div class="max-w-3xl mx-auto py-8">
    <div class="text-center mb-8">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 mb-3">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            Upload & Image Processing Engine
        </div>
        <h1 class="text-3xl font-extrabold text-white">Gelişmiş Dosya & Resim Yükleme</h1>
        <p class="text-sm text-slate-400 mt-2">
            Boyutlandırma (Resize), Kırpma (Crop), Küçük Resim (Thumb) ve Rastgele İsim kontrolü.
        </p>
    </div>

    <!-- Upload Form Card -->
    <div class="p-8 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl mb-8">
        <form action="<?= url('/upload-demo') ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
            <?= csrf_field() ?>

            <!-- File Input -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Resim Seçiniz (JPG, PNG, WEBP, GIF)</label>
                <input type="file" name="image" required accept="image/*" class="w-full text-sm text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 file:cursor-pointer cursor-pointer bg-slate-950 border border-slate-800 rounded-xl p-2">
            </div>

            <!-- Options Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <!-- Randomize Toggle -->
                <div class="p-4 rounded-xl bg-slate-950/60 border border-slate-800">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="randomize" value="1" checked class="w-4 h-4 rounded bg-slate-900 border-slate-700 text-indigo-600 focus:ring-0">
                        <div>
                            <span class="text-sm font-semibold text-white block">Rastgele İsim (Randomize)</span>
                            <span class="text-xs text-slate-400">Kapalıysa orijinal dosya adı slug formatında temizlenir.</span>
                        </div>
                    </label>
                </div>

                <!-- Thumbnail Toggle -->
                <div class="p-4 rounded-xl bg-slate-950/60 border border-slate-800">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="thumb" value="1" checked class="w-4 h-4 rounded bg-slate-900 border-slate-700 text-indigo-600 focus:ring-0">
                        <div>
                            <span class="text-sm font-semibold text-white block">Küçük Resim Üret (Thumb: 200x200)</span>
                            <span class="text-xs text-slate-400">Otomatik olarak thumb_ önekiyle thumbnail oluşturur.</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Resize Mode Options -->
            <div class="p-4 rounded-xl bg-slate-950/60 border border-slate-800 space-y-3">
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">Boyutlandırma Modu (Resize: 800x600)</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                    <label class="flex items-center gap-2 p-2.5 rounded-lg bg-slate-900 border border-slate-800 cursor-pointer hover:border-slate-700">
                        <input type="radio" name="mode" value="fit" checked class="text-indigo-600 focus:ring-0">
                        <span><strong>Fit</strong> (Oranlı Sığdır)</span>
                    </label>
                    <label class="flex items-center gap-2 p-2.5 rounded-lg bg-slate-900 border border-slate-800 cursor-pointer hover:border-slate-700">
                        <input type="radio" name="mode" value="crop" class="text-indigo-600 focus:ring-0">
                        <span><strong>Crop</strong> (Merkezden Kırp)</span>
                    </label>
                    <label class="flex items-center gap-2 p-2.5 rounded-lg bg-slate-900 border border-slate-800 cursor-pointer hover:border-slate-700">
                        <input type="radio" name="mode" value="exact" class="text-indigo-600 focus:ring-0">
                        <span><strong>Exact</strong> (Sabit Boyut)</span>
                    </label>
                    <label class="flex items-center gap-2 p-2.5 rounded-lg bg-slate-900 border border-slate-800 cursor-pointer hover:border-slate-700">
                        <input type="radio" name="mode" value="none" class="text-indigo-600 focus:ring-0">
                        <span><strong>Orijinal</strong> (Değiştirme)</span>
                    </label>
                </div>
            </div>

            <button type="submit" class="w-full py-3.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm shadow-lg shadow-indigo-600/30 transition flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                Yükle ve İşle
            </button>
        </form>
    </div>

    <!-- Last Upload Result Preview -->
    <?php if ($uploaded = flash('upload_result')): ?>
        <div class="p-6 rounded-2xl bg-emerald-950/40 border border-emerald-800/80 mb-8">
            <h3 class="text-lg font-bold text-emerald-300 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Dosya Başarıyla Yüklendi ve İşlendi!
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Main Image Card -->
                <div class="p-4 rounded-xl bg-slate-900 border border-slate-800">
                    <span class="text-xs font-semibold text-slate-400 block mb-2">Ana Resim (<?= $uploaded['width'] ?>x<?= $uploaded['height'] ?> px)</span>
                    <img src="<?= $uploaded['url'] ?>" alt="Uploaded" class="w-full h-48 object-contain bg-slate-950 rounded-lg border border-slate-800">
                    <p class="text-xs text-slate-400 mt-2 font-mono break-all"><?= $uploaded['url'] ?></p>
                </div>

                <!-- Thumbnail Card -->
                <?php if (!empty($uploaded['thumb_url'])): ?>
                    <div class="p-4 rounded-xl bg-slate-900 border border-slate-800">
                        <span class="text-xs font-semibold text-slate-400 block mb-2">Küçük Resim (Thumbnail)</span>
                        <img src="<?= $uploaded['thumb_url'] ?>" alt="Thumb" class="w-full h-48 object-contain bg-slate-950 rounded-lg border border-slate-800">
                        <p class="text-xs text-slate-400 mt-2 font-mono break-all"><?= $uploaded['thumb_url'] ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
