<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Basement_Warehouse extends Model
{
    protected $table = 'basement_warehouse';
    
    protected $fillable = [
        "basement_id", "warehouse_id", "qty"
    ];

    /**
     * Find basement warehouse record by basement_id and warehouse_id
     */
    public function scopeFindBasementWarehouse($query, $basement_id, $warehouse_id)
    {
        return $query->where([
            ['basement_id', $basement_id],
            ['warehouse_id', $warehouse_id]
        ]);
    }

    /**
     * Get or create basement warehouse record
     * Returns the record, creating it with qty=0 if it doesn't exist
     */
    public static function getOrCreate($basement_id, $warehouse_id)
    {
        $record = self::findBasementWarehouse($basement_id, $warehouse_id)->first();
        if (!$record) {
            $record = self::create([
                'basement_id' => $basement_id,
                'warehouse_id' => $warehouse_id,
                'qty' => 0
            ]);
        }
        return $record;
    }
}
