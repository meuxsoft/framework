<?php

namespace Core\Libraries\Upload;

use RuntimeException;

class Upload
{
    /**
     * @var string
     */
    protected $uploadPath = 'uploads';

    /**
     * @var array
     */
    protected $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf'];

    /**
     * @var array
     */
    protected $allowedMimeTypes = [
        'jpg'  => ['image/jpeg', 'image/pjpeg'],
        'jpeg' => ['image/jpeg', 'image/pjpeg'],
        'png'  => ['image/png', 'image/x-png'],
        'gif'  => ['image/gif'],
        'webp' => ['image/webp'],
        'pdf'  => ['application/pdf'],
    ];

    /**
     * @var int (Default 5 MB)
     */
    protected $maxSize = 5242880;

    /**
     * @var bool
     */
    protected $randomizeName = true;

    /**
     * @var string|null
     */
    protected $customName = null;

    /**
     * @var bool
     */
    protected $resizeEnabled = false;

    /**
     * @var int
     */
    protected $resizeWidth = 0;

    /**
     * @var int
     */
    protected $resizeHeight = 0;

    /**
     * @var string (fit, crop, exact, width, height)
     */
    protected $resizeMode = 'fit';

    /**
     * @var int (1-100)
     */
    protected $imageQuality = 85;

    /**
     * @var bool
     */
    protected $thumbEnabled = false;

    /**
     * @var int
     */
    protected $thumbWidth = 150;

    /**
     * @var int
     */
    protected $thumbHeight = 150;

    /**
     * @var string
     */
    protected $thumbPrefix = 'thumb_';

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * Protected constructor. Use static setPath() or init().
     */
    protected function __construct()
    {
    }

    /**
     * Initialize upload fluent builder with target path.
     *
     * @param string|null $path
     * @return static
     */
    public static function setPath(?string $path = null): self
    {
        $instance = new self();
        if ($path !== null) {
            $instance->path($path);
        }
        return $instance;
    }

    /**
     * Alias for setPath.
     *
     * @param string|null $path
     * @return static
     */
    public static function init(?string $path = null): self
    {
        return self::setPath($path);
    }

    /**
     * Set target upload directory.
     *
     * @param string $path
     * @return $this
     */
    public function path(string $path): self
    {
        $this->uploadPath = trim(str_replace(['..', "\0"], '', $path), '/\\');
        return $this;
    }

    /**
     * Set allowed file extensions.
     *
     * @param array $types
     * @return $this
     */
    public function setAllowedTypes(array $types): self
    {
        $this->allowedTypes = array_map('strtolower', $types);
        return $this;
    }

    /**
     * Set max file size in bytes.
     *
     * @param int $bytes
     * @return $this
     */
    public function setMaxSize(int $bytes): self
    {
        $this->maxSize = $bytes;
        return $this;
    }

    /**
     * Set whether to randomize the filename.
     *
     * @param bool $randomize
     * @return $this
     */
    public function randomize(bool $randomize = true): self
    {
        $this->randomizeName = $randomize;
        return $this;
    }

    /**
     * Set custom filename (extension is preserved).
     *
     * @param string $name
     * @return $this
     */
    public function setCustomName(string $name): self
    {
        $this->customName = $name;
        return $this;
    }

    /**
     * Configure image resizing.
     *
     * @param int $width
     * @param int $height
     * @param string $mode 'fit' (proportional within box), 'crop' (cover & center crop), 'exact' (fixed stretch), 'width' (scale by width), 'height' (scale by height)
     * @param int|null $quality Compression quality (1-100)
     * @return $this
     */
    public function resize(int $width, int $height = 0, string $mode = 'fit', ?int $quality = null): self
    {
        $this->resizeEnabled = true;
        $this->resizeWidth = $width;
        $this->resizeHeight = $height;
        $this->resizeMode = strtolower($mode);

        if ($quality !== null) {
            $this->imageQuality = max(1, min(100, $quality));
        }

        return $this;
    }

    /**
     * Configure automatic thumbnail generation.
     *
     * @param bool $enable
     * @param int $width
     * @param int $height
     * @param string $prefix
     * @return $this
     */
    public function thumb(bool $enable = true, int $width = 150, int $height = 150, string $prefix = 'thumb_'): self
    {
        $this->thumbEnabled = $enable;
        $this->thumbWidth = $width;
        $this->thumbHeight = $height;
        $this->thumbPrefix = $prefix;
        return $this;
    }

    /**
     * Set image compression quality (1-100).
     *
     * @param int $quality
     * @return $this
     */
    public function quality(int $quality): self
    {
        $this->imageQuality = max(1, min(100, $quality));
        return $this;
    }

    /**
     * Static direct upload shortcut.
     *
     * @param array $fileArray ($_FILES['key'])
     * @param string $targetPath
     * @param array $allowedTypes
     * @return array
     */
    public static function file(array $fileArray, string $targetPath = 'uploads', array $allowedTypes = ['jpg', 'jpeg', 'png', 'webp']): array
    {
        return self::setPath($targetPath)->setAllowedTypes($allowedTypes)->upload($fileArray);
    }

    /**
     * Process upload for a file or multiple files.
     *
     * @param array $fileArray ($_FILES['key'])
     * @return array
     */
    public function upload(array $fileArray): array
    {
        $this->errors = [];

        // Check if multiple files uploaded
        if (isset($fileArray['name']) && is_array($fileArray['name'])) {
            return $this->uploadMultiple($fileArray);
        }

        return $this->uploadSingle($fileArray);
    }

    /**
     * Upload and process a single file.
     *
     * @param array $file
     * @return array
     */
    protected function uploadSingle(array $file): array
    {
        if (empty($file) || !isset($file['error'])) {
            return [
                'success' => false,
                'error'   => 'Geçersiz dosya verisi.',
                'file'    => null
            ];
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return [
                'success' => false,
                'error'   => $this->getUploadErrorMessage($file['error']),
                'file'    => null
            ];
        }

        // Check file size
        if ($file['size'] > $this->maxSize) {
            $maxMb = round($this->maxSize / 1048576, 2);
            return [
                'success' => false,
                'error'   => "Dosya boyutu çok büyük. Maksimum izin verilen: {$maxMb} MB.",
                'file'    => null
            ];
        }

        // Check filename and extension
        $origName = $file['name'];

        // Security 1: Null byte & Path Traversal check
        if (strpos($origName, "\0") !== false || strpos($origName, '..') !== false) {
            return [
                'success' => false,
                'error'   => 'Güvenlik ihlali: Geçersiz veya tehlikeli dosya adı.',
                'file'    => null
            ];
        }

        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

        // Security 2: Dangerous Executable Extensions Blacklist
        $dangerousExtensions = [
            'php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'phar', 'pht', 'phps',
            'shtml', 'cgi', 'pl', 'asp', 'aspx', 'jsp', 'sh', 'bash', 'exe', 'bat', 'cmd',
            'vbs', 'htaccess', 'htpasswd', 'svg', 'xhtml', 'htm', 'html', 'js', 'jar', 'py'
        ];

        if (in_array($ext, $dangerousExtensions, true)) {
            return [
                'success' => false,
                'error'   => "Güvenlik Engeli: .{$ext} uzantılı yürütülebilir dosyaların yüklenmesi yasaktır.",
                'file'    => null
            ];
        }

        // Security 3: Double extension detection (e.g., shell.php.jpg)
        $nameParts = explode('.', $origName);
        if (count($nameParts) > 2) {
            foreach ($nameParts as $part) {
                if (in_array(strtolower($part), $dangerousExtensions, true)) {
                    return [
                        'success' => false,
                        'error'   => "Güvenlik Engeli: Çift uzantılı (.{$part}) zararlı dosya tespit edildi.",
                        'file'    => null
                    ];
                }
            }
        }

        if (!in_array($ext, $this->allowedTypes, true)) {
            return [
                'success' => false,
                'error'   => "İzin verilmeyen dosya uzantısı: .{$ext}. İzin verilenler: " . implode(', ', $this->allowedTypes),
                'file'    => null
            ];
        }

        // Validate MIME type safely
        $mime = $this->detectMimeType($file['tmp_name'], $file['type'] ?? null);

        if ($mime && isset($this->allowedMimeTypes[$ext])) {
            if (!in_array($mime, $this->allowedMimeTypes[$ext], true)) {
                return [
                    'success' => false,
                    'error'   => "Güvenlik Uyarısı: Dosya MIME tipi ({$mime}) uzantısıyla uyuşmuyor.",
                    'file'    => null
                ];
            }
        }

        // Resolve absolute target directory
        $cleanUploadPath = trim(str_replace(['..', "\0"], '', $this->uploadPath), '/\\');
        $targetDir = PUBLIC_PATH . '/' . $cleanUploadPath;

        if (!is_dir($targetDir)) {
            if (!mkdir($targetDir, 0777, true) && !is_dir($targetDir)) {
                return [
                    'success' => false,
                    'error'   => "Yükleme dizini oluşturulamadı: {$cleanUploadPath}",
                    'file'    => null
                ];
            }
        }

        // Generate safe filename
        if ($this->customName !== null) {
            $finalFilename = $this->sanitizeFilename($this->customName) . '.' . $ext;
        } elseif ($this->randomizeName) {
            $finalFilename = bin2hex(random_bytes(16)) . '_' . time() . '.' . $ext;
        } else {
            $cleanBase = $this->sanitizeFilename(pathinfo($origName, PATHINFO_FILENAME));
            $finalFilename = $cleanBase . '_' . time() . '.' . $ext;
        }

        $destination = $targetDir . '/' . $finalFilename;
        $relativePath = $cleanUploadPath . '/' . $finalFilename;

        // Move uploaded file to target destination
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return [
                'success' => false,
                'error'   => 'Dosya hedef dizine taşınamadı.',
                'file'    => null
            ];
        }

        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true);
        $finalWidth = null;
        $finalHeight = null;
        $thumbPath = null;
        $thumbUrl = null;

        // Image Processing (Resizing & Thumbnails)
        if ($isImage && extension_loaded('gd')) {
            // 1. Process Main Image Resizing if enabled
            if ($this->resizeEnabled && $this->resizeWidth > 0) {
                $this->processImage(
                    $destination,
                    $destination,
                    $this->resizeWidth,
                    $this->resizeHeight,
                    $this->resizeMode,
                    $this->imageQuality
                );
            }

            // Read dimensions of final image
            $imgSize = @getimagesize($destination);
            if ($imgSize) {
                $finalWidth = $imgSize[0];
                $finalHeight = $imgSize[1];
            }

            // 2. Generate Thumbnail if enabled
            if ($this->thumbEnabled && $this->thumbWidth > 0) {
                $thumbFilename = $this->thumbPrefix . $finalFilename;
                $thumbDestination = $targetDir . '/' . $thumbFilename;

                $thumbCreated = $this->processImage(
                    $destination,
                    $thumbDestination,
                    $this->thumbWidth,
                    $this->thumbHeight,
                    'crop', // Default thumbnail mode: center crop
                    $this->imageQuality
                );

                if ($thumbCreated) {
                    $thumbPath = $cleanUploadPath . '/' . $thumbFilename;
                    $thumbUrl = '/' . ltrim($thumbPath, '/');
                }
            }
        }

        return [
            'success'       => true,
            'filename'      => $finalFilename,
            'original_name' => $origName,
            'extension'     => $ext,
            'mime'          => $mime,
            'size'          => filesize($destination),
            'width'         => $finalWidth,
            'height'        => $finalHeight,
            'path'          => $relativePath,
            'url'           => '/' . ltrim($relativePath, '/'),
            'thumb_path'    => $thumbPath,
            'thumb_url'     => $thumbUrl,
            'error'         => null
        ];
    }

    /**
     * Resize, crop or convert an image using PHP GD.
     *
     * @param string $sourcePath
     * @param string $destPath
     * @param int $targetWidth
     * @param int $targetHeight
     * @param string $mode 'fit', 'crop', 'exact', 'width', 'height'
     * @param int $quality
     * @return bool
     */
    public function processImage(string $sourcePath, string $destPath, int $targetWidth, int $targetHeight = 0, string $mode = 'fit', int $quality = 85): bool
    {
        if (!file_exists($sourcePath)) {
            return false;
        }

        $imageInfo = @getimagesize($sourcePath);
        if (!$imageInfo) {
            return false;
        }

        [$origWidth, $origHeight, $imageType] = $imageInfo;

        // Load image resource based on type
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $sourceImage = @imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $sourceImage = @imagecreatefrompng($sourcePath);
                break;
            case IMAGETYPE_GIF:
                $sourceImage = @imagecreatefromgif($sourcePath);
                break;
            case IMAGETYPE_WEBP:
                $sourceImage = function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($sourcePath) : false;
                break;
            default:
                return false;
        }

        if (!$sourceImage) {
            return false;
        }

        // Calculate new dimensions and crop coordinates
        $srcX = 0;
        $srcY = 0;
        $srcW = $origWidth;
        $srcH = $origHeight;

        $newW = $targetWidth;
        $newH = $targetHeight > 0 ? $targetHeight : $targetWidth;

        switch ($mode) {
            case 'crop':
            case 'cover':
                // Scale and crop from center to achieve exact dimensions
                $ratioOrig = $origWidth / $origHeight;
                $ratioTarget = $newW / $newH;

                if ($ratioOrig > $ratioTarget) {
                    $srcW = (int)round($origHeight * $ratioTarget);
                    $srcX = (int)round(($origWidth - $srcW) / 2);
                } else {
                    $srcH = (int)round($origWidth / $ratioTarget);
                    $srcY = (int)round(($origHeight - $srcH) / 2);
                }
                break;

            case 'width':
                // Scale height proportionally based on target width
                $newH = (int)round(($origHeight / $origWidth) * $newW);
                break;

            case 'height':
                // Scale width proportionally based on target height
                $newW = (int)round(($origWidth / $origHeight) * $newH);
                break;

            case 'exact':
            case 'fixed':
                // Forced exact dimensions (may stretch)
                break;

            case 'fit':
            case 'auto':
            default:
                // Fit within bounding box keeping aspect ratio
                $scale = min($newW / $origWidth, $newH / $origHeight);
                if ($scale < 1) {
                    $newW = (int)round($origWidth * $scale);
                    $newH = (int)round($origHeight * $scale);
                } else {
                    $newW = $origWidth;
                    $newH = $origHeight;
                }
                break;
        }

        // Create new truecolor canvas
        $canvas = imagecreatetruecolor($newW, $newH);

        // Preserve transparency for PNG, GIF and WebP
        if ($imageType === IMAGETYPE_PNG || $imageType === IMAGETYPE_WEBP) {
            imagealphablending($canvas, false);
            imagesavealpha($canvas, true);
            $transparent = imagecolorallocatealpha($canvas, 255, 255, 255, 127);
            imagefilledrectangle($canvas, 0, 0, $newW, $newH, $transparent);
        } elseif ($imageType === IMAGETYPE_GIF) {
            $transparentIndex = imagecolortransparent($sourceImage);
            if ($transparentIndex >= 0) {
                $transparentColor = imagecolorsforindex($sourceImage, $transparentIndex);
                $transparentIndex = imagecolorallocate($canvas, $transparentColor['red'], $transparentColor['green'], $transparentColor['blue']);
                imagefill($canvas, 0, 0, $transparentIndex);
                imagecolortransparent($canvas, $transparentIndex);
            }
        }

        // Resample image
        imagecopyresampled($canvas, $sourceImage, 0, 0, $srcX, $srcY, $newW, $newH, $srcW, $srcH);

        // Save image to destination
        $saved = false;
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $saved = imagejpeg($canvas, $destPath, $quality);
                break;
            case IMAGETYPE_PNG:
                // PNG quality scale is 0 (no compression) to 9
                $pngQuality = (int)round((100 - $quality) / 10);
                $saved = imagepng($canvas, $destPath, $pngQuality);
                break;
            case IMAGETYPE_GIF:
                $saved = imagegif($canvas, $destPath);
                break;
            case IMAGETYPE_WEBP:
                if (function_exists('imagewebp')) {
                    $saved = imagewebp($canvas, $destPath, $quality);
                }
                break;
        }

        imagedestroy($sourceImage);
        imagedestroy($canvas);

        return $saved;
    }

    /**
     * Upload multiple files.
     *
     * @param array $fileArray
     * @return array
     */
    protected function uploadMultiple(array $fileArray): array
    {
        $results = [];
        $count = count($fileArray['name']);

        for ($i = 0; $i < $count; $i++) {
            $single = [
                'name'     => $fileArray['name'][$i],
                'type'     => $fileArray['type'][$i],
                'tmp_name' => $fileArray['tmp_name'][$i],
                'error'    => $fileArray['error'][$i],
                'size'     => $fileArray['size'][$i],
            ];
            $results[] = $this->uploadSingle($single);
        }

        $allSuccess = true;
        foreach ($results as $res) {
            if (!$res['success']) {
                $allSuccess = false;
                break;
            }
        }

        return [
            'success' => $allSuccess,
            'files'   => $results,
            'count'   => count($results)
        ];
    }

    /**
     * Sanitize filename.
     *
     * @param string $filename
     * @return string
     */
    protected function sanitizeFilename(string $filename): string
    {
        // Convert Turkish characters to ASCII
        $turkish = ['ç', 'Ç', 'ğ', 'Ğ', 'ı', 'İ', 'ö', 'Ö', 'ş', 'Ş', 'ü', 'Ü'];
        $ascii   = ['c', 'C', 'g', 'G', 'i', 'I', 'o', 'O', 's', 'S', 'u', 'U'];
        $filename = str_replace($turkish, $ascii, $filename);

        $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $filename);
        return trim(preg_replace('/_+/', '_', $filename), '_');
    }

    /**
     * Human readable PHP upload error messages.
     *
     * @param int $code
     * @return string
     */
    protected function getUploadErrorMessage(int $code): string
    {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                return 'Dosya php.ini upload_max_filesize limitini aşıyor.';
            case UPLOAD_ERR_FORM_SIZE:
                return 'Dosya form MAX_FILE_SIZE limitini aşıyor.';
            case UPLOAD_ERR_PARTIAL:
                return 'Dosya yalnızca kısmen yüklendi.';
            case UPLOAD_ERR_NO_FILE:
                return 'Hiçbir dosya yüklenmedi.';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Geçici klasör bulunamadı.';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Dosya diske yazılamadı.';
            case UPLOAD_ERR_EXTENSION:
                return 'PHP uzantısı dosya yüklemesini durdurdu.';
            default:
                return 'Bilinmeyen dosya yükleme hatası.';
        }
    }

    /**
     * Safely detect MIME type of a file with multiple fallbacks.
     *
     * @param string $filePath
     * @param string|null $clientMime
     * @return string
     */
    protected function detectMimeType(string $filePath, ?string $clientMime = null): string
    {
        // 1. Try finfo extension
        if (function_exists('finfo_open')) {
            $finfo = @finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo) {
                $mime = @finfo_file($finfo, $filePath);
                @finfo_close($finfo);
                if ($mime) {
                    return $mime;
                }
            }
        }

        // 2. Try mime_content_type
        if (function_exists('mime_content_type')) {
            $mime = @mime_content_type($filePath);
            if ($mime) {
                return $mime;
            }
        }

        // 3. Try getimagesize for images
        if (function_exists('getimagesize')) {
            $imageInfo = @getimagesize($filePath);
            if ($imageInfo && isset($imageInfo['mime'])) {
                return $imageInfo['mime'];
            }
        }

        // 4. Fallback to client provided MIME or default
        return $clientMime ?: 'application/octet-stream';
    }
}
