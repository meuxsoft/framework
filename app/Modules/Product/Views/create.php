<div class="page-header">
    <div>
        <h1 class="page-title">Yeni Ürün Ekle</h1>
        <p class="page-subtitle">Ürün bilgilerini ve görselini girerek yeni bir kayıt oluşturun</p>
    </div>
    <div>
        <a href="<?= url('/products') ?>" class="btn btn-secondary">
            &larr; Ürün Listesine Dön
        </a>
    </div>
</div>

<div class="card card-form">
    <form action="<?= url('/products') ?>" method="POST" enctype="multipart/form-data" class="form-grid">
        <?= csrf_field() ?>

        <div class="form-group col-span-2">
            <label for="name" class="form-label">Ürün Adı <span class="required">*</span></label>
            <input type="text" id="name" name="name" class="form-control" value="<?= e(old('name')) ?>" placeholder="Örn: Kablosuz Mekanik Klavye" required>
        </div>

        <div class="form-group">
            <label for="sku" class="form-label">Ürün Kodu (SKU)</label>
            <input type="text" id="sku" name="sku" class="form-control" value="<?= e(old('sku')) ?>" placeholder="Örn: PRD-1001">
        </div>

        <div class="form-group">
            <label for="price" class="form-label">Fiyat (₺) <span class="required">*</span></label>
            <input type="number" step="0.01" id="price" name="price" class="form-control" value="<?= e(old('price')) ?>" placeholder="0.00" required>
        </div>

        <div class="form-group">
            <label for="stock" class="form-label">Stok Adedi</label>
            <input type="number" id="stock" name="stock" class="form-control" value="<?= e(old('stock', '10')) ?>" placeholder="0">
        </div>

        <div class="form-group">
            <label for="image" class="form-label">Ürün Görseli</label>
            <input type="file" id="image" name="image" class="form-control file-input" accept="image/jpeg,image/png,image/webp">
            <small class="form-hint">İzin verilen formatlar: JPG, PNG, WEBP. Maksimum: 5 MB</small>
        </div>

        <div class="form-group col-span-2">
            <label for="description" class="form-label">Ürün Açıklaması</label>
            <textarea id="description" name="description" rows="4" class="form-control" placeholder="Ürün detayları, teknik özellikleri..."><?= e(old('description')) ?></textarea>
        </div>

        <div class="form-group col-span-2">
            <label class="checkbox-label">
                <input type="checkbox" name="status" value="1" <?= old('status', '1') ? 'checked' : '' ?>>
                <span>Ürün satışta ve aktif olsun</span>
            </label>
        </div>

        <div class="form-actions col-span-2">
            <button type="submit" class="btn btn-primary">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Ürünü Kaydet
            </button>
            <a href="<?= url('/products') ?>" class="btn btn-outline">İptal</a>
        </div>
    </form>
</div>
