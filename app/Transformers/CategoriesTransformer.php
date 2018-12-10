<?php

namespace App\Transformers;

use App\Models\Categories;
use League\Fractal\TransformerAbstract;

class CategoriesTransformer extends TransformerAbstract
{	

    public function transform(Categories $category)
    {
        return [
            'category_id' => $category->category_id,
            'name' => $category->name,
        ];
    }
}