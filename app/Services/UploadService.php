<?php

class UploadService
{
    private static $allowedExtensions = [
        'jpg',
        'jpeg',
        'png',
        'gif',
        'webp',
        'pdf',
        'doc',
        'docx',
        'xls',
        'xlsx',
        'txt',
        'zip',
        'rar'
    ];

    private static $allowedMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',

        'application/pdf',

        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',

        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',

        'text/plain',

        'application/zip',
        'application/x-zip-compressed',
        'application/vnd.rar',
        'application/x-rar-compressed',
        'application/octet-stream'
    ];

    private static $blockedExtensions = [
        'php',
        'php3',
        'php4',
        'php5',
        'phtml',
        'js',
        'exe',
        'bat',
        'cmd',
        'sh',
        'msi',
        'html',
        'htm'
    ];

    private static $maxSize = 5242880; // 5MB

    public static function uploadMultiple($files, $folder)
    {
        $uploadedFiles = [];

        $actualFileCount = 0;

        foreach ($files['name'] as $index => $name) {
            if ($files['error'][$index] !== UPLOAD_ERR_NO_FILE) {
                $actualFileCount++;
            }
        }

        if ($actualFileCount > 3) {
            throw new Exception("Maximum 3 files can be uploaded.");
        }

        foreach ($files['name'] as $index => $name) {

            if ($files['error'][$index] === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            if ($files['error'][$index] !== UPLOAD_ERR_OK) {
                throw new Exception("File upload failed.");
            }

            $file = [
                'name' => $files['name'][$index],
                'type' => $files['type'][$index],
                'tmp_name' => $files['tmp_name'][$index],
                'error' => $files['error'][$index],
                'size' => $files['size'][$index]
            ];

            $uploadedFiles[] = self::uploadSingle($file, $folder);
        }

        return $uploadedFiles;
    }

    private static function uploadSingle($file, $folder)
    {
        $originalName = $file['name'];
        $fileSize = $file['size'];

        if ($fileSize > self::$maxSize) {
            throw new Exception("File size must be less than 5MB.");
        }

        $extension = strtolower(
            pathinfo($originalName, PATHINFO_EXTENSION)
        );

        if (empty($extension)) {
            throw new Exception("Invalid file extension.");
        }

        if (in_array($extension, self::$blockedExtensions)) {
            throw new Exception("This file type is not allowed.");
        }

        if (!in_array($extension, self::$allowedExtensions)) {
            throw new Exception("Only images, PDF, Word, Excel, text and compressed files are allowed.");
        }

        $mimeType = mime_content_type($file['tmp_name']);

        if (!in_array($mimeType, self::$allowedMimeTypes)) {
            throw new Exception("Invalid file type detected.");
        }

        $safeOriginalName = preg_replace(
            "/[^a-zA-Z0-9\.\-_]/",
            "_",
            $originalName
        );

        $storedName =
            bin2hex(random_bytes(16)) .
            "_" .
            time() .
            "." .
            $extension;

        $basePath = realpath(__DIR__ . "/../../storage/uploads");

        if (!$basePath) {
            throw new Exception("Upload directory not found.");
        }

        $targetFolder = $basePath . DIRECTORY_SEPARATOR . $folder;

        if (!is_dir($targetFolder)) {
            mkdir($targetFolder, 0755, true);
        }

        $targetPath =
            $targetFolder .
            DIRECTORY_SEPARATOR .
            $storedName;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new Exception("Unable to save uploaded file.");
        }

        return [
            'original_name' => $safeOriginalName,
            'stored_name' => $storedName,
            'file_path' => "storage/uploads/" . $folder . "/" . $storedName,
            'file_type' => $mimeType,
            'file_size' => $fileSize
        ];
    }
}
