<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    protected $primaryKey = 'category_id';
    public $timestamps = false;
    protected $fillable = [
      'name'
    ];

    public function product(){
      return $this->hasMany('App\Models\Product', 'category_id');
    }
}
