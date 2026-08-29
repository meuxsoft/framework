<div class="card card-wide">
    <div class="card-header">
        <h2>Mimari İlkeler ve Kurallar</h2>
        <span class="badge badge-success">PHP 7.3 Uyumlu</span>
    </div>
    <div class="card-body">
        <p class="mb-4">Bu MVC çatısı, gereksiz karmaşıklıktan uzak, sürdürülebilir, hafif ve yüksek performanslı bir çekirdek sağlamak üzere geliştirilmiştir.</p>

        <div class="rules-list">
            <div class="rule-item">
                <strong>1. Core Kütüphaneleri Kesinlikle Statictir:</strong>
                <p><code>Database</code>, <code>Session</code>, <code>Router</code>, <code>Upload</code>, <code>Layout</code>, <code>Module</code> kütüphaneleri <code>new</code> ile oluşturulmaz, doğrudan static metotlarla çağrılır.</p>
            </div>

            <div class="rule-item">
                <strong>2. APP vs CORE Ayrımı:</strong>
                <p><code>core/</code> dizini evrensel sistem çekirdeğidir ve projeye özel hiçbir kod barındırmaz. <code>app/</code> dizini ise projeye ait Controllers, Models, Views ve Modules katmanlarını içerir.</p>
            </div>

            <div class="rule-item">
                <strong>3. PHP 7.3 Sözdizimi:</strong>
                <p>Typed properties, match expressions, arrow functions, enums gibi PHP 7.4+ veya PHP 8+ özellikleri kullanılmaz.</p>
            </div>

            <div class="rule-item">
                <strong>4. Modüler Yapı:</strong>
                <p>Her modül (örn. <code>app/Modules/Product</code>) kendi Controller, Model, View ve <code>routes.php</code> dosyaları ile bağımsız olarak paketlenebilir.</p>
            </div>
        </div>

        <div class="mt-4">
            <a href="<?= url('/products') ?>" class="btn btn-primary">Örnek Product Modülünü Test Et</a>
        </div>
    </div>
</div>
