<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AkunLevel1s extends Model
{
    protected $table = 'akun_level_1s';
    public $timestamps = false;

    public function level2()
    {
        return $this->hasMany(AkunLevel2s::class,'parent_id','id')->orderBy('kode_akun');
    }
}
