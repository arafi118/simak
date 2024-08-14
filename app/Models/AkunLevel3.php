<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Session;

class AkunLevel3 extends Model
{
    use HasFactory;

    protected $table;
    public $timestamps = false;

    protected $primaryKey = 'kode_akun';
    protected $keyType = 'string';

    public function __construct()
    {
        $this->table = 'akun_' . Session::get('lokasi');
    }

    public function rek()
    {
        return $this->hasMany(Rekening::class, 'parent_id', 'id')->orderBy('kode_akun', 'ASC');
    }
}
