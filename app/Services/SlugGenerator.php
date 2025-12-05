<?php

namespace App\Services;

use Illuminate\Support\Str;

class SlugGenerator
{
    /**
     * Generate a unique slug for a model based on the given title.
     *
     * @param  string  $modelClass  The Eloquent model class (FQCN)
     * @param  string  $title       The source title to slugify
     * @param  int|null $ignoreId   Optional model id to ignore when checking uniqueness
     * @return string
     */
    public function generate(string $modelClass, string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 1;

        while (true) {
            $query = $modelClass::where('slug', $slug);
            if ($ignoreId !== null) {
                $query->where('id', '!=', $ignoreId);
            }

            if (! $query->exists()) {
                break;
            }

            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}
