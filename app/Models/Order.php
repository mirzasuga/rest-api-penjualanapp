<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
	protected $primaryKey = 'order_id';
	protected $fillable = [
		'product_code', 'product_amount', 'total_price', 'user_id', 'supplier_id'
	];

    public function supplier(){
    	return $this->belongsTo('App\Models\Supplier', 'supplier_id');
    }

    public function user(){
      return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function orderdetail(){
        return $this->hasMany('App\Models\OrderDetail','order_id','order_id');
    }
}
