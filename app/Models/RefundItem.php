<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefundItem extends Model
{
    use HasFactory;
    public function orderItem(){
        return $this->belongsTo(OrderItem::class,'order_item_id','id');
    }
}
