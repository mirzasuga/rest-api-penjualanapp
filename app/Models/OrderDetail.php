<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{	
	public $timestamps = false;
    protected $fillable = [
    	'order_id', 'product_code', 'product_amount', 'buy_price', 'subtotal_price'
    ];

    //jumlah harga jumlah*harga
    public function getSubtotalAttribute()
    {
        return number_format($this->product_amount * $this->buy_price);
    }

    public function order(){
    	return $this->belongsTo('App\Models\Order', 'order_id');
    }

    public function product(){
        return $this->belongsTo('App\Models\Product');
    }
}
