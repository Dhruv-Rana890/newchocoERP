<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable =[
        "customer_group_id", "user_id", "name", "company_name",
        "email", "type", "phone_number", "mobile_number_2", "wa_number", "tax_no", "address", "area", "house_number", "street", "ave", "block", "city",
        "state", "postal_code", "country", "opening_balance", "credit_limit", "points", "deposit", "pay_term_no","pay_term_period", "expense", "wishlist", "is_active"
    ];

    public function customerGroup()
    {
        return $this->belongsTo('App\Models\CustomerGroup');
    }

    public function user()
    {
    	return $this->belongsTo('App\Models\User');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function discountPlans()
    {
        return $this->belongsToMany('App\Models\DiscountPlan', 'discount_plan_customers');
    }

    public function points(){
        return $this->hasMany(Point::class,'customer_id');
    }
}
