<?php

namespace App\Http\Controllers;

use App\Models\AdminInvoice;
use App\Models\AdminJenisPembayaran;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Menu;
use App\Models\Usaha;
use App\Models\User;
use App\Utils\Keuangan;
use App\Utils\Tanggal;
use Auth;
use Cookie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;
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
