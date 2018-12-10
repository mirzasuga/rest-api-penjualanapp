<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $primaryKey = 'product_code';
    protected $fillable = [
      'product_code', 'user_id', 'name', 'category_id', 'stock', 'buy_price', 'sell_price', 'unit'
    ];

    public function user(){
      return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function categories(){
      return $this->belongsTo('App\Models\Categories', 'category_id');
    }

    public function orderdetail(){
      return $this->hasMany('App\Models\OrderDetail', 'product_code');
    }
}
