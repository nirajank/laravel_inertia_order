<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $casts = [
        'billing' => 'array', // Casts the 'data' attribute to an array
        'shipping' => 'array',
    ];

    protected $fillable = [
        'number',
        'order_key',
        'status',
        'date_created',
        'total',
        'customer_id',
        'customer_note',
        'billing',
        'shipping',
        'order_id'
    ];

    public function insertOrUpdateArrayBilling(array $data)
    {
        // Prepare the data (optional)
        // ...

        $this->updateOrCreate([], [ // Empty array as first argument for insert
            'billing' => $data,
        ]);
    }

    public function insertOrUpdateArrayShipping(array $data)
    {
        // Prepare the data (optional)
        // ...

        $this->updateOrCreate([], [ // Empty array as first argument for insert
            'shipping' => $data,
        ]);
    }
}
