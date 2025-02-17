<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    public function reviewImage(){
        return $this->hasMany(ReviewImage::class,'review_id','id');
    }

    public function user(){
        return $this->hasOne(User::class,'id','user_id');
    }
    public function product(){
        return $this->belongsTo(Product::class,'product_id','id');
    }
}
