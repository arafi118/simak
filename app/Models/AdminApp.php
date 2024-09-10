<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminApp extends Model
{
    use HasFactory;

    protected $table = 'admin_app';
    public $timestamps = false;

    protected $guarded = ['id'];
}
