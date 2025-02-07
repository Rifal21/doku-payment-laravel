<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $fillable = ['invoice_number', 'user_id', 'cart_items', 'total_price', 'status'];
    public function items()
    {
        return $this->hasMany(TransactionItems::class , 'transaction_id');
    }

}
