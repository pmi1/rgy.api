<?php

namespace App\Filters;

use Intervention\Image\Image;
use Intervention\Image\Filters\FilterInterface;

class w200 implements FilterInterface
{
    public function applyFilter(Image $image)
    {
        return $image->resize(200, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
    }
}