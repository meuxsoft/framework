<?php

namespace Core\Libraries\Upload;

class Upload
{
    protected $uploadPath = 'uploads';
    protected $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf'];
    protected $allowedMimeTypes = [
        'jpg'  => ['image/jpeg', 'image/pjpeg'],
        'jpeg' => ['image/jpeg', 'image/pjpeg'],
        'png'  => ['image/png', 'image/x-png'],
        'gif'  => ['image/gif'],
        'webp' => ['image/webp'],
        'pdf'  => ['application/pdf'],
    ];

    protected $maxSize = 5242880;
    protected $randomizeName = true;
    protected $customName = null;
    protected $resizeEnabled = false;
    protected $resizeWidth = 0;
    protected $resizeHeight = 0;
    protected $resizeMode = 'fit';
    protected $imageQuality = 85;
    protected $thumbEnabled = false;
    protected $thumbWidth = 150;
    protected $thumbHeight = 150;
    protected $thumbPrefix = 'thumb_';
    protected $errors = [];

    protected function __construct()
    {
    }

    public static function setPath($path = null)
    {
        $instance = new self();
        if ($path !== null) {
            $instance->path($path);
        }
        return $instance;
    }

    public static function init($path = null)
    {
        return self::setPath($path);
    }

    public function path($path)
    {
        $this->uploadPath = trim(str_replace(['..', "\0"], '', $path), '/\\');
        return $this;
    }

    public function setAllowedTypes($types)
    {
        $this->allowedTypes = array_map('strtolower', $types);
        return $this;
    }

    public function setMaxSize($bytes)
    {
        $this->maxSize = $bytes;
        return $this;
    }

    public function randomize($randomize = true)
    {
        $this->randomizeName = $randomize;
        return $this;
    }

    public function setCustomName($name)
    {
        $this->customName = $name;
        return $this;
    }

    public function resize($width, $height = 0, $mode = 'fit', $quality = null)
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

    public function thumb($enable = true, $width = 150, $height = 150, $prefix = 'thumb_')
    {
        $this->thumbEnabled = $enable;
        $this->thumbWidth = $width;
        $this->thumbHeight = $height;
        $this->thumbPrefix = $prefix;
        return $this;
    }

    public function quality($quality)
    {
        $this->imageQuality = max(1, min(100, $quality));
        return $this;
    }

    public static function file($fileArray, $targetPath = 'uploads', $allowedTypes = ['jpg', 'jpeg', 'png', 'webp'])
    {
        return self::setPath($targetPath)->setAllowedTypes($allowedTypes)->upload($fileArray);
    }

    public function upload($fileArray)
    {
        $this->errors = [];

        if (isset($fileArray['name']) && is_array($fileArray['name'])) {
            return $this->uploadMultiple($fileArray);
        }

        return $this->uploadSingle($fileArray);
    }

    protected function uploadSingle($file)
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

        if ($file['size'] > $this->maxSize) {
            $maxMb = round($this->maxSize / 1048576, 2);
            return [
                'success' => false,
                'error'   => "Dosya boyutu çok büyük. Maksimum izin verilen: {$maxMb} MB.",
                'file'    => null
            ];
        }

        $origName = $file['name'];

        if (strpos($origName, "\0") !== false || strpos($origName, '..') !== false) {
            return [
                'success' => false,
                'error'   => 'Güvenlik ihlali: Geçersiz veya tehlikeli dosya adı.',
                'file'    => null
            ];
        }

        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

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

        if ($isImage && extension_loaded('gd')) {
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

            $imgSize = @getimagesize($destination);
            if ($imgSize) {
                $finalWidth = $imgSize[0];
                $finalHeight = $imgSize[1];
            }

            if ($this->thumbEnabled && $this->thumbWidth > 0) {
                $thumbFilename = $this->thumbPrefix . $finalFilename;
                $thumbDestination = $targetDir . '/' . $thumbFilename;

                $thumbCreated = $this->processImage(
                    $destination,
                    $thumbDestination,
                    $this->thumbWidth,
                    $this->thumbHeight,
                    'crop',
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

    public function processImage($sourcePath, $destPath, $targetWidth, $targetHeight = 0, $mode = 'fit', $quality = 85)
    {
        if (!file_exists($sourcePath)) {
            return false;
        }

        $imageInfo = @getimagesize($sourcePath);
        if (!$imageInfo) {
            return false;
        }

        [$origWidth, $origHeight, $imageType] = $imageInfo;

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

        $srcX = 0;
        $srcY = 0;
        $srcW = $origWidth;
        $srcH = $origHeight;

        $newW = $targetWidth;
        $newH = $targetHeight > 0 ? $targetHeight : $targetWidth;

        switch ($mode) {
            case 'crop':
            case 'cover':
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
                $newH = (int)round(($origHeight / $origWidth) * $newW);
                break;

            case 'height':
                $newW = (int)round(($origWidth / $origHeight) * $newH);
                break;

            case 'exact':
            case 'fixed':
                break;

            case 'fit':
            case 'auto':
            default:
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

        $canvas = imagecreatetruecolor($newW, $newH);

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

        imagecopyresampled($canvas, $sourceImage, 0, 0, $srcX, $srcY, $newW, $newH, $srcW, $srcH);

        $saved = false;
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $saved = imagejpeg($canvas, $destPath, $quality);
                break;
            case IMAGETYPE_PNG:
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

    protected function uploadMultiple($fileArray)
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

    protected function sanitizeFilename($filename)
    {
        $turkish = ['ç', 'Ç', 'ğ', 'Ğ', 'ı', 'İ', 'ö', 'Ö', 'ş', 'Ş', 'ü', 'Ü'];
        $ascii   = ['c', 'C', 'g', 'G', 'i', 'I', 'o', 'O', 's', 'S', 'u', 'U'];
        $filename = str_replace($turkish, $ascii, $filename);

        $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $filename);
        return trim(preg_replace('/_+/', '_', $filename), '_');
    }

    protected function getUploadErrorMessage($code)
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

    protected function detectMimeType($filePath, $clientMime = null)
    {
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

        if (function_exists('mime_content_type')) {
            $mime = @mime_content_type($filePath);
            if ($mime) {
                return $mime;
            }
        }

        if (function_exists('getimagesize')) {
            $imageInfo = @getimagesize($filePath);
            if ($imageInfo && isset($imageInfo['mime'])) {
                return $imageInfo['mime'];
            }
        }

        return $clientMime ?: 'application/octet-stream';
    }
}
