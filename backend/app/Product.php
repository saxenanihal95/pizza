<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Order;

class Product extends Model
{
    protected $table = 'product';
    protected $primaryKey = 'product_id';

    public function orders()
    {
        return $this->belongsToMany('App\Order', 'order_product', 'product_id', 'order_id');
    }
}
