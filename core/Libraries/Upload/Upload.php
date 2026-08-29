<?php

namespace Core\Libraries\Upload;

use RuntimeException;

class Upload
{
    /**
     * @var string
     */
    protected $uploadPath = 'public/uploads';

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
     * @var array
     */
    protected $errors = [];

    /**
     * Protected constructor. Use static builder.
     */
    protected function __construct()
    {
    }

    /**
     * Initialize upload fluent builder.
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
     * Set target upload directory.
     *
     * @param string $path
     * @return $this
     */
    public function path(string $path): self
    {
        $this->uploadPath = trim($path, '/\\');
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
     * Set custom filename.
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
     * Static direct upload shortcut.
     *
     * @param array $fileArray
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
     * Upload a single file.
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

        // Check file extension
        $origName = $file['name'];
        
        // Security 1: Null byte injection & Path Traversal in filename
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

        // Security 4: Prevent upload directory traversal
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

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return [
                'success' => false,
                'error'   => 'Dosya hedef dizine taşınamadı.',
                'file'    => null
            ];
        }

        return [
            'success'       => true,
            'filename'      => $finalFilename,
            'original_name' => $origName,
            'extension'     => $ext,
            'mime'          => $mime,
            'size'          => $file['size'],
            'path'          => $relativePath,
            'url'           => '/' . ltrim($relativePath, '/'),
            'error'         => null
        ];
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
        $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $filename);
        return trim($filename, '_');
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
