<?php

namespace App\Modules\Product\Controllers;

use Core\Controller;
use Core\Libraries\Request\Request;
use Core\Libraries\Upload\Upload;
use Core\Libraries\Session\Session;
use Core\Libraries\Router\Router;
use App\Modules\Product\Models\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     *
     * @return string
     */
    public function index()
    {
        $products = Product::all();

        return $this->view('Product::index', [
            'title'    => 'Ürün Listesi - ' . config('app.name'),
            'products' => $products,
        ]);
    }

    /**
     * Show the form for creating a new product.
     *
     * @return string
     */
    public function create()
    {
        return $this->view('Product::create', [
            'title' => 'Yeni Ürün Ekle - ' . config('app.name'),
        ]);
    }

    /**
     * Store a newly created product in storage.
     *
     * @return void
     */
    public function store()
    {
        // 1. Validate Form Input
        $data = $this->validate(Request::all(), [
            'name'  => 'required|min:2|max:150',
            'sku'   => 'max:50',
            'price' => 'required|numeric',
            'stock' => 'integer',
        ]);

        $imagePath = null;

        // 2. Handle File Upload if provided
        if (Request::hasFile('image')) {
            $uploadResult = Upload::setPath('uploads/products')
                ->setAllowedTypes(['jpg', 'jpeg', 'png', 'webp'])
                ->setMaxSize(5242880) // 5 MB
                ->upload($_FILES['image']);

            if (!$uploadResult['success']) {
                $this->back($uploadResult['error']);
                return;
            }

            $imagePath = $uploadResult['path'];
        }

        // 3. Save Product
        $productId = Product::create([
            'name'        => $data['name'],
            'sku'         => $data['sku'] ?? null,
            'price'       => (float)$data['price'],
            'stock'       => (int)($data['stock'] ?? 0),
            'description' => $data['description'] ?? null,
            'image'       => $imagePath,
            'status'      => isset($data['status']) ? 1 : 0,
        ]);

        Session::flash('success', "Ürün başarıyla oluşturuldu! (ID: #{$productId})");
        $this->redirect('/products');
    }

    /**
     * Display the specified product.
     *
     * @param int|string $id
     * @return string
     */
    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return Router::handleNotFound();
        }

        return $this->view('Product::edit', [
            'title'   => e($product['name']) . ' - Ürün Detayı',
            'product' => $product,
        ]);
    }

    /**
     * Show the form for editing the specified product.
     *
     * @param int|string $id
     * @return string
     */
    public function edit($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return Router::handleNotFound();
        }

        return $this->view('Product::edit', [
            'title'   => 'Ürün Düzenle: ' . e($product['name']),
            'product' => $product,
        ]);
    }

    /**
     * Update the specified product in storage.
     *
     * @param int|string $id
     * @return void
     */
    public function update($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return Router::handleNotFound();
        }

        // 1. Validate Input
        $data = $this->validate(Request::all(), [
            'name'  => 'required|min:2|max:150',
            'sku'   => 'max:50',
            'price' => 'required|numeric',
            'stock' => 'integer',
        ]);

        $imagePath = $product['image'];

        // 2. Process optional new image upload
        if (Request::hasFile('image')) {
            $uploadResult = Upload::setPath('uploads/products')
                ->setAllowedTypes(['jpg', 'jpeg', 'png', 'webp'])
                ->setMaxSize(5242880)
                ->upload($_FILES['image']);

            if (!$uploadResult['success']) {
                $this->back($uploadResult['error']);
                return;
            }

            // Remove old image if present
            if ($imagePath) {
                $oldFullPath = (defined('PUBLIC_PATH') ? PUBLIC_PATH : dirname(__DIR__, 4) . '/public') . '/' . ltrim($imagePath, '/\\');
                if (file_exists($oldFullPath)) {
                    @unlink($oldFullPath);
                }
            }

            $imagePath = $uploadResult['path'];
        }

        // 3. Update database record
        Product::update($id, [
            'name'        => $data['name'],
            'sku'         => $data['sku'] ?? null,
            'price'       => (float)$data['price'],
            'stock'       => (int)($data['stock'] ?? 0),
            'description' => $data['description'] ?? null,
            'image'       => $imagePath,
            'status'      => isset($data['status']) ? 1 : 0,
        ]);

        Session::flash('success', 'Ürün bilgileri başarıyla güncellendi.');
        $this->redirect('/products');
    }

    /**
     * Remove the specified product from storage.
     *
     * @param int|string $id
     * @return void
     */
    public function delete($id)
    {
        $product = Product::find($id);

        if ($product) {
            // Delete image file
            if (!empty($product['image'])) {
                $fullImgPath = (defined('PUBLIC_PATH') ? PUBLIC_PATH : dirname(__DIR__, 4) . '/public') . '/' . ltrim($product['image'], '/\\');
                if (file_exists($fullImgPath)) {
                    @unlink($fullImgPath);
                }
            }

            Product::delete($id);
            Session::flash('success', 'Ürün başarıyla silindi.');
        } else {
            Session::flash('error', 'Silinmek istenen ürün bulunamadı.');
        }

        $this->redirect('/products');
    }
}
