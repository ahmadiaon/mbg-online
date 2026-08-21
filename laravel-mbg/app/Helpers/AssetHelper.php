<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AssetHelper
{
    /**
     * Upload file ke Assets Server
     *
     * Database cukup menyimpan nama file
     *
     * @param UploadedFile|string $file
     * @param string $folder
     * @param string|null $filename
     * @return string
     */
    public static function upload(
        $file,
        string $folder = 'general',
        ?string $filename = null
    ): string {

        /*
        |--------------------------------------------------------------------------
        | File
        |--------------------------------------------------------------------------
        */

        if ($file instanceof UploadedFile) {

            if (!$file->isValid()) {
                throw new \RuntimeException(
                    'File upload tidak valid.'
                );
            }

            $filePath = $file->getPathname();

            $originalName =
                $file->getClientOriginalName();

        } else {

            if (
                !is_string($file) ||
                !is_file($file)
            ) {
                throw new \RuntimeException(
                    'File tidak ditemukan.'
                );
            }

            $filePath = $file;

            $originalName =
                basename($file);
        }


        /*
        |--------------------------------------------------------------------------
        | Filename
        |--------------------------------------------------------------------------
        */

        if (!$filename) {

            $filename =
                $originalName;

        }


        /*
        |--------------------------------------------------------------------------
        | Sanitize filename
        |--------------------------------------------------------------------------
        */

        $filename = preg_replace(
            '/[^a-zA-Z0-9\.\-_]/',
            '_',
            $filename
        );


        /*
        |--------------------------------------------------------------------------
        | Sanitize folder
        |--------------------------------------------------------------------------
        */

        $folder =
            trim($folder, '/');


        $folder = preg_replace(
            '#\.\.+#',
            '',
            $folder
        );


        $folder = preg_replace(
            '#[^a-zA-Z0-9/_\-]#',
            '_',
            $folder
        );


        /*
        |--------------------------------------------------------------------------
        | Assets Server
        |--------------------------------------------------------------------------
        */

        $assetsUrl =
            rtrim(
                env('ASSETS_API_URL'),
                '/'
            );


        /*
        |--------------------------------------------------------------------------
        | Upload
        |--------------------------------------------------------------------------
        */

        $response =
            Http::timeout(120)
                ->withHeaders([
                    'X-API-Token' =>
                        env('ASSETS_API_TOKEN'),
                ])
                ->attach(
                    'file',
                    fopen($filePath, 'r'),
                    $filename
                )
                ->post(
                    $assetsUrl . '/upload',
                    [
                        'filename' =>
                            $filename,

                        'folder' =>
                            $folder,
                    ]
                );


        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        if (!$response->successful()) {

            throw new \RuntimeException(
                'Upload asset gagal. ' .
                'HTTP ' .
                $response->status() .
                ': ' .
                $response->body()
            );

        }


        $result =
            $response->json();


        if (
            !isset($result['success']) ||
            !$result['success']
        ) {

            throw new \RuntimeException(
                'Assets Server menolak upload.'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Return filename
        |--------------------------------------------------------------------------
        */

        return $filename;
    }


    /**
     * Upload dengan nama UUID
     *
     * Contoh:
     *
     * 550e8400-e29b-41d4-a716-446655440000.jpg
     */
    public static function uploadUuid(
        $file,
        string $folder = 'general'
    ): string {

        /*
        |--------------------------------------------------------------------------
        | Extension
        |--------------------------------------------------------------------------
        */

        if ($file instanceof UploadedFile) {

            $extension =
                $file->getClientOriginalExtension();

        } else {

            $extension =
                pathinfo(
                    $file,
                    PATHINFO_EXTENSION
                );
        }


        /*
        |--------------------------------------------------------------------------
        | UUID
        |--------------------------------------------------------------------------
        */

        $filename =
            (string) Str::uuid();


        if ($extension) {

            $filename .=
                '.' .
                strtolower($extension);

        }


        return self::upload(
            $file,
            $folder,
            $filename
        );
    }


    /**
     * Membuat URL file dari folder + filename
     *
     * Database tetap menyimpan filename saja.
     */
    public static function url(
        string $folder,
        ?string $filename
    ): ?string {

        if (
            empty($filename)
        ) {
            return null;
        }


        $baseUrl =
            rtrim(
                env('ASSETS_API_URL'),
                '/'
            );


        $folder =
            trim($folder, '/');


        return
            $baseUrl .
            '/uploads/' .
            $folder .
            '/' .
            rawurlencode($filename);
    }
}