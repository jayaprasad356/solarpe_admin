<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bankdetails extends Model
{
    protected $fillable = ['mobile', 'ifsc','account_num','holder_name','bank','branch'];
}
