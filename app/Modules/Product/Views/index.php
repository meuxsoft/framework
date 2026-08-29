<div class="page-header">
    <div>
        <h1 class="page-title">Ürünler Modülü</h1>
        <p class="page-subtitle">Modüler MVC yapısı ile veritabanı CRUD ve dosya yükleme yönetimi</p>
    </div>
    <div>
        <a href="<?= url('/products/create') ?>" class="btn btn-primary">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Yeni Ürün Ekle
        </a>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 70px;">Görsel</th>
                    <th>Ürün Adı</th>
                    <th>SKU</th>
                    <th>Fiyat</th>
                    <th>Stok</th>
                    <th>Durum</th>
                    <th>Tarih</th>
                    <th style="text-align: right;">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="8" class="text-center empty-state">
                            <div class="empty-state-icon">📦</div>
                            <p>Henüz kayıtlı ürün bulunmuyor.</p>
                            <a href="<?= url('/products/create') ?>" class="btn btn-sm btn-primary mt-2">İlk Ürünü Ekle</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($products as $p): ?>
                        <tr>
                            <td>
                                <?php if (!empty($p['image'])): ?>
                                    <img src="/<?= e($p['image']) ?>" alt="<?= e($p['name']) ?>" class="table-thumb">
                                <?php else: ?>
                                    <div class="table-thumb-placeholder">📷</div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= e($p['name']) ?></strong>
                                <?php if (!empty($p['description'])): ?>
                                    <div class="table-desc-snippet"><?= e(mb_substr($p['description'], 0, 60)) ?>...</div>
                                <?php endif; ?>
                            </td>
                            <td><code><?= e($p['sku'] ?: '-') ?></code></td>
                            <td class="font-bold">₺<?= number_format((float)$p['price'], 2, ',', '.') ?></td>
                            <td>
                                <span class="badge <?= $p['stock'] > 5 ? 'badge-success' : ($p['stock'] > 0 ? 'badge-warning' : 'badge-danger') ?>">
                                    <?= (int)$p['stock'] ?> Adet
                                </span>
                            </td>
                            <td>
                                <span class="status-dot <?= $p['status'] ? 'status-active' : 'status-inactive' ?>"></span>
                                <?= $p['status'] ? 'Aktif' : 'Pasif' ?>
                            </td>
                            <td class="text-muted text-sm"><?= e(date('d.m.Y H:i', strtotime($p['created_at'] ?? 'now'))) ?></td>
                            <td class="text-right table-actions">
                                <a href="<?= url('/products/' . $p['id'] . '/edit') ?>" class="btn btn-sm btn-secondary" title="Düzenle">
                                    Düzenle
                                </a>

                                <form action="<?= url('/products/' . $p['id'] . '/delete') ?>" method="POST" class="inline-form" onsubmit="return confirm('Bu ürünü silmek istediğinize emin misiniz?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-danger" title="Sil">
                                        Sil
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
