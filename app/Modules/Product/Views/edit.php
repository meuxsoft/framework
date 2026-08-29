<div class="page-header">
    <div>
        <h1 class="page-title">Ürün Düzenle: #<?= (int)$product['id'] ?></h1>
        <p class="page-subtitle"><?= e($product['name']) ?> ürün bilgilerini güncelleyin</p>
    </div>
    <div>
        <a href="<?= url('/products') ?>" class="btn btn-secondary">
            &larr; Ürün Listesine Dön
        </a>
    </div>
</div>

<div class="card card-form">
    <form action="<?= url('/products/' . $product['id'] . '/update') ?>" method="POST" enctype="multipart/form-data" class="form-grid">
        <?= csrf_field() ?>
        <?= method_field('POST') ?>

        <div class="form-group col-span-2">
            <label for="name" class="form-label">Ürün Adı <span class="required">*</span></label>
            <input type="text" id="name" name="name" class="form-control" value="<?= e(old('name', $product['name'])) ?>" required>
        </div>

        <div class="form-group">
            <label for="sku" class="form-label">Ürün Kodu (SKU)</label>
            <input type="text" id="sku" name="sku" class="form-control" value="<?= e(old('sku', $product['sku'])) ?>">
        </div>

        <div class="form-group">
            <label for="price" class="form-label">Fiyat (₺) <span class="required">*</span></label>
            <input type="number" step="0.01" id="price" name="price" class="form-control" value="<?= e(old('price', $product['price'])) ?>" required>
        </div>

        <div class="form-group">
            <label for="stock" class="form-label">Stok Adedi</label>
            <input type="number" id="stock" name="stock" class="form-control" value="<?= e(old('stock', $product['stock'])) ?>">
        </div>

        <div class="form-group">
            <label for="image" class="form-label">Yeni Görsel Yükle</label>
            <input type="file" id="image" name="image" class="form-control file-input" accept="image/jpeg,image/png,image/webp">
            <small class="form-hint">Mevcut görseli korumak için boş bırakın.</small>

            <?php if (!empty($product['image'])): ?>
                <div class="current-image-preview mt-2">
                    <span class="text-xs text-muted block mb-1">Mevcut Görsel:</span>
                    <img src="/<?= e($product['image']) ?>" alt="Mevcut Görsel" style="max-height: 80px; border-radius: 6px; border: 1px solid #cbd5e1;">
                </div>
            <?php endif; ?>
        </div>

        <div class="form-group col-span-2">
            <label for="description" class="form-label">Ürün Açıklaması</label>
            <textarea id="description" name="description" rows="4" class="form-control"><?= e(old('description', $product['description'])) ?></textarea>
        </div>

        <div class="form-group col-span-2">
            <label class="checkbox-label">
                <input type="checkbox" name="status" value="1" <?= old('status', $product['status']) ? 'checked' : '' ?>>
                <span>Ürün satışta ve aktif olsun</span>
            </label>
        </div>

        <div class="form-actions col-span-2">
            <button type="submit" class="btn btn-primary">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Değişiklikleri Kaydet
            </button>
            <a href="<?= url('/products') ?>" class="btn btn-outline">İptal</a>
        </div>
    </form>
</div>
