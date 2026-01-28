<?php

namespace App\Models;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Session;

class AkunLevel2 extends Model
{
    use HasFactory, Compoships;

    protected $table = 'sub_akun';

    public $timestamps = false;

    protected $primaryKey = 'kode_akun';

    protected $keyType = 'string';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'akun_level_2';

        if (Session::has('jenis_akun')) {
            if (Session::get('jenis_akun') == 7) {
                $this->table = 'akun_level_2s';
            }

            if (Session::get('jenis_akun') == '8') {
                $this->table = 'akun_level_2_koperasi';
            }
        }

        if (Session::has('lokasi')) {
            $usaha = Usaha::find(Session::get('lokasi'));

            if ($usaha && $usaha->jenis_akun == 7) {
                $this->table = 'akun_level_2s';
                Session::put('jenis_akun', 7);
            }

            // dd([

            //     'lokasi'     => Session::get('lokasi'),
            //     'usaha_id'   => $usaha->id ?? null,
            //     'usaha_jenis'=> $usaha->jenis_akun ?? null,
            //     'table'      => $this->table,
            // ]);
        }
    }

    public function akun3()
    {
        return $this->hasMany(AkunLevel3::class, 'parent_id', 'id')->orderBy('kode_akun', 'ASC');
    }

    public function rek()
    {
        return $this->hasMany(Rekening::class, ['lev1', 'lev2'], ['lev1', 'lev2']);
    }
}
