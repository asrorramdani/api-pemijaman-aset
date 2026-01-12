<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Order extends Model
{
    protected $appends = ['total_price', 'total_price_formatted'];

 protected $fillable = [
    'user_id',
    'borrow_date',
    'return_date',
    'status',
    'total'
];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

public function items()
{
    return $this->hasMany(OrderItem::class);
}

public function orderItems()
{
    return $this->hasMany(OrderItem::class);
}

public function getTotalPriceAttribute()
{
    return $this->items->sum(function ($item) {
        return $item->price * $item->quantity;
    });
}

public function getTotalPriceFormattedAttribute()
{
    return number_format($this->total_price, 0, ',', '.');
}

public function calculateTotal()
{
    $total = $this->orderItems()->sum(
        DB::raw('price * quantity')
    );

    $this->update([
        'total_price' => $total
    ]);
}

}
