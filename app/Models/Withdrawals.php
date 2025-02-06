<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdrawals extends Model
{
    use HasFactory;

    protected $fillable = [
        'mobile',
        'account_num',
        'ifsc_code',
        'branch',
        'amount',
        'bank',
        'datetime',
    ];

    public function users()
    {
        return $this->belongsTo(Users::class, 'user_id');
    }
    public function upi()
{
    return $this->hasOne(Upis::class, 'user_id', 'user_id');
}

}
