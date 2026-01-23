<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AkunLevel2s extends Model
{
    protected $table = 'akun_level_2s';
    public $timestamps = false;

    public function level3()
    {
        return $this->hasMany(AkunLevel3s::class,'parent_id','id')->orderBy('kode_akun');
    }
}
