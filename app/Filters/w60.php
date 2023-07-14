<?php

namespace App\Filters;

use Intervention\Image\Image;
use Intervention\Image\Filters\FilterInterface;

class w60 implements FilterInterface
{
    public function applyFilter(Image $image)
    {
        return $image->resize(60, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
    }
}