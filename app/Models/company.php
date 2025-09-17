<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class company extends Model
{
    use HasFactory, SoftDeletes;

    public function users() {
    return $this->hasOne(User::class,'id','user_id');
    }

    public function ActiveCompany() {
    return $this->hasOne(user_active_companies::class,'company_id','id');
    }

    
}
