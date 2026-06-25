<?php

namespace App\Support;

use Illuminate\Support\Str;

class BranchNameGenerator
{
    public static function fromTitle(string $title): string
    {
        $slug = Str::slug($title, '-');

        if ($slug === '') {
            return 'task';
        }

        return Str::limit($slug, 60, '');
    }
}
