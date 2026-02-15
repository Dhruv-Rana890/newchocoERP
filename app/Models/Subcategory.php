<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    protected $fillable = [
        'category_id',
        'subcate_banner_img',
        'image',
        'name_english',
        'name_arabic',
        'slug',
        'sort_order',
        'description_english',
        'description_arabic',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * Subcategory belongs to a product category.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
