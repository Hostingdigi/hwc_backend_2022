<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
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
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf'];

        $extension = strtolower($file->getClientOriginalExtension());
        $detected = strtolower((string) $file->guessExtension());

        if (!in_array($extension, $allowed) || ($detected !== '' && !in_array($detected, $allowed))) {
            abort(422, 'Only JPG, PNG, GIF, WEBP or PDF files can be uploaded.');
        }

        $base = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'file';

        return time() . '_' . Str::limit($base, 80, '') . '.' . $extension;
    }

    /**
     * Quoted SQL string literal for "column LIKE <literal>" inside raw where clauses.
     */
    protected function sqlLike($value, $prefix = '%', $suffix = '%')
    {
        return DB::connection()->getPdo()->quote($prefix . $value . $suffix);
    }
}
