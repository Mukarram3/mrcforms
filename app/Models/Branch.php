<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    public function cities(){
        return $this->belongsToMany(City::class,BranchCity::class,'branch_id','city_id');
    }
}
