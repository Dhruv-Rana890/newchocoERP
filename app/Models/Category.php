<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable =[

        "name", 'image', "parent_id", "is_active", "type", "is_sync_disable", "woocommerce_category_id","slug","featured","show_in_menu","menu_sort_order","page_title","short_description"
    ];

    public function product()
    {
    	return $this->hasMany('App\Models\Product');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Category has many subcategories (product menu only).
     */
    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }
}
