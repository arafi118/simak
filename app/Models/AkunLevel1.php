<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Session;

class AkunLevel1 extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $primaryKey = 'kode_akun';

    protected $keyType = 'string';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'akun_level_1';

        if (Session::has('jenis_akun')) {
            if (Session::get('jenis_akun') == 7) {
                $this->table = 'akun_level_1s';
            }

            if (Session::get('jenis_akun') == '8') {
                $this->table = 'akun_level_1_koperasi';
            }
        }

        if (Session::has('lokasi')) {
            $usaha = Usaha::find(Session::get('lokasi'));

            if ($usaha && $usaha->jenis_akun == 7) {
                $this->table = 'akun_level_1s';
                Session::put('jenis_akun', 7);
            }

        }
    }

    public function akun2()
    {
        return $this->hasMany(AkunLevel2::class, 'parent_id', 'id')->orderBy('kode_akun', 'ASC');
    }

    public function saldo_awal()
    {
        return $this->belongsTo(Saldo::class, 'kode_akun', 'kode_akun')->where('bulan', '12')->orderBy('id', 'ASC');
    }

    public function saldo()
    {
        return $this->belongsTo(Saldo::class, 'kode_akun', 'kode_akun');
    }
}
