<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $primaryKey = 'customer_id';

    protected $fillable = [
      'name', 'address', 'phone_number', 'city', 'user_id'
    ];

    public function user(){
      return $this->belongsTo('App\Models\User', 'user_id', 'customer_id');
    }
}
