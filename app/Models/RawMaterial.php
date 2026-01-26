<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawMaterial extends Model
{
    protected $fillable = [
        "name", "name_arabic", "code", "type", "barcode_symbology", "brand_id", "category_id", "unit_id", "purchase_unit_id", "sale_unit_id", "cost", "price", "qty", "alert_quantity", "tax_id", "tax_method", "image", "file", "product_details", "is_active"
    ];

    public function category()
    {
        return $this->belongsTo('App\Models\Category');
    }

    public function brand()
    {
        return $this->belongsTo('App\Models\Brand');
    }

    public function tax()
    {
        return $this->belongsTo('App\Models\Tax');
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
