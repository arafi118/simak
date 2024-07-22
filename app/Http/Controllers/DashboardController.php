<?php

namespace App\Http\Controllers;

use App\Models\AdminInvoice;
use App\Models\AkunLevel1;
use App\Models\Desa;
use App\Models\Kecamatan;
use App\Models\Rekening;
use App\Models\Saldo;
use App\Models\Transaksi;
use App\Models\Usaha;
use App\Utils\Keuangan;
use App\Utils\Tanggal;
use Illuminate\Http\Request;
use Cookie;
use DB;
use Session;

class DashboardController extends Controller
{
    public function index()
    {
        $keuangan = new Keuangan;

        $usaha = Usaha::where('id', Session::get('lokasi'))->with([
            'd.kec.kabupaten'
        ])->first();
        if (Session::get('pesan')) {
            $this->sync(Session::get('lokasi'));
        }

        $rekening_kas = Rekening::where('kode_akun', 'LIKE', '1.1.01%')->with([
            'kom_saldo' => function ($query) {
                $query->where('tahun', date('Y'))->where(function ($query) {
                    $query->where('bulan', '0')->orwhere('bulan', date('m'));
                });
            },
            'saldo' => function ($query) {
                $query->where('tahun', date('Y'))->where('bulan', date('m') - 1);
            }
        ])->get();

        $data['saldo_kas'] = 0;
        $data['saldo_kas_lalu'] = 0;
        foreach ($rekening_kas as $rek) {
            $saldo = $keuangan->komSaldo($rek);

            $awal_debit = 0;
            $awal_kredit = 0;
            foreach ($rek->kom_saldo as $kom_saldo) {
                if ($kom_saldo->bulan == 0) {
                    $awal_debit = floatval($kom_saldo->debit);
                    $awal_kredit = floatval($kom_saldo->kredit);
                }
            }

            $saldo_debit = 0;
            $saldo_kredit = 0;
            if ($rek->saldo) {
                $saldo_debit = floatval($rek->saldo->debit);
                $saldo_kredit = floatval($rek->saldo->kredit);
            }

            if ($rek->lev1 == 1 || $rek->lev1 == '5') {
                $saldo_awal = $awal_debit - $awal_kredit;
                $saldo_bulan_lalu = $saldo_awal + ($saldo_debit - $saldo_kredit);
            } else {
                $saldo_awal = $awal_kredit - $awal_debit;
                $saldo_bulan_lalu = $saldo_awal + ($saldo_kredit - $saldo_debit);
            }

            $data['saldo_kas'] += $saldo;
            $data['saldo_kas_lalu'] += $saldo_bulan_lalu;
        }

        $tgl_pakai = $usaha->tgl_pakai;
        $tgl = date('Y-m-d');

        $data['user'] = auth()->user();
        $data['saldo'] = $this->_saldo($tgl);
        $data['jumlah_saldo'] = Saldo::where('kode_akun', 'NOT LIKE', $usaha->kd_desa . '%')->count();

        $data['api'] = env('APP_API', 'https://api-whatsapp.sidbm.net');
        $data['title'] = "Dashboard";
        return view('dashboard.index')->with($data);
    }

    public function sync($lokasi)
    {
        $tahun = date('Y');
        $bulan = date('m');
        $usaha = Usaha::where('id', Session::get('lokasi'))->with('d')->first();

        if (Saldo::where([['kode_akun', 'LIKE', '%' . $usaha->kd_desa . '%']])->count() <= 0) {
            $saldo_desa = [];

            $saldo_desa[] = [
                'id' => str_replace('.', '', $usaha->kd_desa) . $tahun . 0 . 1,
                'kode_akun' => $usaha->kd_desa,
                'tahun' => $tahun,
                'bulan' => 0,
                'debit' => 0,
                'kredit' => 0
            ];
            $saldo_desa[] = [
                'id' => str_replace('.', '', $usaha->kd_desa) . $tahun . 0 . 2,
                'kode_akun' => $usaha->kd_desa,
                'tahun' => $tahun,
                'bulan' => 0,
                'debit' => 0,
                'kredit' => 0
            ];
            $saldo_desa[] = [
                'id' => str_replace('.', '', $usaha->kd_desa) . $tahun . 0 . 3,
                'kode_akun' => $usaha->kd_desa,
                'tahun' => $tahun,
                'bulan' => 0,
                'debit' => 0,
                'kredit' => 0
            ];
            $saldo_desa[] = [
                'id' => str_replace('.', '', $usaha->kd_desa) . $tahun . 0 . 4,
                'kode_akun' => $usaha->kd_desa,
                'tahun' => $tahun,
                'bulan' => 0,
                'debit' => 0,
                'kredit' => 0
            ];
            $saldo_desa[] = [
                'id' => str_replace('.', '', $usaha->kd_desa) . $tahun . 0 . 5,
                'kode_akun' => $usaha->kd_desa,
                'tahun' => $tahun,
                'bulan' => 0,
                'debit' => 0,
                'kredit' => 0
            ];
            $saldo_desa[] = [
                'id' => str_replace('.', '', $usaha->kd_desa) . $tahun . 0 . 6,
                'kode_akun' => $usaha->kd_desa,
                'tahun' => $tahun,
                'bulan' => 0,
                'debit' => 0,
                'kredit' => 0
            ];

            Saldo::insert($saldo_desa);
        }

        $date = $tahun . '-' . $bulan . '-01';

        $saldo = Saldo::where([
            ['tahun', $tahun],
            ['bulan', $bulan]
        ])->with([
            'saldo' => function ($query) use ($tahun, $bulan) {
                $bulan = (($bulan - 1) < 1) ? 1 : $bulan - 1;

                $query->where([
                    ['tahun', $tahun],
                    ['bulan', $bulan]
                ]);
            }
        ])->orderBy('kode_akun', 'ASC')->get();

        $data_id = [];
        $insert = [];
        foreach ($saldo as $s) {
            $debit = 0;
            $kredit = 0;
            $debit_lalu = 0;
            $kredit_lalu = 0;

            if ($s->debit > 0) {
                $debit = $s->debit;
            }

            if ($s->kredit > 0) {
                $kredit = $s->kredit;
            }

            if ($s->saldo) {
                if ($s->saldo->debit > 0) {
                    $debit_lalu = $s->saldo->debit;
                }

                if ($s->saldo->kredit > 0) {
                    $kredit_lalu = $s->saldo->kredit;
                }
            }

            if ($debit < $debit_lalu || $kredit < $kredit_lalu) {
                $id = str_replace('.', '', $s->kode_akun) . $tahun . str_pad($bulan, 2, "0", STR_PAD_LEFT);
                $insert[] = [
                    'id' => $id,
                    'kode_akun' => $s->kode_akun,
                    'tahun' => $tahun,
                    'bulan' => $bulan,
                    'debit' => $debit_lalu,
                    'kredit' => $kredit_lalu
                ];

                $data_id[] = $id;
            }
        }

        if (count($insert) > 0) {
            Saldo::whereIn('id', $data_id)->delete();
            $query = Saldo::insert($insert);

            $update = Saldo::where([
                ['tahun', $tahun],
                ['bulan', '>', $bulan]
            ])->update([
                'debit' => 0,
                'kredit' => 0
            ]);
        }
    }

    private function _saldo($tgl)
    {
        $data = [
            '4' => [
                '1' => 0,
                '2' => 0,
                '3' => 0,
                '4' => 0,
                '5' => 0,
                '6' => 0,
                '7' => 0,
                '8' => 0,
                '9' => 0,
                '10' => 0,
                '11' => 0,
                '12' => 0,
            ],
            '5' => [
                '1' => 0,
                '2' => 0,
                '3' => 0,
                '4' => 0,
                '5' => 0,
                '6' => 0,
                '7' => 0,
                '8' => 0,
                '9' => 0,
                '10' => 0,
                '11' => 0,
                '12' => 0,
            ],
        ];

        $akun1 = AkunLevel1::where('lev1', '>=', '4')->with([
            'akun2',
            'akun2.akun3',
            'akun2.akun3.rek',
            'akun2.akun3.rek.kom_saldo' => function ($query) use ($tgl) {
                $tahun = date('Y', strtotime($tgl));
                $query->where([
                    ['tahun', $tahun],
                    ['bulan', '!=', '0'],
                    ['bulan', '!=', '13']
                ])->orderBy('kode_akun', 'ASC')->orderBy('bulan', 'ASC');
            },
        ])->get();

        foreach ($akun1 as $lev1) {
            $kom_saldo[$lev1->lev1] = $data[$lev1->lev1];
            foreach ($lev1->akun2 as $lev2) {
                foreach ($lev2->akun3 as $lev3) {
                    foreach ($lev3->rek as $rek) {
                        foreach ($rek->kom_saldo as $saldo) {
                            if ($lev1->lev1 == '5') {
                                $_saldo = $saldo->debit - $saldo->kredit;
                            } else {
                                $_saldo = $saldo->kredit - $saldo->debit;
                            }

                            $kom_saldo[$lev1->lev1][$saldo->bulan] += $_saldo;
                            if ($saldo->bulan > 1) {
                                if ($saldo->bulan > date('m')) {
                                    $kom_saldo[$lev1->lev1][$saldo->bulan] = 0;
                                }
                            }
                        }
                    }
                }
            }
        }

        $kom_saldo['surplus'] = [
            '1' => $kom_saldo['4']['1'] - $kom_saldo['5']['1'],
            '2' => $kom_saldo['4']['2'] - $kom_saldo['5']['2'],
            '3' => $kom_saldo['4']['3'] - $kom_saldo['5']['3'],
            '4' => $kom_saldo['4']['4'] - $kom_saldo['5']['4'],
            '5' => $kom_saldo['4']['5'] - $kom_saldo['5']['5'],
            '6' => $kom_saldo['4']['6'] - $kom_saldo['5']['6'],
            '7' => $kom_saldo['4']['7'] - $kom_saldo['5']['7'],
            '8' => $kom_saldo['4']['8'] - $kom_saldo['5']['8'],
            '9' => $kom_saldo['4']['9'] - $kom_saldo['5']['9'],
            '10' => $kom_saldo['4']['10'] - $kom_saldo['5']['10'],
            '11' => $kom_saldo['4']['11'] - $kom_saldo['5']['11'],
            '12' => $kom_saldo['4']['12'] - $kom_saldo['5']['12'],
        ];

        return $kom_saldo;
    }

    public function unpaid()
    {
        $invoice = AdminInvoice::where([
            ['lokasi', Session::get('lokasi')],
            ['status', 'UNPAID']
        ])->orderBy('tgl_invoice', 'DESC');

        $jumlah = 0;
        if ($invoice->count() > 0) {
            $jumlah = $invoice->count();
            $inv = $invoice->first();
        }

        return response()->json([
            'success' => true,
            'invoice' => $jumlah
        ]);
    }

    public function simpanSaldo()
    {
        $tahun = request()->get('tahun') ?: date('Y');
        $bulan = request()->get('bulan') ?: date('m');
        $kode_akun = request()->get('kode_akun') ?: '0';

        $kec = Kecamatan::where('id', Session::get('lokasi'))->with('desa')->first();

        $data_id = [];
        $saldo = [];
        if ($bulan == '00') {
            $tahun_tb = $tahun - 1;
            $tb = 'tb' . $tahun_tb;
            $tbk = 'tbk' . $tahun_tb;

            $rekening = Rekening::orderBy('kode_akun', 'ASC')->get();
            foreach ($rekening as $rek) {
                $saldo_debit = $rek->$tb;
                $saldo_kredit = $rek->$tbk;

                $id = str_replace('.', '', $rek->kode_akun) . $tahun . "00";
                $saldo[] = [
                    'id' => $id,
                    'kode_akun' => $rek->kode_akun,
                    'tahun' => $tahun,
                    'bulan' => 0,
                    'debit' => $saldo_debit,
                    'kredit' => $saldo_kredit
                ];

                $data_id[] = $id;
            }
        } else {
            $date = $tahun . '-' . $bulan . '-01';
            $tgl_kondisi = date('Y-m-t', strtotime($date));
            $rekening = Rekening::withSum([
                'trx_debit' => function ($query) use ($tgl_kondisi, $tahun) {
                    $query->whereBetween('tgl_transaksi', [$tahun . '-01-01', $tgl_kondisi]);
                }
            ], 'jumlah')->withSum([
                'trx_kredit' => function ($query) use ($tgl_kondisi, $tahun) {
                    $query->whereBetween('tgl_transaksi', [$tahun . '-01-01', $tgl_kondisi]);
                }
            ], 'jumlah')->orderBy('kode_akun', 'ASC');
            if ($kode_akun != '0') {
                $kode = explode(',', $kode_akun);
                $rekening = $rekening->whereIn('kode_akun', $kode);
            }

            $rekening = $rekening->get();

            foreach ($rekening as $rek) {
                $id = str_replace('.', '', $rek->kode_akun) . $tahun . str_pad($bulan, 2, "0", STR_PAD_LEFT);
                $saldo[] = [
                    'id' => $id,
                    'kode_akun' => $rek->kode_akun,
                    'tahun' => $tahun,
                    'bulan' => intval($bulan),
                    'debit' => $rek->trx_debit_sum_jumlah,
                    'kredit' => $rek->trx_kredit_sum_jumlah
                ];

                $data_id[] = $id;
            }
        }

        if ($bulan < 1) {
            $jumlah = Saldo::where([
                ['tahun', $tahun],
                ['bulan', '0']
            ])->whereRaw('LENGTH(kode_akun)=9')->count();

            if ($jumlah <= '0') {
                Saldo::whereIn('id', $data_id)->delete();
                $query = Saldo::insert($saldo);
            }
        } else {
            Saldo::whereIn('id', $data_id)->delete();
            $query = Saldo::insert($saldo);
        }

        $link = request()->url('');
        $query = request()->query();

        if (isset($query['bulan'])) {
            $query['bulan'] += 1;
        } else {
            $query['bulan'] = date('m') + 1;
        }
        if (!isset($query['tahun'])) {
            $query['tahun'] = date('Y');
        }

        $query['bulan'] = str_pad($query['bulan'], 2, '0', STR_PAD_LEFT);
        $next = $link . '?' . http_build_query($query);

        if (($kode_akun != '0' && $bulan >= date('m'))) {
            echo '<script>window.opener.postMessage("closed", "*"); window.close();</script>';
            exit;
        }

        if ($query['bulan'] < 13) {
            echo '<a href="' . $next . '" id="next"></a><script>document.querySelector("#next").click()</script>';
            exit;
        } else {
            echo '<script>window.opener.postMessage("closed", "*"); window.close();</script>';
            exit;
        }
    }
}
