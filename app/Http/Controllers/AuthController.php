<?php

namespace App\Http\Controllers;

use App\Models\AdminInvoice;
use App\Models\AdminJenisPembayaran;
use App\Models\Desa;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Menu;
use App\Models\Usaha;
use App\Models\User;
use App\Models\Wilayah;
use App\Utils\Keuangan;
use App\Utils\Tanggal;
use Auth;
use Cookie;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Validator;
use Session;

class AuthController extends Controller
{
    public function index()
    {
        $keuangan = new Keuangan;
        if ($keuangan->startWith(request()->getHost(), 'master.sidbm')) {
            return redirect('/master');
        }

        $usaha = Usaha::where('domain', explode('//', request()->url(''))[1])->orwhere('domain_alt', explode('//', request()->url(''))[1])->with([
            'd',
            'd.sebutan_desa',
            'd.kec'
        ])->first();

        $logo = '/assets/img/icon/favicon.png';
        if ($usaha->logo) {
            $logo = '/storage/logo/' . $usaha->logo;
        }

        return view('auth.login')->with(compact('usaha', 'logo'));
    }

    public function register()
    {
        $usaha = Usaha::where('domain', request()->getHost())->orwhere('domain_alt', request()->getHost())->with([
            'd',
            'd.sebutan_desa',
            'd.kec'
        ])->first();

        if ($usaha->id != '1') {
            abort(404);
        }

        $logo = '/assets/img/icon/favicon.png';
        if ($usaha->logo) {
            $logo = '/storage/logo/' . $usaha->logo;
        }

        return view('auth.register')->with(compact('usaha', 'logo'));
    }

    public function provinsi()
    {
        $wilayah = Wilayah::whereRaw("LENGTH(kode)=2")->orderBy('nama', 'ASC')->get();
        return response()->json([
            'success' => true,
            'data' => $wilayah
        ]);
    }

    public function kabupaten($kode)
    {
        $wilayah = Wilayah::whereRaw("LENGTH(kode)=5")->where('kode', 'LIKE', $kode . '%')->orderBy('nama', 'ASC')->get();
        return response()->json([
            'success' => true,
            'data' => $wilayah
        ]);
    }

    public function kecamatan($kode)
    {
        $wilayah = Wilayah::whereRaw("LENGTH(kode)=8")->where('kode', 'LIKE', $kode . '%')->orderBy('nama', 'ASC')->get();
        return response()->json([
            'success' => true,
            'data' => $wilayah
        ]);
    }

    public function desa($kode)
    {
        $wilayah = Wilayah::whereRaw("LENGTH(kode)=13")->where('kode', 'LIKE', $kode . '%')->orderBy('nama', 'ASC')->get();
        return response()->json([
            'success' => true,
            'data' => $wilayah
        ]);
    }

    public function login(Request $request)
    {
        $url = $request->getHost();
        $username = htmlspecialchars($request->username);
        $password = $request->password;

        $validate = $this->validate($request, [
            'username' => 'required',
            'password' => 'required'
        ]);

        $usaha = Usaha::where('domain', $url)->orwhere('domain_alt', $url)->with([
            'd.kec.kabupaten'
        ])->first();

        $kec = $usaha->d->kec;
        $lokasi = $usaha->id;

        $icon = '/assets/img/icon/favicon.png';
        if ($usaha->logo) {
            $icon = '/storage/logo/' . $usaha->logo;
        }

        $user = User::where([
            ['uname', $username],
            ['lokasi', $lokasi]
        ])->first();

        if ($user) {
            if ($password === $user->pass) {
                if (Auth::loginUsingId($user->id)) {
                    $inv = $this->generateInvoice($usaha);
                    $request->session()->regenerate();

                    session([
                        'nama_usaha' => $usaha->nama_usaha,
                        'nama' => $user->namadepan . ' ' . $user->namabelakang,
                        'foto' => $user->foto,
                        'logo' => $usaha->logo,
                        'lokasi' => $usaha->id,
                        'usaha' => $user->usaha,
                        'lokasi_user' => $user->lokasi,
                        'icon' => $icon,
                    ]);

                    return redirect('/dashboard')->with([
                        'pesan' => 'Selamat Datang ' . $user->namadepan . ' ' . $user->namabelakang,
                        'invoice' => $inv['invoice'],
                        'msg' => $inv['msg'],
                        'hp_dir' => $inv['dir'],
                    ]);
                }
            }
        }

        return redirect()->back();
    }

    public function store(Request $request)
    {
        $data = $request->only([
            "provinsi",
            "kabupaten",
            "kecamatan",
            "desa",
            "nama_usaha",
            "tgl_register",
            "alamat",
            "email",
            "telpon",
        ]);

        $validate = Validator::make($data, [
            "provinsi" => 'required',
            "kabupaten" => 'required',
            "kecamatan" => 'required',
            "desa" => 'required',
            "nama_usaha" => 'required',
            "tgl_register" => 'required',
            "alamat" => 'required',
            "email" => 'required',
            "telpon" => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), Response::HTTP_MOVED_PERMANENTLY);
        }

        $kab = Kabupaten::where([
            ['kd_kab', $data['kabupaten']],
            ['kd_prov', $data['provinsi']]
        ])->first();
        if (!$kab) {
            $kabupaten = Wilayah::where('kode', $data['kabupaten'])->first();

            $nama_kab = $kabupaten->nama;
            $nama_kab = ucwords(strtolower(str_replace('KAB. ', '', $nama_kab)));
            Kabupaten::insert([
                "kd_prov" => $data['provinsi'],
                "kd_kab" => $data['kabupaten'],
                "nama_kab" => $nama_kab,
                "nama_lembaga" => $nama_kab,
                "alamat_kab" => "-",
                "telpon_kab" => "0",
                "email_kab" => "-",
                "web_kab" => "-",
                "web_kab_alternatif" => "-",
                "uname" => "-",
                "pass" => "-",
            ]);
        }

        $kec = Kecamatan::where([
            ['kd_kec', $data['kecamatan']],
            ['kd_kab', $data['kabupaten']]
        ])->first();
        if (!$kec) {
            $kecamatan = Wilayah::where('kode', $data['kecamatan'])->first();

            $kec = Kecamatan::insert([
                "kd_kab" => $data['kabupaten'],
                "kd_kec" => $data['kecamatan'],
                "nama_kec" => $kecamatan->nama,
                "alamat_kec" => "-",
                "telpon_kec" => "0",
                "email_kec" => "-",
                "web_kec" => "-",
                "web_alternatif" => "-",
                "logo" => "-",
                "uname" => "-",
                "pass" => "-",
            ]);
        }

        $desa = Desa::where([
            ['kd_desa', $data['desa']],
            ['kd_kec', $data['kecamatan']],
        ])->first();
        if (!$desa) {
            $desa = Wilayah::where('kode', $data['desa'])->first();

            Desa::insert([
                "kd_kec" => $data['kecamatan'],
                "nama_kec" => $kec->nama_kec,
                "kd_desa" => str_replace('.', '', $data['desa']),
                "nama_desa" => $desa->nama,
                "alamat_desa" => "-",
                "telp_desa" => "-",
                "sebutan" => "1",
                "kode_desa" => $data['desa'],
                "kades" => "-",
                "pangkat" => "-",
                "nip" => "-",
                "no_kades" => "-",
                "sekdes" => "-",
                "no_sekdes" => "-",
                "ked" => "-",
                "no_ked" => "-",
                "deskripsi_desa" => "-",
                "uname" => "-",
                "pass" => "-",
            ]);
        }

        $domain = str_replace('BUMDES', '', strtoupper($data['nama_usaha']));
        $domain = str_replace('BUMDESMA', '', strtoupper($domain));
        $domain = str_replace(' ', '-', $domain);

        $usaha = Usaha::create([
            "kd_desa" => $data['desa'],
            "nama_usaha" => $data['nama_usaha'],
            "kepala_lembaga" => "-",
            "badan_pengawas" => "-",
            "kabag_administrasi" => "-",
            "kabag_keuangan" => "-",
            "bkk_bkm_bm" => "-",
            "npwp" => "-",
            "tgl_npwp" => date('Y-m-d'),
            "nomor_bh" => "-",
            "alamat" => $data['alamat'],
            "email" => $data['email'],
            "telpon" => $data['telpon'],
            "domain" => $domain . '.akubumdes.id',
            "domain_alt" => $domain . '.siupk.net',
            "logo" => "-",
            "background" => "-",
            "tgl_register" => $data['tgl_register'],
            "tgl_pakai" => $data['tgl_register'],
            "biaya" => "0",
            "peraturan_desa" => "-",
        ]);

        Session::put('lokasi', $usaha->id);
        return redirect('/register/user');
    }

    public function user()
    {
        User::create([
            "namadepan" => "Direktur",
            "namabelakang" => "",
            "ins" => "DR",
            "jk" => "",
            "tempat_lahir" => "",
            "tgl_lahir" => date('Y-m-d'),
            "alamat" => "",
            "hp" => "",
            "nik" => "",
            "pendidikan" => "1",
            "jabatan" => "1",
            "level" => "1",
            "usaha" => Session::get('lokasi'),
            "lokasi" => Session::get('lokasi'),
            "foto" => "",
            "status" => "1",
            "uname" => "Direktur",
            "pass" => "Direktur",
            "hak_akses" => "",
        ]);

        User::create([
            "namadepan" => "Sekretaris",
            "namabelakang" => "",
            "ins" => "SK",
            "jk" => "",
            "tempat_lahir" => "",
            "tgl_lahir" => date('Y-m-d'),
            "alamat" => "",
            "hp" => "",
            "nik" => "",
            "pendidikan" => "1",
            "jabatan" => "2",
            "level" => "1",
            "usaha" => Session::get('lokasi'),
            "lokasi" => Session::get('lokasi'),
            "foto" => "",
            "status" => "1",
            "uname" => "Sekretaris",
            "pass" => "Sekretaris",
            "hak_akses" => "",
        ]);

        User::create([
            "namadepan" => "Bendahara",
            "namabelakang" => "",
            "ins" => "SK",
            "jk" => "",
            "tempat_lahir" => "",
            "tgl_lahir" => date('Y-m-d'),
            "alamat" => "",
            "hp" => "",
            "nik" => "",
            "pendidikan" => "1",
            "jabatan" => "3",
            "level" => "1",
            "usaha" => Session::get('lokasi'),
            "lokasi" => Session::get('lokasi'),
            "foto" => "",
            "status" => "1",
            "uname" => "Bendahara",
            "pass" => "Bendahara",
            "hak_akses" => "",
        ]);

        return redirect('/register')->with('sucess', 'Register berhasil');
    }

    public function logout(Request $request)
    {
        $user = auth()->user()->namadepan . ' ' . auth()->user()->namabelakang;
        FacadesAuth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('pesan', 'Terima Kasih ' . $user);
    }

    private function generateInvoice($usaha)
    {
        $kec = $usaha->d->kec;
        $return = [
            'invoice' => false,
            'msg' => '',
            'dir' => ''
        ];

        $bulan_pakai = date('m-d', strtotime($usaha->tgl_pakai));
        $tgl_pakai = date('Y') . '-' . $bulan_pakai;

        $tgl_invoice = date('Y-m-d', strtotime('-1 month', strtotime($tgl_pakai)));

        $invoice = AdminInvoice::where([
            ['lokasi', $usaha->id],
            ['jenis_pembayaran', '2']
        ])->whereBetween('tgl_invoice', [$tgl_invoice, $tgl_pakai]);

        $pesan = "";
        if ($invoice->count() <= 0 && (date('Y-m-d') <= $tgl_pakai && date('Y-m-d') >= $tgl_invoice)) {
            $tanggal = date('Y-m-d');
            $nomor_invoice = date('ymd', strtotime($tanggal));
            $invoice = AdminInvoice::where('tgl_invoice', $tanggal)->count();
            $nomor_urut = str_pad($invoice + 1, '2', '0', STR_PAD_LEFT);
            $nomor_invoice .= $nomor_urut;

            $invoice = AdminInvoice::create([
                'lokasi' => $usaha->id,
                'nomor' => $nomor_invoice,
                'jenis_pembayaran' => 2,
                'tgl_invoice' => date('Y-m-d'),
                'tgl_lunas' => date('Y-m-d'),
                'status' => 'UNPAID',
                'jumlah' => $usaha->biaya,
                'id_user' => 1
            ]);

            $jenis_pembayaran = AdminJenisPembayaran::where('id', '2')->first();
            $pesan .= "_#Invoice - " . str_pad($usaha->id, '3', '0', STR_PAD_LEFT) . " " . $usaha->nama_usaha . " - " . $usaha->d->nama_desa . "_\n";
            $pesan .= $jenis_pembayaran->nama_jp . "\n";
            $pesan .= "Jumlah           : Rp. " . number_format($usaha->biaya) . "\n";
            $pesan .= "Jatuh Tempo  : " . Tanggal::tglIndo($tgl_pakai) . "\n\n";
            $pesan .= "*Detail Invoice*\n";
            $pesan .= "_https://" . $usaha->domain_alt . "/" . $invoice->id . "_";

            $return['invoice'] = true;
            $return['msg'] = $pesan;

            $dir = User::where([
                ['usaha', $usaha->id],
                ['lokasi', $usaha->id],
                ['jabatan', '1'],
                ['level', '1']
            ])->first();

            $return['dir'] = $dir->hp;
        }

        return $return;
    }
}
