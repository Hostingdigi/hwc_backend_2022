<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Turn a request value like "3,7,12" (or an array) into a comma-separated
     * list of integers that is safe to place inside a raw "IN (...)" clause.
     */
    protected function sqlIdList($value)
    {
        $ids = is_array($value) ? $value : explode(',', (string) $value);
        $ids = array_filter(array_map('intval', $ids), function ($id) {
            return $id > 0;
        });

        return $ids ? implode(',', array_unique($ids)) : '0';
    }

    /**
     * Safe file name for an upload moved into public/uploads. Only image and PDF
     * extensions are accepted (checked against the file name and the detected
     * content type), and the name is reduced to [a-z0-9-] so nothing executable
     * or path-like can land in the public folder.
     */
    protected function safeUploadName($file)
    {
        // extension => content types it may have (detected from the file's bytes)
        $allowed = [
            'jpg' => ['image/jpeg'],
            'jpeg' => ['image/jpeg'],
            'png' => ['image/png'],
            'gif' => ['image/gif'],
            'webp' => ['image/webp'],
            'pdf' => ['application/pdf'],
        ];

        $extension = strtolower($file->getClientOriginalExtension());
        $detected = strtolower((string) $file->getMimeType());

        if (!isset($allowed[$extension]) || !in_array($detected, $allowed[$extension])) {
            abort(422, 'Only JPG, PNG, GIF, WEBP or PDF files can be uploaded.');
        }

        $base = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'file';

        return time() . '_' . Str::limit($base, 80, '') . '.' . $extension;
    }

    /**
     * Stores an upload on the "uploads" disk (public/uploads) under the folder of
     * $destinationPath, e.g. public_path('uploads/brands') -> "brands".
     * Name the file with safeUploadName() first.
     */
    protected function storeUpload($file, $destinationPath, $filename)
    {
        $folder = trim(str_replace('\\', '/', substr($destinationPath, strlen(public_path('uploads')))), '/');

        Storage::disk('uploads')->putFileAs($folder, $file, $filename);
    }

    /**
     * Quoted SQL string literal for "column = <literal>" inside raw where clauses.
     */
    protected function sqlQuote($value)
    {
        return DB::connection()->getPdo()->quote((string) $value);
    }

    /**
     * Quoted SQL string literal for "column LIKE <literal>" inside raw where clauses.
     */
    protected function sqlLike($value, $prefix = '%', $suffix = '%')
    {
        return DB::connection()->getPdo()->quote($prefix . $value . $suffix);
    }
}
