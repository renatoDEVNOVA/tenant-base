<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Models\Domain as ModelsDomain;

class Domain extends ModelsDomain
{
    use HasFactory;

    // public function booted(){
    //     // static::creating(function ($domain){
    //     //     $domain->domain = $domain . '.' . config('tenancy.central_domains')[0];
    //     // });
    // }
}
