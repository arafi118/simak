<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AkunLevel3s extends Model
{
    protected $table = 'akun_level_3s';
    public $timestamps = false;

    
    public function accounts()
    {
        return $this->hasMany(Accounts::class,'kode_akun','kode_akun');
    }
}
