<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefundRequest extends Model
{
    use HasFactory;
//    public function scopeIsAdminApproval($query)
//    {
//        return $query->where('payment_status', 'paid');
//    }

    public function scopeIsAdminApproval($query)
    {
        return $query->where('admin_approval', 'pending');
    }


    public function refundItems(){
        return $this->hasOne(RefundItem::class,'refund_request_id','id');
    }
    public function order(){
        return $this->belongsTo(Order::class,'order_id','id');
    }
    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }
}
