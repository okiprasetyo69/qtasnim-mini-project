<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'item_id', 
        'transaction_date', 
        'total_item_sold', 
    ];

    public function items(){
        return $this->belongsTo(Items::class, 'id');
    }

    
}
