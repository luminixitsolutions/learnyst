<?php

namespace App\Support;

class Brand
{
    public static function name(): string
    {
        return (string) config('website.brand', config('app.name', 'StudyNest'));
    }

    public static function logoPath(): string
    {
        return (string) config('website.logo', 'images/studynest_logo.jpeg');
    }

    public static function logoUrl(): string
    {
        $path = ltrim(self::logoPath(), '/');

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '//')) {
            return $path;
        }

        return asset($path);
    }
}
