<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
class user_active_companies extends Pivot
{
    use HasFactory;

    public function users() {
        return $this->hasOne(User::class,'id','user_id');
    }

    public function company() {
        return $this->hasOne(User::class,'id','company_id');
    }
}
