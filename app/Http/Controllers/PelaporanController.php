<?php

namespace App\Http\Controllers;

use App\Models\AdminInvoice;
use App\Models\AkunLevel1;
use App\Models\AkunLevel2;
use App\Models\AkunLevel3;
use App\Models\ArusKas;
use App\Models\Calk;
use App\Models\Desa;
use App\Models\JenisLaporan;
use App\Models\JenisLaporanPinjaman;
use App\Models\JenisProdukPinjaman;
use App\Models\Kecamatan;
use App\Models\Kelompok;
use App\Models\MasterArusKas;
use App\Models\PinjamanKelompok;
use App\Models\Rekening;
use App\Models\Saldo;
use App\Models\Transaksi;
use App\Models\Usaha;
use App\Models\User;
use App\Utils\Keuangan;
use App\Utils\Tanggal;
use DB;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use PDF;
use Session;

class PelaporanController extends Controller
{
    public function index()
    {
        $usaha = Usaha::where('id', Session::get('lokasi'))->first();
        $laporan = JenisLaporan::where([['file', '!=', '0']])->orderBy('urut', 'ASC')->get();

        $title = 'Pelaporan';
        return view('pelaporan.index')->with(compact('title', 'usaha', 'laporan'));
    }

    public function subLaporan($file)
    {
        if ($file == 3) {
            $rekening = Rekening::orderBy('kode_akun', 'ASC')->get();
            return view('pelaporan.partials.sub_laporan')->with(compact('file', 'rekening'));
        }

        if ($file == 'calk') {
            $tahun = request()->get('tahun');
            $bulan = request()->get('bulan');

            $calk = Calk::where([
                ['lokasi', Session::get('lokasi')],
                ['tanggal', 'LIKE', $tahun . '-' . $bulan . '%']
            ])->first();

            $keterangan = '';
            if ($calk) {
                $keterangan = $calk->catatan;
            }

            return view('pelaporan.partials.sub_laporan')->with(compact('file', 'keterangan'));
        }

        if ($file == 5) {
            $jenis_laporan = JenisLaporanPinjaman::where('file', '!=', '0')->orderBy('urut', 'ASC')->get();

            return view('pelaporan.partials.sub_laporan')->with(compact('file', 'jenis_laporan'));
        }

        if ($file == 14) {
            $data = [
                0 => [
                    'title' => '01. Januari - Maret',
                    'id' => '1,2,3'
                ],
                1 => [
                    'title' => '02. April - Juni',
                    'id' => '4,5,6'
                ],
                2 => [
                    'title' => '03. Juli - September',
                    'id' => '7,8,9'
                ],
                3 => [
                    'title' => '04. Oktober - Desember',
                    'id' => '10,11,12'
                ]
            ];

            return view('pelaporan.partials.sub_laporan')->with(compact('file', 'data'));
        }

        if ($file == 'tutup_buku') {
            $data = [
                0 => [
                    'title' => 'Pengalokasian Laba',
                    'file' => 'alokasi_laba'
                ],
                1 => [
                    'title' => 'Jurnal Tutup Buku',
                    'file' => 'jurnal_tutup_buku'
                ],
                2 => [
                    'title' => 'Neraca',
                    'file' => 'neraca_tutup_buku'
                ],
                3 => [
                    'title' => 'Laba Rugi',
                    'file' => 'laba_rugi_tutup_buku'
                ],
                4 => [
                    'title' => 'CALK',
                    'file' => 'CALK_tutup_buku'
                ]
            ];

            return view('pelaporan.partials.sub_laporan')->with(compact('file', 'data'));
        }

        return view('pelaporan.partials.sub_laporan')->with(compact('file'));
    }

    public function preview(Request $request, $lokasi = null)
    {
        if ($lokasi != null) {
            Session::put('lokasi', $lokasi);
        }

        $data = $request->only([
            'tahun',
            'bulan',
            'hari',
            'laporan',
            'sub_laporan',
            'type'
        ]);

        if ($data['laporan'] == 'calk' && strlen($data['sub_laporan']) > 22) {
            Calk::where([
                ['lokasi', Session::get('lokasi')],
                ['tanggal', 'LIKE', $data['tahun'] . '-' . $data['bulan'] . '%']
            ])->delete();

            Calk::create([
                'lokasi' => Session::get('lokasi'),
                'tanggal' => $data['tahun'] . '-' . $data['bulan'] . '-01',
                'catatan' => $data['sub_laporan'],
            ]);
        }

        $request->hari = ($request->hari) ?: 31;
        $usaha = Usaha::where('id', Session::get('lokasi'))->with([
            'd',
            'd.sebutan_desa',
            'd.kec.kabupaten',
            'ttd'
        ])->first();
        $kab = $usaha->d->kec->kabupaten;

        $jabatan = '1';
        $level = '1';

        $dir = User::where([
            ['lokasi', Session::get('lokasi')],
            ['jabatan', $jabatan],
            ['level', $level]
        ])->first();

        $data['logo'] = $usaha->logo;
        $data['nama_usaha'] = $usaha->nama_usaha . ' ' . $usaha->d->nama_desa;
        $data['nama_kecamatan'] = $usaha->d->kec->sebutan_kec . ' ' . $usaha->d->kec->nama_kec;

        if (Keuangan::startWith($kab->nama_kab, 'KOTA') || Keuangan::startWith($kab->nama_kab, 'KAB')) {
            $data['nama_kecamatan'] .= ' ' . ucwords(strtolower($kab->nama_kab));
            $data['nama_kabupaten'] = ucwords(strtolower($kab->nama_kab));
        } else {
            $data['nama_kecamatan'] .= ' Kabupaten ' . ucwords(strtolower($kab->nama_kab));
            $data['nama_kabupaten'] = ' Kabupaten ' . ucwords(strtolower($kab->nama_kab));
        }

        $data['nomor_usaha'] = 'SK Kemenkumham RI No.' . $usaha->nomor_bh;
        $data['info'] = $usaha->alamat . ', Telp.' . $usaha->telpon;
        $data['email'] = $usaha->email;

        $data['usaha'] = $usaha;
        $data['kec'] = $usaha->d->kec;
        $data['kab'] = $kab;
        $data['dir'] = $dir;

        if ($data['tahun'] == null) {
            abort(404);
        }

        $data['bulanan'] = true;
        if ($data['bulan'] == null) {
            $data['bulanan'] = false;
            $data['bulan'] = '12';
        }

        $data['harian'] = true;
        if ($data['hari'] == null) {
            $data['harian'] = false;
            $data['hari'] = date('t', strtotime($data['tahun'] . '-' . $data['bulan'] . '-01'));
        }

        $data['tgl_kondisi'] = $data['tahun'] . '-' . $data['bulan'] . '-' . $data['hari'];
        $data['tanggal_kondisi'] = $usaha->d->kec->nama_kec . ', ' . Tanggal::tglLatin($data['tgl_kondisi']);

        $data['nama_hari'] = Tanggal::namaHari($data['tgl_kondisi']);
        $data['nama_bulan'] = Tanggal::namaBulan($data['tgl_kondisi']);

        $file = $request->laporan;
        if ($file == 3) {
            $laporan = explode('_', $request->sub_laporan);
            $file = $laporan[0];

            $data['kode_akun'] = $laporan[1];
            $data['laporan'] = 'buku_besar ' . $laporan[1];
            return $this->$file($data);
        } elseif ($file == 5) {
            $file = $request->sub_laporan;
            $data['laporan'] = $file;
            return $this->$file($data);
        } elseif ($file == 14) {
            $laporan = explode('_', $request->sub_laporan);
            $file = $laporan[0];

            $data['sub'] = $laporan[1];
            $data['laporan'] = 'E - Budgeting ';
            return $this->$file($data);
        } elseif ($file == 'tutup_buku') {
            $file = $request->sub_laporan;;
            return $this->$file($data);
        } else {
            return $this->$file($data);
        }
    }

    private function cover(array $data)
    {
        $thn = $data['tahun'];
        $bln = $data['bulan'];
        $hari = $data['hari'];

        $tgl = $thn . '-' . $bln . '-' . $hari;
        $data['judul'] = 'Laporan Keuangan';
        $data['sub_judul'] = 'Tahun ' . Tanggal::tahun($tgl);
        if ($data['bulanan']) {
            $data['judul'] = 'Laporan Keuangan';
            $data['sub_judul'] = 'Bulan ' . Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
        }

        $view = view('pelaporan.view.cover', $data)->render();

        if ($data['type'] == 'pdf') {
            $pdf = PDF::loadHTML($view);
            return $pdf->stream();
        } else {
            return $view;
        }
    }

    private function surat_pengantar(array $data)
    {
        $thn = $data['tahun'];
        $bln = $data['bulan'];
        $hari = $data['hari'];

        if (strlen($hari) > 0 && strlen($bln) > 0) {
            $tgl = $thn . '-' . $bln . '-' . $hari;
            $data['judul'] = 'Laporan Harian';
            $data['sub_judul'] = 'Tanggal ' . Tanggal::tglLatin($tgl);
            $data['tgl'] = Tanggal::tglLatin($tgl);
        } elseif (strlen($bln) > 0) {
            $tgl = $thn . '-' . $bln . '-' . $hari;
            $data['judul'] = 'Laporan Bulanan';
            $data['sub_judul'] = 'Tanggal ' . Tanggal::tglLatin(date('Y-m-t', strtotime($thn . '-' . $bln . '-01')));
            $data['tgl'] = Tanggal::tglLatin(date('Y-m-t', strtotime($thn . '-' . $bln . '-01')));
        } else {
            $tgl = $thn . '-' . $bln . '-' . $hari;
            $data['judul'] = 'Laporan Tahunan';
            $data['sub_judul'] = 'Tahun ' . Tanggal::tahun($tgl);
            $data['tgl'] = Tanggal::tahun($tgl);
        }

        $data['dir_utama'] = User::where([
            ['level', '2'],
            ['jabatan', '65'],
            ['lokasi', Session::get('lokasi')],
        ])->first();

        $view = view('pelaporan.view.surat_pengantar', $data)->render();

        if ($data['type'] == 'pdf') {
            $pdf = PDF::loadHTML($view);
            return $pdf->stream();
        } else {
            return $view;
        }
    }

    private function neraca(array $data)
    {
        $keuangan = new Keuangan;

        $thn = $data['tahun'];
        $bln = $data['bulan'];
        $hari = ($data['hari']);

        if ($bln == '1' && $hari == '1') {
            return $this->neraca_tutup_buku($data);
        }

        $tgl = $thn . '-' . $bln . '-' . $hari;
        $data['sub_judul'] = 'Per ' . date('t', strtotime($tgl)) . ' ' . Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
        $data['tgl'] = Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);

        $data['debit'] = 0;
        $data['kredit'] = 0;

        $data['akun1'] = AkunLevel1::where('lev1', '<=', '3')->with([
            'akun2',
            'akun2.akun3',
            'akun2.akun3.rek',
            'akun2.akun3.rek.kom_saldo' => function ($query) use ($data) {
                $query->where('tahun', $data['tahun'])->where(function ($query) use ($data) {
                    $query->where('bulan', '0')->orwhere('bulan', $data['bulan']);
                });
            },
        ])->orderBy('kode_akun', 'ASC')->get();

        $view = view('pelaporan.view.neraca', $data)->render();

        if ($data['type'] == 'pdf') {
            $pdf = PDF::loadHTML($view);
            return $pdf->stream();
        } else {
            return $view;
        }
    }

    private function laba_rugi(array $data)
    {
        $keuangan = new Keuangan;

        $thn = $data['tahun'];
        $bln = $data['bulan'];
        $hari = $data['hari'];
        $awal_tahun = $thn . '-01-01';

        if ($bln == '1' && $hari == '1') {
            return $this->laba_rugi_tutup_buku($data);
        }

        $tgl = $thn . '-' . $bln . '-' . $hari;
        if ($data['bulanan']) {
            $data['sub_judul'] = 'Periode ' . Tanggal::tglLatin($thn . '-01-01') . ' S.D ' . Tanggal::tglLatin($data['tgl_kondisi']);
            $data['tgl'] = Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
            $data['bulan_lalu'] = date('Y-m-t', strtotime('-1 month', strtotime($thn . '-' . $bln . '-10')));
            $data['header_lalu'] = 'Bulan Lalu';
            $data['header_sekarang'] = 'Bulan Ini';
        } else {
            $data['sub_judul'] = 'Periode ' . Tanggal::tglLatin($awal_tahun) . ' S.D ' . Tanggal::tglLatin($data['tgl_kondisi']);
            $data['tgl'] = Tanggal::tahun($tgl);
            $data['bulan_lalu'] = ($thn - 1) . '-12-31';
            $data['header_lalu'] = 'Tahun Lalu';
            $data['header_sekarang'] = 'Tahun Ini';
        }

        $jenis = 'Tahunan';
        if ($data['bulanan']) {
            $jenis = 'Bulanan';
        }


        $laba_rugi = $keuangan->laporan_laba_rugi($data['tgl_kondisi'], $jenis);
        $data['pph'] = $keuangan->beban_pajak($data['tgl_kondisi'], $jenis);
        $data['pendapatan'] = $laba_rugi['pendapatan'];
        $data['beban'] = $laba_rugi['beban'];
        $data['pendapatanNOP'] = $laba_rugi['pendapatan_non_ops'];
        $data['bebanNOP'] = $laba_rugi['beban_non_ops'];

        $view = view('pelaporan.view.laba_rugi', $data)->render();

        if ($data['type'] == 'pdf') {
            $pdf = PDF::loadHTML($view);
            return $pdf->stream();
        } else {
            return $view;
        }
    }

    private function arus_kas(array $data)
    {
        $keuangan = new Keuangan;

        $thn = $data['tahun'];
        $bln = $data['bulan'];
        $hari = $data['hari'];

        $tgl = $thn . '-' . $bln . '-' . $hari;
        $tgl = $thn . '-' . $bln . '-' . $hari;
        $data['sub_judul'] = 'Tahun ' . Tanggal::tahun($tgl);
        $data['tgl'] = Tanggal::tahun($tgl);
        $data['jenis'] = 'Tahunan';
        $tgl_lalu = ($thn - 1) . '-00-00';
        if ($data['bulanan']) {
            $data['sub_judul'] = 'Bulan ' . Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
            $data['tgl'] = Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
            $data['jenis'] = 'Bulanan';

            $bulan_lalu = $bln - 1;
            if ($bulan_lalu <= 0) {
                $bulan_lalu = 12;
                $thn -= 1;
            }

            $tgl_lalu = $thn . '-' . $bulan_lalu . '-' . date('t', strtotime($thn . '-' . $bulan_lalu . '-01'));
        }

        $data['keuangan'] = $keuangan;

        $tanggal = explode('-', $data['tgl_kondisi']);
        $thn = $tanggal[0];
        $bln = $tanggal[1];
        $tgl = $tanggal[2];

        $data['tgl_awal'] = $thn . '-' . $bln . '-01';
        $data['arus_kas'] = MasterArusKas::with([
            'child',
            'child.rek_debit.rek.trx_debit' => function ($query) use ($data) {
                $query->whereBetween('tgl_transaksi', [$data['tgl_awal'], $data['tgl_kondisi']])->where(function ($query) use ($data) {
                    $query->where('rekening_kredit', 'LIKE', '1.1.01%')->orwhere('rekening_kredit', 'LIKE', '1.1.02%');
                });
            },
            'child.rek_kredit.rek.trx_kredit' => function ($query) use ($data) {
                $query->whereBetween('tgl_transaksi', [$data['tgl_awal'], $data['tgl_kondisi']])->where(function ($query) use ($data) {
                    $query->where('rekening_debit', 'LIKE', '1.1.01%')->orwhere('rekening_debit', 'LIKE', '1.1.02%');
                });
            }
        ])->where('parent_id', '0')->get();

        $data['saldo_bulan_lalu'] = $keuangan->saldoKas($tgl_lalu);
        // $data['arus_kas'] = ArusKas::where('sub', '0')->with('child')->orderBy('id', 'ASC')->get();

        $view = view('pelaporan.view.arus_kas', $data)->render();

        if ($data['type'] == 'pdf') {
            $pdf = PDF::loadHTML($view);
            return $pdf->stream();
        } else {
            return $view;
        }
    }

    private function LPM(array $data)
    {
        $data['laporan'] = 'Laporan Perubahan Ekuitas';
        $keuangan = new Keuangan;

        $thn = $data['tahun'];
        $bln = $data['bulan'];
        $hari = $data['hari'];

        $tgl = $thn . '-' . $bln . '-' . $hari;
        $data['sub_judul'] = 'Tahun ' . Tanggal::tahun($tgl);
        $data['tgl'] = Tanggal::tahun($tgl);
        if ($data['bulanan']) {
            $data['sub_judul'] = 'Bulan ' . Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
            $data['tgl'] = Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
        }

        $data['keuangan'] = $keuangan;
        $data['rekening'] = Rekening::where('lev1', '3')->with([
            'kom_saldo' => function ($query) use ($data) {
                $query->where('tahun', $data['tahun'])->where(function ($query) use ($data) {
                    $query->where('bulan', '0')->orwhere('bulan', $data['bulan']);
                });
            }
        ])->get();

        $view = view('pelaporan.view.perubahan_modal', $data)->render();

        if ($data['type'] == 'pdf') {
            $pdf = PDF::loadHTML($view);
            return $pdf->stream();
        } else {
            return $view;
        }
    }

    private function CALK(array $data)
    {
        $keuangan = new Keuangan;

        $thn = $data['tahun'];
        $bln = $data['bulan'];
        $hari = $data['hari'];

        $trx = Transaksi::where([
            ['keterangan_transaksi', 'LIKE', '%tahun ' . $data['tahun'] - 1],
            ['rekening_debit', '3.2.01.01']
        ])->first();

        $data['tgl_mad'] = $data['tgl_kondisi'];
        if ($trx) {
            $data['tgl_mad'] = $trx->tgl_transaksi;
        }

        if ($bln == '1' && $hari == '1') {
            return $this->CALK_tutup_buku($data);
        }

        $tgl = $thn . '-' . $bln . '-' . $hari;
        $data['tgl'] = Tanggal::tahun($tgl);
        $data['nama_tgl'] = 'Tahun ' . $thn;
        $data['sub_judul'] = 'Tahun ' . $thn;
        if ($data['bulanan']) {
            $data['tgl'] = Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
            $data['nama_tgl'] = 'Bulan ' . Tanggal::namaBulan($tgl) . ' Tahun ' . $thn;
            $data['sub_judul'] = 'Bulan ' . Tanggal::namaBulan($tgl) . ' Tahun ' . $thn;
        }

        $data['debit'] = 0;
        $data['kredit'] = 0;

        $data['akun1'] = AkunLevel1::where('lev1', '<=', '3')->with([
            'akun2',
            'akun2.akun3',
            'akun2.akun3.rek',
            'akun2.akun3.rek.kom_saldo' => function ($query) use ($data) {
                $query->where('tahun', $data['tahun'])->where(function ($query) use ($data) {
                    $query->where('bulan', '0')->orwhere('bulan', $data['bulan']);
                });
            },
        ])->orderBy('kode_akun', 'ASC')->get();

        $data['keterangan'] = Calk::where([
            ['lokasi', Session::get('lokasi')],
            ['tanggal', 'LIKE', $data['tahun'] . '-' . $data['bulan'] . '%']
        ])->first();

        $data['sekr'] = User::where([
            ['level', '1'],
            ['jabatan', '2'],
            ['lokasi', Session::get('lokasi')],
        ])->first();

        $data['bend'] = User::where([
            ['level', '1'],
            ['jabatan', '3'],
            ['lokasi', Session::get('lokasi')],
        ])->first();

        $data['pengawas'] = User::where([
            ['level', '3'],
            ['jabatan', '1'],
            ['lokasi', Session::get('lokasi')],
        ])->first();

        $data['dir_utama'] = User::where([
            ['level', '2'],
            ['jabatan', '65'],
            ['lokasi', Session::get('lokasi')],
        ])->first();

        $data['saldo_calk'] = Saldo::where([
            ['kode_akun', $data['kec']->kd_kec],
            ['tahun', $thn]
        ])->get();
        $view = view('pelaporan.view.calk', $data)->render();

        if ($data['type'] == 'pdf') {
            $pdf = PDF::loadHTML($view);
            return $pdf->stream();
        } else {
            return $view;
        }
    }

    private function jurnal_transaksi(array $data)
    {
        $thn = $data['tahun'];
        $bln = $data['bulan'];
        $hari = $data['hari'];

        if ($bln == '1' && $hari == '1') {
            return $this->jurnal_tutup_buku($data);
        }

        $tgl = $thn . '-' . $bln . '-' . $hari;
        if (!$data['bulanan']) {
            $data['sub_judul'] = 'Tahun ' . Tanggal::tahun($tgl);
            $data['tgl'] = Tanggal::tahun($tgl);
            $data['transaksi'] = Transaksi::whereBetween('tgl_transaksi', [
                $thn . '-01-01',
                $thn . '-12-31'
            ])->where(function ($query) {
                $query->where('rekening_debit', '!=', '0')->orwhere('rekening_kredit', '!=', '0');
            })->with('user', 'rek_debit', 'rek_kredit', 'angs', 'angs.rek_debit', 'angs.rek_kredit')->orderBy('tgl_transaksi', 'ASC')->orderBy('idt', 'ASC')->get();
        } else {
            if (!$data['harian']) {
                $data['sub_judul'] = 'Bulan ' . Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
                $data['tgl'] = Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
                $data['transaksi'] = Transaksi::whereBetween('tgl_transaksi', [
                    $thn . '-' . $bln . '-01',
                    $thn . '-' . $bln . '-' . date('t', strtotime($thn . '-' . $bln . '-01'))
                ])->where(function ($query) {
                    $query->where('rekening_debit', '!=', '0')->orwhere('rekening_kredit', '!=', '0');
                })->with('user', 'rek_debit', 'rek_kredit', 'angs', 'angs.rek_debit', 'angs.rek_kredit')->orderBy('tgl_transaksi', 'ASC')->orderBy('idt', 'ASC')->get();
            } else {
                $data['sub_judul'] = 'Tanggal ' . $hari . ' Bulan ' . Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
                $data['tgl'] = Tanggal::tglLatin($tgl);
                $data['transaksi'] = Transaksi::where('tgl_transaksi', $tgl)->where(function ($query) {
                    $query->where('rekening_debit', '!=', '0')->orwhere('rekening_kredit', '!=', '0');
                })->with('user', 'rek_debit', 'rek_kredit', 'angs', 'angs.rek_debit', 'angs.rek_kredit')->orderBy('tgl_transaksi', 'ASC')->orderBy('idt', 'ASC')->get();
            }
        }

        $view = view('pelaporan.view.jurnal_transaksi', $data)->render();

        if ($data['type'] == 'pdf') {
            $pdf = PDF::loadHTML($view);
            return $pdf->stream();
        } else {
            return $view;
        }
    }

    private function BB(array $data)
    {
        $keuangan = new Keuangan;

        $thn = $data['tahun'];
        $bln = $data['bulan'];
        $hari = $data['hari'];

        $tgl = $thn . '-' . $bln . '-' . $hari;
        $tgl = $thn . '-';
        $data['judul'] = 'Laporan Tahunan';
        $data['sub_judul'] = 'Tahun ' . Tanggal::tahun($tgl);
        $data['tgl'] = Tanggal::tahun($tgl);
        $awal_bulan = $thn . '00-00';
        if ($data['bulanan']) {
            $tgl = $thn . '-' . $bln . '-';
            $data['judul'] = 'Laporan Bulanan';
            $data['sub_judul'] = 'Bulan ' . Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
            $data['tgl'] = Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
            $bulan_lalu = date('m', strtotime('-1 month', strtotime($tgl . '01')));
            $awal_bulan = $thn . '-' . $bulan_lalu . '-' . date('t', strtotime($thn . '-' . $bulan_lalu));
            if ($bln == 1) {
                $awal_bulan = $thn . '00-00';
            }
        }

        if ($data['harian']) {
            $tgl = $thn . '-' . $bln . '-' . $hari;
            $data['judul'] = 'Laporan Harian';
            $data['sub_judul'] = 'Tanggal ' . $hari . ' ' . Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
            $data['tgl'] = Tanggal::tglLatin($tgl);
            $awal_bulan = $tgl;
            if ($tgl != $thn . '-01-01') {
                $awal_bulan = date('Y-m-d', strtotime('-1 day', strtotime($tgl)));
            }
        }

        $data['rek'] = Rekening::where('kode_akun', $data['kode_akun'])->first();
        $data['transaksi'] = Transaksi::where('tgl_transaksi', 'LIKE', '%' . $tgl . '%')->where(function ($query) use ($data) {
            $query->where('rekening_debit', $data['kode_akun'])->orwhere('rekening_kredit', $data['kode_akun']);
        })->with('user')->orderBy('tgl_transaksi', 'ASC')->orderBy('urutan', 'ASC')->orderBy('idt', 'ASC')->get();

        $data['saldo'] = $keuangan->saldoAwal($data['tgl_kondisi'], $data['kode_akun']);
        $data['d_bulan_lalu'] = $keuangan->saldoD($awal_bulan, $data['kode_akun']);
        $data['k_bulan_lalu'] = $keuangan->saldoK($awal_bulan, $data['kode_akun']);

        if ($tgl == $thn . '-01-01') {
            $data['d_bulan_lalu'] = '0';
            $data['k_bulan_lalu'] = '0';
        }

        $view = view('pelaporan.view.buku_besar', $data)->render();

        if ($data['type'] == 'pdf') {
            $pdf = PDF::loadHTML($view);
            return $pdf->stream();
        } else {
            return $view;
        }
    }

    private function neraca_saldo(array $data)
    {
        $keuangan = new Keuangan;

        $thn = $data['tahun'];
        $bln = $data['bulan'];
        $hari = $data['hari'];

        $tgl = $thn . '-' . $bln . '-' . $hari;
        $data['sub_judul'] = 'Tahun ' . Tanggal::tahun($tgl);
        $data['tgl'] = Tanggal::tahun($tgl);
        if ($data['bulanan']) {
            $data['sub_judul'] = 'Bulan ' . Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
            $data['tgl'] = Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
        }

        $data['keuangan'] = $keuangan;
        $data['rekening'] = Rekening::orderBy('kode_akun', 'ASC')->with([
            'kom_saldo' => function ($query) use ($data) {
                $query->where('tahun', $data['tahun'])->where(function ($query) use ($data) {
                    $query->where('bulan', '0')->orwhere('bulan', $data['bulan']);
                });
            }
        ])->get();

        $view = view('pelaporan.view.neraca_saldo', $data)->render();

        if ($data['type'] == 'pdf') {
            $pdf = PDF::loadHTML($view)->setPaper('A4', 'landscape');
            return $pdf->stream();
        } else {
            return $view;
        }
    }

    private function penduduk(array $data)
    {
        $thn = $data['tahun'];
        $bln = $data['bulan'];
        $hari = $data['hari'];

        $tgl = $thn . '-' . $bln . '-' . $hari;
        $data['sub_judul'] = 'Tahun ' . Tanggal::tahun($tgl);
        $data['tgl'] = Tanggal::tahun($tgl);
        if ($data['bulanan']) {
            $data['sub_judul'] = 'Bulan ' . Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
            $data['tgl'] = Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
        }

        $data['desa'] = Desa::where('kd_kec', $data['kec']->kd_kec)->with([
            'anggota',
            'anggota.u',
            'sebutan_desa'
        ])->get();

        $view = view('pelaporan.view.basis_data.penduduk', $data)->render();

        if ($data['type'] == 'pdf') {
            $pdf = PDF::loadHTML($view)->setPaper('A4', 'landscape');
            return $pdf->stream();
        } else {
            return $view;
        }
    }

    private function kelompok(array $data)
    {
        $thn = $data['tahun'];
        $bln = $data['bulan'];
        $hari = $data['hari'];

        $tgl = $thn . '-' . $bln . '-' . $hari;
        $data['sub_judul'] = 'Tahun ' . Tanggal::tahun($tgl);
        $data['tgl'] = Tanggal::tahun($tgl);
        if ($data['bulanan']) {
            $data['sub_judul'] = 'Bulan ' . Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
            $data['tgl'] = Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
        }

        $data['desa'] = Desa::where('kd_kec', $data['kec']->kd_kec)->with([
            'kelompok' => function ($query) {
                $query->where('jenis_produk_pinjaman', '!=', '3');
            },
            'sebutan_desa'
        ])->get();

        $view = view('pelaporan.view.basis_data.kelompok', $data)->render();

        if ($data['type'] == 'pdf') {
            $pdf = PDF::loadHTML($view)->setPaper('A4', 'landscape');
            return $pdf->stream();
        } else {
            return $view;
        }
    }

    private function lembaga_lain(array $data)
    {
        $thn = $data['tahun'];
        $bln = $data['bulan'];
        $hari = $data['hari'];

        $tgl = $thn . '-' . $bln . '-' . $hari;
        $data['sub_judul'] = 'Tahun ' . Tanggal::tahun($tgl);
        $data['tgl'] = Tanggal::tahun($tgl);
        if ($data['bulanan']) {
            $data['sub_judul'] = 'Bulan ' . Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
            $data['tgl'] = Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
        }

        $data['desa'] = Desa::where('kd_kec', 'LIKE', $data['kec']->kd_kab . '%')->with([
            'kelompok' => function ($query) {
                $query->where('jenis_produk_pinjaman', '=', '3');
            },
            'sebutan_desa'
        ])->get();

        $view = view('pelaporan.view.basis_data.lembaga_lain', $data)->render();

        if ($data['type'] == 'pdf') {
            $pdf = PDF::loadHTML($view)->setPaper('A4', 'landscape');
            return $pdf->stream();
        } else {
            return $view;
        }
    }

    private function kelompok_aktif(array $data)
    {
        $thn = $data['tahun'];
        $bln = $data['bulan'];
        $hari = $data['hari'];

        $tgl = $thn . '-' . $bln . '-' . $hari;
        $data['sub_judul'] = 'Tahun ' . Tanggal::tahun($tgl);
        $data['tgl'] = Tanggal::tahun($tgl);
        if ($data['bulanan']) {
            $data['sub_judul'] = 'Bulan ' . Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
            $data['tgl'] = Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
        }

        $data['jenis_pp'] = JenisProdukPinjaman::where('lokasi', '0')->with([
            'pinjaman_kelompok' => function ($query) use ($data) {
                $tb_pinkel = 'pinjaman_kelompok_' . $data['kec']->id;
                $tb_kel = 'kelompok_' . $data['kec']->id;
                $data['tb_pinkel'] = $tb_pinkel;

                $query->select($tb_pinkel . '.*', $tb_kel . '.nama_kelompok', $tb_kel . '.ketua', 'desa.nama_desa', 'desa.kd_desa', 'desa.kode_desa', 'sebutan_desa.sebutan_desa')
                    ->join($tb_kel, $tb_kel . '.id', '=', $tb_pinkel . '.id_kel')
                    ->join('desa', $tb_kel . '.desa', '=', 'desa.kd_desa')
                    ->join('sebutan_desa', 'sebutan_desa.id', '=', 'desa.sebutan')
                    ->withCount('pinjaman_anggota')
                    ->withSum(['real' => function ($query) use ($data) {
                        $query->where('tgl_transaksi', 'LIKE', '%' . $data['tahun'] . '-' . $data['bulan'] . '-%');
                    }], 'realisasi_pokok')
                    ->withSum(['real' => function ($query) use ($data) {
                        $query->where('tgl_transaksi', 'LIKE', '%' . $data['tahun'] . '-' . $data['bulan'] . '-%');
                    }], 'realisasi_jasa')
                    ->where($tb_pinkel . '.sistem_angsuran', '!=', '12')->where(function ($query) use ($data) {
                        $query->where([
                            [$data['tb_pinkel'] . '.status', 'A'],
                            [$data['tb_pinkel'] . '.tgl_cair', '<=', $data['tgl_kondisi']]
                        ])->orwhere([
                            [$data['tb_pinkel'] . '.status', 'L'],
                            [$data['tb_pinkel'] . '.tgl_lunas', '>=', $data['tgl_kondisi']]
                        ])->orwhere([
                            [$data['tb_pinkel'] . '.status', 'R'],
                            [$data['tb_pinkel'] . '.tgl_lunas', '>=', $data['tgl_kondisi']]
                        ])->orwhere([
                            [$data['tb_pinkel'] . '.status', 'H'],
                            [$data['tb_pinkel'] . '.tgl_lunas', '>=', $data['tgl_kondisi']]
                        ]);
                    })
                    ->orderBy($tb_kel . '.desa', 'ASC')
                    ->orderBy($tb_pinkel . '.tgl_cair', 'ASC');
            },
            'pinjaman_kelompok.saldo' => function ($query) use ($data) {
                $query->where('tgl_transaksi', '<=', $data['tgl_kondisi']);
            },
            'pinjaman_kelompok.target' => function ($query) use ($data) {
                $query->where('jatuh_tempo', '<=', $data['tgl_kondisi']);
            },
            'pinjaman_kelompok.sis_pokok'
        ])->get();

        $view = view('pelaporan.view.perkembangan_piutang.kelompok_aktif', $data)->render();

        if ($data['type'] == 'pdf') {
            $pdf = PDF::loadHTML($view)->setPaper('A4', 'landscape');
            return $pdf->stream();
        } else {
            return $view;
        }
    }

    private function pemanfaat_aktif(array $data)
    {
        $thn = $data['tahun'];
        $bln = $data['bulan'];
        $hari = $data['hari'];

        $tgl = $thn . '-' . $bln . '-' . $hari;
        $data['sub_judul'] = 'Tahun ' . Tanggal::tahun($tgl);
        $data['tgl'] = Tanggal::tahun($tgl);
        if ($data['bulanan']) {
            $data['sub_judul'] = 'Bulan ' . Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
            $data['tgl'] = Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
        }

        $data['jenis_pp'] = JenisProdukPinjaman::where('lokasi', '0')->with([
            'pinjaman_anggota' => function ($query) use ($data) {
                $tb_pinj = 'pinjaman_anggota_' . $data['kec']->id;
                $tb_angg = 'anggota_' . $data['kec']->id;
                $tb_kel = 'kelompok_' . $data['kec']->id;
                $data['tb_pinj'] = $tb_pinj;

                $query->select(
                    $tb_pinj . '.*',
                    $tb_angg . '.namadepan',
                    $tb_angg . '.alamat',
                    $tb_angg . '.nik',
                    $tb_angg . '.kk',
                    $tb_kel . '.nama_kelompok',
                    'desa.nama_desa',
                    'desa.kd_desa',
                    'desa.kode_desa',
                    'sebutan_desa.sebutan_desa'
                )
                    ->join($tb_angg, $tb_angg . '.id', '=', $tb_pinj . '.nia')
                    ->join($tb_kel, $tb_kel . '.id', '=', $tb_pinj . '.id_kel')
                    ->join('desa', $tb_angg . '.desa', '=', 'desa.kd_desa')
                    ->join('sebutan_desa', 'sebutan_desa.id', '=', 'desa.sebutan')
                    ->where($tb_pinj . '.sistem_angsuran', '!=', '12')->where(function ($query) use ($data) {
                        $query->where([
                            [$data['tb_pinj'] . '.status', 'A'],
                            [$data['tb_pinj'] . '.tgl_cair', '<=', $data['tgl_kondisi']]
                        ])->orwhere([
                            [$data['tb_pinj'] . '.status', 'L'],
                            [$data['tb_pinj'] . '.tgl_lunas', '>=', $data['tgl_kondisi']]
                        ])->orwhere([
                            [$data['tb_pinj'] . '.status', 'R'],
                            [$data['tb_pinj'] . '.tgl_lunas', '>=', $data['tgl_kondisi']]
                        ])->orwhere([
                            [$data['tb_pinj'] . '.status', 'H'],
                            [$data['tb_pinj'] . '.tgl_lunas', '>=', $data['tgl_kondisi']]
                        ]);
                    })
                    ->orderBy($tb_angg . '.desa', 'ASC')
                    ->orderBy($tb_pinj . '.tgl_cair', 'ASC');
            }
        ])->get();

        $view = view('pelaporan.view.perkembangan_piutang.pemanfaat_aktif', $data)->render();

        if ($data['type'] == 'pdf') {
            $pdf = PDF::loadHTML($view)->setPaper('A4', 'landscape');
            return $pdf->stream();
        } else {
            return $view;
        }
    }

    private function proposal(array $data)
    {
        $thn = $data['tahun'];
        $bln = $data['bulan'];
        $hari = $data['hari'];

        $tgl = $thn . '-' . $bln . '-' . $hari;
        $data['sub_judul'] = 'Tahun ' . Tanggal::tahun($tgl);
        $data['tgl'] = Tanggal::tahun($tgl);
        if ($data['bulanan']) {
            $data['sub_judul'] = 'Bulan ' . Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
            $data['tgl'] = Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
        }

        $data['jenis_pp'] = JenisProdukPinjaman::where('lokasi', '0')->with([
            'pinjaman_kelompok' => function ($query) use ($data) {
                $tb_pinkel = 'pinjaman_kelompok_' . $data['kec']->id;
                $tb_kel = 'kelompok_' . $data['kec']->id;
                $data['tb_pinkel'] = $tb_pinkel;

                $query->select($tb_pinkel . '.*', $tb_kel . '.nama_kelompok', $tb_kel . '.ketua', 'desa.nama_desa', 'desa.kd_desa', 'desa.kode_desa', 'sebutan_desa.sebutan_desa')
                    ->join($tb_kel, $tb_kel . '.id', '=', $tb_pinkel . '.id_kel')
                    ->join('desa', $tb_kel . '.desa', '=', 'desa.kd_desa')
                    ->join('sebutan_desa', 'sebutan_desa.id', '=', 'desa.sebutan')
                    ->withCount('pinjaman_anggota')
                    ->where($tb_pinkel . '.sistem_angsuran', '!=', '12')->where('status', 'P')
                    ->orderBy($tb_kel . '.desa', 'ASC')
                    ->orderBy($tb_pinkel . '.tgl_proposal', 'ASC');
            },
            'pinjaman_kelompok.sis_pokok'
        ])->get();

        $view = view('pelaporan.view.perkembangan_piutang.proposal', $data)->render();

        if ($data['type'] == 'pdf') {
            $pdf = PDF::loadHTML($view)->setPaper('A4', 'landscape');
            return $pdf->stream();
        } else {
            return $view;
        }
    }

    private function verifikasi(array $data)
    {
        $thn = $data['tahun'];
        $bln = $data['bulan'];
        $hari = $data['hari'];

        $tgl = $thn . '-' . $bln . '-' . $hari;
        $data['sub_judul'] = 'Tahun ' . Tanggal::tahun($tgl);
        $data['tgl'] = Tanggal::tahun($tgl);
        if ($data['bulanan']) {
            $data['sub_judul'] = 'Bulan ' . Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
            $data['tgl'] = Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
        }

        $data['jenis_pp'] = JenisProdukPinjaman::where('lokasi', '0')->with([
            'pinjaman_kelompok' => function ($query) use ($data) {
                $tb_pinkel = 'pinjaman_kelompok_' . $data['kec']->id;
                $tb_kel = 'kelompok_' . $data['kec']->id;
                $data['tb_pinkel'] = $tb_pinkel;

                $query->select($tb_pinkel . '.*', $tb_kel . '.nama_kelompok', $tb_kel . '.ketua', 'desa.nama_desa', 'desa.kd_desa', 'desa.kode_desa', 'sebutan_desa.sebutan_desa')
                    ->join($tb_kel, $tb_kel . '.id', '=', $tb_pinkel . '.id_kel')
                    ->join('desa', $tb_kel . '.desa', '=', 'desa.kd_desa')
                    ->join('sebutan_desa', 'sebutan_desa.id', '=', 'desa.sebutan')
                    ->withCount('pinjaman_anggota')
                    ->where($tb_pinkel . '.sistem_angsuran', '!=', '12')->where('status', 'V')
                    ->orderBy($tb_kel . '.desa', 'ASC')
                    ->orderBy($tb_pinkel . '.tgl_verifikasi', 'ASC');
            },
            'pinjaman_kelompok.sis_pokok'
        ])->get();

        $view = view('pelaporan.view.perkembangan_piutang.verifikasi', $data)->render();

        if ($data['type'] == 'pdf') {
            $pdf = PDF::loadHTML($view)->setPaper('A4', 'landscape');
            return $pdf->stream();
        } else {
            return $view;
        }
    }

    private function waiting(array $data)
    {
        $thn = $data['tahun'];
        $bln = $data['bulan'];
        $hari = $data['hari'];

        $tgl = $thn . '-' . $bln . '-' . $hari;
        $data['sub_judul'] = 'Tahun ' . Tanggal::tahun($tgl);
        $data['tgl'] = Tanggal::tahun($tgl);
        if ($data['bulanan']) {
            $data['sub_judul'] = 'Bulan ' . Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
            $data['tgl'] = Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
        }

        $data['jenis_pp'] = JenisProdukPinjaman::where('lokasi', '0')->with([
            'pinjaman_kelompok' => function ($query) use ($data) {
                $tb_pinkel = 'pinjaman_kelompok_' . $data['kec']->id;
                $tb_kel = 'kelompok_' . $data['kec']->id;
                $data['tb_pinkel'] = $tb_pinkel;

                $query->select($tb_pinkel . '.*', $tb_kel . '.nama_kelompok', $tb_kel . '.ketua', 'desa.nama_desa', 'desa.kd_desa', 'desa.kode_desa', 'sebutan_desa.sebutan_desa')
                    ->join($tb_kel, $tb_kel . '.id', '=', $tb_pinkel . '.id_kel')
                    ->join('desa', $tb_kel . '.desa', '=', 'desa.kd_desa')
                    ->join('sebutan_desa', 'sebutan_desa.id', '=', 'desa.sebutan')
                    ->withCount('pinjaman_anggota')
                    ->where($tb_pinkel . '.sistem_angsuran', '!=', '12')->where('status', 'W')
                    ->orderBy($tb_kel . '.desa', 'ASC')
                    ->orderBy($tb_pinkel . '.tgl_tunggu', 'ASC');
            },
            'pinjaman_kelompok.sis_pokok'
        ])->get();

        $view = view('pelaporan.view.perkembangan_piutang.waiting', $data)->render();

        if ($data['type'] == 'pdf') {
            $pdf = PDF::loadHTML($view)->setPaper('A4', 'landscape');
            return $pdf->stream();
        } else {
            return $view;
        }
    }

    private function pinjaman_per_kelompok(array $data)
    {
        $thn = $data['tahun'];
        $bln = $data['bulan'];
        $hari = $data['hari'];

        $tgl = $thn . '-' . $bln . '-' . $hari;
        $data['sub_judul'] = 'Tahun ' . Tanggal::tahun($tgl);
        $data['tgl'] = Tanggal::tahun($tgl);
        if ($data['bulanan']) {
            $data['sub_judul'] = 'Bulan ' . Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
            $data['tgl'] = Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
        }

        $data['tgl_lalu'] = $data['tahun'] . '-' . $data['bulan'] . '-01';

        $data['jenis_pp'] = JenisProdukPinjaman::where('lokasi', '0')->with([
            'pinjaman_kelompok' => function ($query) use ($data) {
                $tb_pinkel = 'pinjaman_kelompok_' . $data['kec']->id;
                $tb_kel = 'kelompok_' . $data['kec']->id;
                $data['tb_pinkel'] = $tb_pinkel;

                $query->select($tb_pinkel . '.*', $tb_kel . '.nama_kelompok', $tb_kel . '.ketua', 'desa.nama_desa', 'desa.kd_desa', 'desa.kode_desa', 'sebutan_desa.sebutan_desa')
                    ->join($tb_kel, $tb_kel . '.id', '=', $tb_pinkel . '.id_kel')
                    ->join('desa', $tb_kel . '.desa', '=', 'desa.kd_desa')
                    ->join('sebutan_desa', 'sebutan_desa.id', '=', 'desa.sebutan')
                    ->withSum(['real' => function ($query) use ($data) {
                        $query->where('tgl_transaksi', 'LIKE', '%' . $data['tahun'] . '-' . $data['bulan'] . '-%');
                    }], 'realisasi_pokok')
                    ->withSum(['real' => function ($query) use ($data) {
                        $query->where('tgl_transaksi', 'LIKE', '%' . $data['tahun'] . '-' . $data['bulan'] . '-%');
                    }], 'realisasi_jasa')
                    ->where($tb_pinkel . '.sistem_angsuran', '!=', '12')->where(function ($query) use ($data) {
                        $query->where([
                            [$data['tb_pinkel'] . '.status', 'A'],
                            [$data['tb_pinkel'] . '.tgl_cair', '<=', $data['tgl_kondisi']]
                        ])->orwhere([
                            [$data['tb_pinkel'] . '.status', 'L'],
                            [$data['tb_pinkel'] . '.tgl_cair', '<=', $data['tgl_kondisi']],
                            [$data['tb_pinkel'] . '.tgl_lunas', '>=', "$data[tahun]-01-01"]
                        ])->orwhere([
                            [$data['tb_pinkel'] . '.status', 'L'],
                            [$data['tb_pinkel'] . '.tgl_lunas', '<=', $data['tgl_kondisi']],
                            [$data['tb_pinkel'] . '.tgl_lunas', '>=', "$data[tahun]-01-01"]
                        ])->orwhere([
                            [$data['tb_pinkel'] . '.status', 'R'],
                            [$data['tb_pinkel'] . '.tgl_cair', '<=', $data['tgl_kondisi']],
                            [$data['tb_pinkel'] . '.tgl_lunas', '>=', "$data[tahun]-01-01"]
                        ])->orwhere([
                            [$data['tb_pinkel'] . '.status', 'R'],
                            [$data['tb_pinkel'] . '.tgl_lunas', '<=', $data['tgl_kondisi']],
                            [$data['tb_pinkel'] . '.tgl_lunas', '>=', "$data[tahun]-01-01"]
                        ])->orwhere([
                            [$data['tb_pinkel'] . '.status', 'H'],
                            [$data['tb_pinkel'] . '.tgl_cair', '<=', $data['tgl_kondisi']],
                            [$data['tb_pinkel'] . '.tgl_lunas', '>=', "$data[tahun]-01-01"]
                        ])->orwhere([
                            [$data['tb_pinkel'] . '.status', 'H'],
                            [$data['tb_pinkel'] . '.tgl_lunas', '<=', $data['tgl_kondisi']],
                            [$data['tb_pinkel'] . '.tgl_lunas', '>=', "$data[tahun]-01-01"]
                        ]);
                    })
                    ->orderBy($tb_kel . '.desa', 'ASC')
                    ->orderBy($tb_pinkel . '.tgl_cair', 'ASC');
            },
            'pinjaman_kelompok.saldo' => function ($query) use ($data) {
                $query->where('tgl_transaksi', '<=', $data['tgl_kondisi']);
            },
            'pinjaman_kelompok.target' => function ($query) use ($data) {
                $query->where('jatuh_tempo', '<=', $data['tgl_kondisi']);
            }
        ])->get();

        $data['lunas'] = PinjamanKelompok::where([
            ['tgl_lunas', '<', $thn . '-01-01'],
            ['status', 'L']
        ])->with('saldo', 'target')->get();

        $view = view('pelaporan.view.perkembangan_piutang.lpp_kelompok', $data)->render();

        if ($data['type'] == 'pdf') {
            $pdf = PDF::loadHTML($view)->setPaper('A4', 'landscape');
            return $pdf->stream();
        } else {
            return $view;
        }
    }

    private function pinjaman_per_desa(array $data)
    {
        $thn = $data['tahun'];
        $bln = $data['bulan'];
        $hari = $data['hari'];

        $tgl = $thn . '-' . $bln . '-' . $hari;
        $data['sub_judul'] = 'Tahun ' . Tanggal::tahun($tgl);
        $data['tgl'] = Tanggal::tahun($tgl);
        if ($data['bulanan']) {
            $data['sub_judul'] = 'Bulan ' . Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
            $data['tgl'] = Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
        }

        $data['jenis_pp'] = JenisProdukPinjaman::where('lokasi', '0')->with([
            'pinjaman_kelompok' => function ($query) use ($data) {
                $tb_pinkel = 'pinjaman_kelompok_' . $data['kec']->id;
                $tb_kel = 'kelompok_' . $data['kec']->id;
                $data['tb_pinkel'] = $tb_pinkel;

                $query->select($tb_pinkel . '.*', $tb_kel . '.nama_kelompok', 'desa.nama_desa', 'desa.kd_desa', 'desa.kode_desa', 'sebutan_desa.sebutan_desa')
                    ->join($tb_kel, $tb_kel . '.id', '=', $tb_pinkel . '.id_kel')
                    ->join('desa', $tb_kel . '.desa', '=', 'desa.kd_desa')
                    ->join('sebutan_desa', 'sebutan_desa.id', '=', 'desa.sebutan')
                    ->withSum(['real' => function ($query) use ($data) {
                        $query->where('tgl_transaksi', 'LIKE', '%' . $data['tahun'] . '-' . $data['bulan'] . '-%');
                    }], 'realisasi_pokok')
                    ->withSum(['real' => function ($query) use ($data) {
                        $query->where('tgl_transaksi', 'LIKE', '%' . $data['tahun'] . '-' . $data['bulan'] . '-%');
                    }], 'realisasi_jasa')
                    ->where($tb_pinkel . '.sistem_angsuran', '!=', '12')->where(function ($query) use ($data) {
                        $query->where([
                            [$data['tb_pinkel'] . '.status', 'A'],
                            [$data['tb_pinkel'] . '.tgl_cair', '<=', $data['tgl_kondisi']]
                        ])->orwhere([
                            [$data['tb_pinkel'] . '.status', 'L'],
                            [$data['tb_pinkel'] . '.tgl_cair', '<=', $data['tgl_kondisi']],
                            [$data['tb_pinkel'] . '.tgl_lunas', '>=', "$data[tahun]-01-01"]
                        ])->orwhere([
                            [$data['tb_pinkel'] . '.status', 'L'],
                            [$data['tb_pinkel'] . '.tgl_lunas', '<=', $data['tgl_kondisi']],
                            [$data['tb_pinkel'] . '.tgl_lunas', '>=', "$data[tahun]-01-01"]
                        ])->orwhere([
                            [$data['tb_pinkel'] . '.status', 'R'],
                            [$data['tb_pinkel'] . '.tgl_cair', '<=', $data['tgl_kondisi']],
                            [$data['tb_pinkel'] . '.tgl_lunas', '>=', "$data[tahun]-01-01"]
                        ])->orwhere([
                            [$data['tb_pinkel'] . '.status', 'R'],
                            [$data['tb_pinkel'] . '.tgl_lunas', '<=', $data['tgl_kondisi']],
                            [$data['tb_pinkel'] . '.tgl_lunas', '>=', "$data[tahun]-01-01"]
                        ])->orwhere([
                            [$data['tb_pinkel'] . '.status', 'H'],
                            [$data['tb_pinkel'] . '.tgl_cair', '<=', $data['tgl_kondisi']],
                            [$data['tb_pinkel'] . '.tgl_lunas', '>=', "$data[tahun]-01-01"]
                        ])->orwhere([
                            [$data['tb_pinkel'] . '.status', 'H'],
                            [$data['tb_pinkel'] . '.tgl_lunas', '<=', $data['tgl_kondisi']],
                            [$data['tb_pinkel'] . '.tgl_lunas', '>=', "$data[tahun]-01-01"]
                        ]);
                    })
                    ->orderBy($tb_kel . '.desa', 'ASC')
                    ->orderBy($tb_pinkel . '.tgl_cair', 'ASC');
            },
            'pinjaman_kelompok.saldo' => function ($query) use ($data) {
                $query->where('tgl_transaksi', '<=', $data['tgl_kondisi']);
            },
            'pinjaman_kelompok.target' => function ($query) use ($data) {
                $query->where('jatuh_tempo', '<=', $data['tgl_kondisi']);
            }
        ])->get();

        $data['lunas'] = PinjamanKelompok::where([
            ['tgl_lunas', '<', $thn . '-01-01'],
            ['status', 'L']
        ])->with('saldo', 'target')->get();

        $view = view('pelaporan.view.perkembangan_piutang.lpp_desa', $data)->render();

        if ($data['type'] == 'pdf') {
            $pdf = PDF::loadHTML($view)->setPaper('A4', 'landscape');
            return $pdf->stream();
        } else {
            return $view;
        }
    }

    private function kolek_per_kelompok(array $data)
    {
        $thn = $data['tahun'];
        $bln = $data['bulan'];
        $hari = $data['hari'];

        $tgl = $thn . '-' . $bln . '-' . $hari;
        $data['sub_judul'] = 'Tahun ' . Tanggal::tahun($tgl);
        $data['tgl'] = Tanggal::tahun($tgl);
        if ($data['bulanan']) {
            $data['sub_judul'] = 'Bulan ' . Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
            $data['tgl'] = Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
        }

        $data['jenis_pp'] = JenisProdukPinjaman::where('lokasi', '0')->with([
            'pinjaman_kelompok' => function ($query) use ($data) {
                $tb_pinkel = 'pinjaman_kelompok_' . $data['kec']->id;
                $tb_kel = 'kelompok_' . $data['kec']->id;
                $data['tb_pinkel'] = $tb_pinkel;

                $query->select($tb_pinkel . '.*', $tb_kel . '.nama_kelompok', 'desa.nama_desa', 'desa.kd_desa', 'desa.kode_desa', 'sebutan_desa.sebutan_desa')
                    ->join($tb_kel, $tb_kel . '.id', '=', $tb_pinkel . '.id_kel')
                    ->join('desa', $tb_kel . '.desa', '=', 'desa.kd_desa')
                    ->join('sebutan_desa', 'sebutan_desa.id', '=', 'desa.sebutan')
                    ->withSum(['real' => function ($query) use ($data) {
                        $query->where('tgl_transaksi', 'LIKE', '%' . $data['tahun'] . '-' . $data['bulan'] . '-%');
                    }], 'realisasi_pokok')
                    ->withSum(['real' => function ($query) use ($data) {
                        $query->where('tgl_transaksi', 'LIKE', '%' . $data['tahun'] . '-' . $data['bulan'] . '-%');
                    }], 'realisasi_jasa')
                    ->where($tb_pinkel . '.sistem_angsuran', '!=', '12')->where(function ($query) use ($data) {
                        $query->where([
                            [$data['tb_pinkel'] . '.status', 'A'],
                            [$data['tb_pinkel'] . '.tgl_cair', '<=', $data['tgl_kondisi']]
                        ])->orwhere([
                            [$data['tb_pinkel'] . '.status', 'L'],
                            [$data['tb_pinkel'] . '.tgl_cair', '<=', $data['tgl_kondisi']],
                            [$data['tb_pinkel'] . '.tgl_lunas', '>=', "$data[tahun]-01-01"]
                        ])->orwhere([
                            [$data['tb_pinkel'] . '.status', 'L'],
                            [$data['tb_pinkel'] . '.tgl_lunas', '<=', $data['tgl_kondisi']],
                            [$data['tb_pinkel'] . '.tgl_lunas', '>=', "$data[tahun]-01-01"]
                        ])->orwhere([
                            [$data['tb_pinkel'] . '.status', 'R'],
                            [$data['tb_pinkel'] . '.tgl_cair', '<=', $data['tgl_kondisi']],
                            [$data['tb_pinkel'] . '.tgl_lunas', '>=', "$data[tahun]-01-01"]
                        ])->orwhere([
                            [$data['tb_pinkel'] . '.status', 'R'],
                            [$data['tb_pinkel'] . '.tgl_lunas', '<=', $data['tgl_kondisi']],
                            [$data['tb_pinkel'] . '.tgl_lunas', '>=', "$data[tahun]-01-01"]
                        ])->orwhere([
                            [$data['tb_pinkel'] . '.status', 'H'],
                            [$data['tb_pinkel'] . '.tgl_cair', '<=', $data['tgl_kondisi']],
                            [$data['tb_pinkel'] . '.tgl_lunas', '>=', "$data[tahun]-01-01"]
                        ])->orwhere([
                            [$data['tb_pinkel'] . '.status', 'H'],
                            [$data['tb_pinkel'] . '.tgl_lunas', '<=', $data['tgl_kondisi']],
                            [$data['tb_pinkel'] . '.tgl_lunas', '>=', "$data[tahun]-01-01"]
                        ]);
                    })
                    ->orderBy($tb_kel . '.desa', 'ASC')
                    ->orderBy($tb_pinkel . '.tgl_cair', 'ASC');
            },
            'pinjaman_kelompok.saldo' => function ($query) use ($data) {
                $query->where('tgl_transaksi', '<=', $data['tgl_kondisi']);
            },
            'pinjaman_kelompok.target' => function ($query) use ($data) {
                $query->where('jatuh_tempo', '<=', $data['tgl_kondisi']);
            }
        ])->get();

        $view = view('pelaporan.view.perkembangan_piutang.kolek_kelompok', $data)->render();

        if ($data['type'] == 'pdf') {
            $pdf = PDF::loadHTML($view)->setPaper('A4', 'landscape');
            return $pdf->stream();
        } else {
            return $view;
        }
    }

    private function kolek_per_desa(array $data)
    {
        $thn = $data['tahun'];
        $bln = $data['bulan'];
        $hari = $data['hari'];

        $tgl = $thn . '-' . $bln . '-' . $hari;
        $data['sub_judul'] = 'Tahun ' . Tanggal::tahun($tgl);
        $data['tgl'] = Tanggal::tahun($tgl);
        if ($data['bulanan']) {
            $data['sub_judul'] = 'Bulan ' . Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
            $data['tgl'] = Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
        }

        $data['jenis_pp'] = JenisProdukPinjaman::where('lokasi', '0')->with([
            'pinjaman_kelompok' => function ($query) use ($data) {
                $tb_pinkel = 'pinjaman_kelompok_' . $data['kec']->id;
                $tb_kel = 'kelompok_' . $data['kec']->id;
                $data['tb_pinkel'] = $tb_pinkel;

                $query->select($tb_pinkel . '.*', $tb_kel . '.nama_kelompok', 'desa.nama_desa', 'desa.kd_desa', 'desa.kode_desa', 'sebutan_desa.sebutan_desa')
                    ->join($tb_kel, $tb_kel . '.id', '=', $tb_pinkel . '.id_kel')
                    ->join('desa', $tb_kel . '.desa', '=', 'desa.kd_desa')
                    ->join('sebutan_desa', 'sebutan_desa.id', '=', 'desa.sebutan')
                    ->withSum(['real' => function ($query) use ($data) {
                        $query->where('tgl_transaksi', 'LIKE', '%' . $data['tahun'] . '-' . $data['bulan'] . '-%');
                    }], 'realisasi_pokok')
                    ->withSum(['real' => function ($query) use ($data) {
                        $query->where('tgl_transaksi', 'LIKE', '%' . $data['tahun'] . '-' . $data['bulan'] . '-%');
                    }], 'realisasi_jasa')
                    ->where($tb_pinkel . '.sistem_angsuran', '!=', '12')->where(function ($query) use ($data) {
                        $query->where([
                            [$data['tb_pinkel'] . '.status', 'A'],
                            [$data['tb_pinkel'] . '.tgl_cair', '<=', $data['tgl_kondisi']]
                        ])->orwhere([
                            [$data['tb_pinkel'] . '.status', 'L'],
                            [$data['tb_pinkel'] . '.tgl_cair', '<=', $data['tgl_kondisi']],
                            [$data['tb_pinkel'] . '.tgl_lunas', '>=', "$data[tahun]-01-01"]
                        ])->orwhere([
                            [$data['tb_pinkel'] . '.status', 'L'],
                            [$data['tb_pinkel'] . '.tgl_lunas', '<=', $data['tgl_kondisi']],
                            [$data['tb_pinkel'] . '.tgl_lunas', '>=', "$data[tahun]-01-01"]
                        ])->orwhere([
                            [$data['tb_pinkel'] . '.status', 'R'],
                            [$data['tb_pinkel'] . '.tgl_cair', '<=', $data['tgl_kondisi']],
                            [$data['tb_pinkel'] . '.tgl_lunas', '>=', "$data[tahun]-01-01"]
                        ])->orwhere([
                            [$data['tb_pinkel'] . '.status', 'R'],
                            [$data['tb_pinkel'] . '.tgl_lunas', '<=', $data['tgl_kondisi']],
                            [$data['tb_pinkel'] . '.tgl_lunas', '>=', "$data[tahun]-01-01"]
                        ])->orwhere([
                            [$data['tb_pinkel'] . '.status', 'H'],
                            [$data['tb_pinkel'] . '.tgl_cair', '<=', $data['tgl_kondisi']],
                            [$data['tb_pinkel'] . '.tgl_lunas', '>=', "$data[tahun]-01-01"]
                        ])->orwhere([
                            [$data['tb_pinkel'] . '.status', 'H'],
                            [$data['tb_pinkel'] . '.tgl_lunas', '<=', $data['tgl_kondisi']],
                            [$data['tb_pinkel'] . '.tgl_lunas', '>=', "$data[tahun]-01-01"]
                        ]);
                    })
                    ->orderBy($tb_kel . '.desa', 'ASC')
                    ->orderBy($tb_pinkel . '.tgl_cair', 'ASC');
            },
            'pinjaman_kelompok.saldo' => function ($query) use ($data) {
                $query->where('tgl_transaksi', '<=', $data['tgl_kondisi']);
            },
            'pinjaman_kelompok.target' => function ($query) use ($data) {
                $query->where('jatuh_tempo', '<=', $data['tgl_kondisi']);
            }
        ])->get();

        $view = view('pelaporan.view.perkembangan_piutang.kolek_desa', $data)->render();

        if ($data['type'] == 'pdf') {
            $pdf = PDF::loadHTML($view)->setPaper('A4', 'landscape');
            return $pdf->stream();
        } else {
            return $view;
        }
    }

    private function cadangan_penghapusan(array $data)
    {
        $thn = $data['tahun'];
        $bln = $data['bulan'];
        $hari = $data['hari'];

        $tgl = $thn . '-' . $bln . '-' . $hari;
        $data['sub_judul'] = 'Tahun ' . Tanggal::tahun($tgl);
        $data['tgl'] = Tanggal::tahun($tgl);
        if ($data['bulanan']) {
            $data['sub_judul'] = 'Bulan ' . Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
            $data['tgl'] = Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
        }

        $data['jenis_pp'] = JenisProdukPinjaman::where('lokasi', '0')->with([
            'pinjaman_kelompok' => function ($query) use ($data) {
                $tb_pinkel = 'pinjaman_kelompok_' . $data['kec']->id;
                $tb_kel = 'kelompok_' . $data['kec']->id;
                $data['tb_pinkel'] = $tb_pinkel;

                $query->select($tb_pinkel . '.*', $tb_kel . '.nama_kelompok', 'desa.nama_desa', 'desa.kd_desa', 'desa.kode_desa', 'sebutan_desa.sebutan_desa')
                    ->join($tb_kel, $tb_kel . '.id', '=', $tb_pinkel . '.id_kel')
                    ->join('desa', $tb_kel . '.desa', '=', 'desa.kd_desa')
                    ->join('sebutan_desa', 'sebutan_desa.id', '=', 'desa.sebutan')
                    ->withSum(['real' => function ($query) use ($data) {
                        $query->where('tgl_transaksi', 'LIKE', '%' . $data['tahun'] . '-' . $data['bulan'] . '-%');
                    }], 'realisasi_pokok')
                    ->withSum(['real' => function ($query) use ($data) {
                        $query->where('tgl_transaksi', 'LIKE', '%' . $data['tahun'] . '-' . $data['bulan'] . '-%');
                    }], 'realisasi_jasa')
                    ->where($tb_pinkel . '.sistem_angsuran', '!=', '12')->where(function ($query) use ($data) {
                        $query->where([
                            [$data['tb_pinkel'] . '.status', 'A'],
                            [$data['tb_pinkel'] . '.tgl_cair', '<=', $data['tgl_kondisi']]
                        ])->orwhere([
                            [$data['tb_pinkel'] . '.status', 'L'],
                            [$data['tb_pinkel'] . '.tgl_cair', '<=', $data['tgl_kondisi']],
                            [$data['tb_pinkel'] . '.tgl_lunas', '>=', "$data[tahun]-01-01"]
                        ])->orwhere([
                            [$data['tb_pinkel'] . '.status', 'L'],
                            [$data['tb_pinkel'] . '.tgl_lunas', '<=', $data['tgl_kondisi']],
                            [$data['tb_pinkel'] . '.tgl_lunas', '>=', "$data[tahun]-01-01"]
                        ])->orwhere([
                            [$data['tb_pinkel'] . '.status', 'R'],
                            [$data['tb_pinkel'] . '.tgl_cair', '<=', $data['tgl_kondisi']],
                            [$data['tb_pinkel'] . '.tgl_lunas', '>=', "$data[tahun]-01-01"]
                        ])->orwhere([
                            [$data['tb_pinkel'] . '.status', 'R'],
                            [$data['tb_pinkel'] . '.tgl_lunas', '<=', $data['tgl_kondisi']],
                            [$data['tb_pinkel'] . '.tgl_lunas', '>=', "$data[tahun]-01-01"]
                        ])->orwhere([
                            [$data['tb_pinkel'] . '.status', 'H'],
                            [$data['tb_pinkel'] . '.tgl_cair', '<=', $data['tgl_kondisi']],
                            [$data['tb_pinkel'] . '.tgl_lunas', '>=', "$data[tahun]-01-01"]
                        ])->orwhere([
                            [$data['tb_pinkel'] . '.status', 'H'],
                            [$data['tb_pinkel'] . '.tgl_lunas', '<=', $data['tgl_kondisi']],
                            [$data['tb_pinkel'] . '.tgl_lunas', '>=', "$data[tahun]-01-01"]
                        ]);
                    })
                    ->orderBy($tb_kel . '.desa', 'ASC')
                    ->orderBy($tb_pinkel . '.tgl_cair', 'ASC');
            },
            'pinjaman_kelompok.saldo' => function ($query) use ($data) {
                $query->where('tgl_transaksi', '<=', $data['tgl_kondisi']);
            },
            'pinjaman_kelompok.target' => function ($query) use ($data) {
                $query->where('jatuh_tempo', '<=', $data['tgl_kondisi']);
            }
        ])->get();

        $view = view('pelaporan.view.perkembangan_piutang.cadangan_penghapusan', $data)->render();

        if ($data['type'] == 'pdf') {
            $pdf = PDF::loadHTML($view)->setPaper('A4', 'landscape');
            return $pdf->stream();
        } else {
            return $view;
        }
    }

    private function rencana_realisasi(array $data)
    {
        $keuangan = new Keuangan;
        $thn = $data['tahun'];
        $bln = $data['bulan'];
        $hari = $data['hari'];

        $tgl = $thn . '-' . $bln . '-' . $hari;
        $data['sub_judul'] = 'Tahun ' . Tanggal::tahun($tgl);
        $data['tgl'] = Tanggal::tahun($tgl);
        $data['tgl_cair'] = $thn . '-';
        if ($data['bulanan']) {
            $data['sub_judul'] = 'Bulan ' . Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
            $data['tgl'] = Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
            $data['tgl_cair'] = $thn . '-' . $bln . '-';
        }

        $data['jenis_pp'] = JenisProdukPinjaman::where('lokasi', '0')->with([
            'pinjaman_kelompok' => function ($query) use ($data) {
                $tb_pinkel = 'pinjaman_kelompok_' . $data['kec']->id;
                $tb_pinj = 'pinjaman_anggota_' . $data['kec']->id;
                $tb_kel = 'kelompok_' . $data['kec']->id;
                $data['tb_pinkel'] = $tb_pinkel;

                $query->select(
                    $tb_pinkel . '.*',
                    $tb_kel . '.nama_kelompok',
                    $tb_kel . '.ketua',
                    'desa.nama_desa',
                    'desa.kd_desa',
                    'desa.kode_desa',
                    'sebutan_desa.sebutan_desa',
                    DB::raw("(SELECT count(*) as jumlah FROM $tb_pinj WHERE $tb_pinj.id_pinkel=$tb_pinkel.id) as pinjaman_anggota_count")
                )
                    ->join($tb_kel, $tb_kel . '.id', '=', $tb_pinkel . '.id_kel')
                    ->join('desa', $tb_kel . '.desa', '=', 'desa.kd_desa')
                    ->join('sebutan_desa', 'sebutan_desa.id', '=', 'desa.sebutan')
                    ->where($tb_pinkel . '.sistem_angsuran', '!=', '12')->where(function ($query) use ($data) {
                        $query->where($data['tb_pinkel'] . '.tgl_cair', 'LIKE', $data['tgl_cair'] . '%')
                            ->where(function ($query) use ($data) {
                                $query->where($data['tb_pinkel'] . '.status', 'A')
                                    ->orwhere($data['tb_pinkel'] . '.status', 'L')
                                    ->orwhere($data['tb_pinkel'] . '.status', 'H')
                                    ->orwhere($data['tb_pinkel'] . '.status', 'R');
                            });
                    })
                    ->orderBy($tb_kel . '.desa', 'ASC')
                    ->orderBy($tb_pinkel . '.id', 'ASC');
            },
            'pinjaman_kelompok.sis_pokok'
        ])->get();

        $data['keuangan'] = $keuangan;
        $view = view('pelaporan.view.perkembangan_piutang.rencana_realisasi', $data)->render();

        if ($data['type'] == 'pdf') {
            $pdf = PDF::loadHTML($view)->setPaper('A4', 'landscape');
            return $pdf->stream();
        } else {
            return $view;
        }
    }

    private function _rencana_realisasi(array $data)
    {
        $keuangan = new Keuangan;
        $thn = $data['tahun'];
        $bln = $data['bulan'];
        $hari = $data['hari'];

        $tgl = $thn . '-' . $bln . '-' . $hari;
        if (strlen($hari) > 0 && strlen($bln) > 0) {
            $data['sub_judul'] = 'Tanggal ' . Tanggal::tglLatin($tgl);
            $data['tgl'] = Tanggal::tglLatin($tgl);
        } elseif (strlen($bln) > 0) {
            $data['sub_judul'] = 'Bulan ' . Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
            $data['tgl'] = Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
        } else {
            $data['sub_judul'] = 'Tahun ' . Tanggal::tahun($tgl);
            $data['tgl'] = Tanggal::tahun($tgl);
        }

        $triwulan = [
            '01' => ['1', '2', '3'],
            '02' => ['1', '2', '3'],
            '03' => ['1', '2', '3'],
            '04' => ['4', '5', '6'],
            '05' => ['4', '5', '6'],
            '06' => ['4', '5', '6'],
            '07' => ['7', '8', '9'],
            '08' => ['7', '8', '9'],
            '09' => ['7', '8', '9'],
            '10' => ['10', '11', '12'],
            '11' => ['10', '11', '12'],
            '12' => ['10', '11', '12'],
        ];

        $bulan_tampil = $triwulan[$data['bulan']];
        $bulan1 = str_pad($bulan_tampil[0], 2, '0', STR_PAD_LEFT);
        $bulan3 = str_pad($bulan_tampil[2], 2, '0', STR_PAD_LEFT);

        $tgl_awal = $data['tahun'] . '-' . $bulan1 . '-01';
        $tgl_akhir = date('Y-m-t', strtotime($data['tahun'] . '-' . $bulan3 . '-01'));
        $data['tgl_akhir'] = $tgl_akhir;

        $data['jenis_pp'] = JenisProdukPinjaman::where('lokasi', '0')->with([
            'pinjaman_kelompok' => function ($query) use ($data) {
                $tb_pinkel = 'pinjaman_kelompok_' . $data['kec']->id;
                $tb_kel = 'kelompok_' . $data['kec']->id;
                $data['tb_pinkel'] = $tb_pinkel;

                $query->select(
                    $tb_pinkel . '.*',
                    $tb_kel . '.nama_kelompok',
                    $tb_kel . '.ketua',
                    'desa.nama_desa',
                    'desa.kd_desa',
                    'desa.kode_desa',
                    'sebutan_desa.sebutan_desa'
                )
                    ->join($tb_kel, $tb_kel . '.id', '=', $tb_pinkel . '.id_kel')
                    ->join('desa', $tb_kel . '.desa', '=', 'desa.kd_desa')
                    ->join('sebutan_desa', 'sebutan_desa.id', '=', 'desa.sebutan')
                    ->where($tb_pinkel . '.sistem_angsuran', '!=', '12')->where(function ($query) use ($data) {
                        $query->where([
                            [$data['tb_pinkel'] . '.status', 'A'],
                            [$data['tb_pinkel'] . '.tgl_cair', '<=', $data['tgl_akhir']]
                        ]);
                    })
                    ->orderBy($tb_kel . '.desa', 'ASC')
                    ->orderBy($tb_pinkel . '.id', 'ASC');
            },
            'pinjaman_kelompok.real' => function ($query) use ($tgl_awal, $tgl_akhir) {
                $query->whereBetween('tgl_transaksi', [$tgl_awal, $tgl_akhir]);
            },
            'pinjaman_kelompok.ra' => function ($query) use ($tgl_awal, $tgl_akhir) {
                $query->whereBetween('jatuh_tempo', [$tgl_awal, $tgl_akhir]);
            }
        ])->get();

        $data['keuangan'] = $keuangan;

        $view = view('pelaporan.view.perkembangan_piutang._rencana_realisasi', $data)->render();

        if ($data['type'] == 'pdf') {
            $pdf = PDF::loadHTML($view)->setPaper('A4', 'landscape');
            return $pdf->stream();
        } else {
            return $view;
        }
    }

    private function tagihan_hari_ini(array $data)
    {
        $thn = $data['tahun'];
        $bln = $data['bulan'];
        $hari = $data['hari'];

        $tgl = $thn . '-' . $bln . '-' . $hari;
        $data['sub_judul'] = $hari . ' ' . Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
        $data['tgl'] = Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);

        $data['pinjaman'] = PinjamanKelompok::where('status', 'A')->whereDay('tgl_cair', date('d', strtotime($tgl)))->with([
            'target' => function ($query) use ($tgl) {
                $query->where([
                    ['jatuh_tempo', $tgl],
                    ['angsuran_ke', '!=', '0']
                ]);
            },
            'saldo' => function ($query) use ($tgl) {
                $query->where('tgl_transaksi', '<=', $tgl);
            },
            'kelompok',
            'kelompok.d'
        ])->get();

        $view = view('pelaporan.view.perkembangan_piutang.jatuh_tempo', $data)->render();

        if ($data['type'] == 'pdf') {
            $pdf = PDF::loadHTML($view)->setPaper('A4', 'landscape');
            return $pdf->stream();
        } else {
            return $view;
        }
    }

    private function menunggak(array $data)
    {
        $thn = $data['tahun'];
        $bln = $data['bulan'];
        $hari = $data['hari'];

        $tgl = $thn . '-' . $bln . '-' . $hari;
        $data['sub_judul'] = $hari . ' ' . Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
        $data['tgl'] = Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);

        $data['jenis_pp'] = JenisProdukPinjaman::where('lokasi', '0')->with([
            'pinjaman_kelompok' => function ($query) use ($data) {
                $tb_pinkel = 'pinjaman_kelompok_' . $data['kec']->id;
                $tb_kel = 'kelompok_' . $data['kec']->id;
                $data['tb_pinkel'] = $tb_pinkel;

                $query->select($tb_pinkel . '.*', $tb_kel . '.nama_kelompok', $tb_kel . '.ketua', 'desa.nama_desa', 'desa.kd_desa', 'desa.kode_desa', 'sebutan_desa.sebutan_desa')
                    ->join($tb_kel, $tb_kel . '.id', '=', $tb_pinkel . '.id_kel')
                    ->join('desa', $tb_kel . '.desa', '=', 'desa.kd_desa')
                    ->join('sebutan_desa', 'sebutan_desa.id', '=', 'desa.sebutan')
                    ->withCount('pinjaman_anggota')
                    ->withSum(['real' => function ($query) use ($data) {
                        $query->where('tgl_transaksi', 'LIKE', '%' . $data['tahun'] . '-' . $data['bulan'] . '-%');
                    }], 'realisasi_pokok')
                    ->withSum(['real' => function ($query) use ($data) {
                        $query->where('tgl_transaksi', 'LIKE', '%' . $data['tahun'] . '-' . $data['bulan'] . '-%');
                    }], 'realisasi_jasa')
                    ->where($tb_pinkel . '.sistem_angsuran', '!=', '12')->where(function ($query) use ($data) {
                        $query->where([
                            [$data['tb_pinkel'] . '.status', 'A'],
                            [$data['tb_pinkel'] . '.tgl_cair', '<=', $data['tgl_kondisi']]
                        ])->orwhere([
                            [$data['tb_pinkel'] . '.status', 'L'],
                            [$data['tb_pinkel'] . '.tgl_lunas', '>=', $data['tgl_kondisi']]
                        ])->orwhere([
                            [$data['tb_pinkel'] . '.status', 'R'],
                            [$data['tb_pinkel'] . '.tgl_lunas', '>=', $data['tgl_kondisi']]
                        ])->orwhere([
                            [$data['tb_pinkel'] . '.status', 'H'],
                            [$data['tb_pinkel'] . '.tgl_lunas', '>=', $data['tgl_kondisi']]
                        ]);
                    })
                    ->orderBy($tb_kel . '.desa', 'ASC')
                    ->orderBy($tb_pinkel . '.id_kel', 'ASC')
                    ->orderBy($tb_pinkel . '.tgl_cair', 'ASC');
            },
            'pinjaman_kelompok.saldo' => function ($query) use ($data) {
                $query->where('tgl_transaksi', '<=', $data['tgl_kondisi']);
            },
            'pinjaman_kelompok.target' => function ($query) use ($data) {
                $query->where('jatuh_tempo', '<=', $data['tgl_kondisi']);
            },
            'pinjaman_kelompok.sis_pokok'
        ])->get();

        $view = view('pelaporan.view.perkembangan_piutang.tunggakan', $data)->render();

        if ($data['type'] == 'pdf') {
            $pdf = PDF::loadHTML($view)->setPaper('A4', 'landscape');
            return $pdf->stream();
        } else {
            return $view;
        }
    }

    private function ati(array $data)
    {
        $data['laporan'] = 'Aset Tetap dan Inventaris';
        $thn = $data['tahun'];
        $bln = $data['bulan'];
        $hari = $data['hari'];

        $tgl = $thn . '-' . $bln . '-' . $hari;
        $data['sub_judul'] = 'Tahun ' . Tanggal::tahun($tgl);
        $data['tgl'] = Tanggal::tahun($tgl);
        if ($data['bulanan']) {
            $data['sub_judul'] = 'Bulan ' . Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
            $data['tgl'] = Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
        }

        $data['inventaris'] = Rekening::where('kode_akun', 'LIKE', '1.2.01%')
            ->with([
                'inventaris' => function ($query) use ($data) {
                    $query->where([
                        ['jenis', '1'],
                        ['status', '!=', '0'],
                        ['tgl_beli', '<=', $data['tgl_kondisi']],
                        ['tgl_beli', 'NOT LIKE', ''],
                        ['harsat', '>', '0']
                    ])->orderBy('tgl_beli', 'ASC');
                }
            ])
            ->get();

        $view = view('pelaporan.view.aset_tetap', $data)->render();

        if ($data['type'] == 'pdf') {
            $pdf = PDF::loadHTML($view)->setPaper('A4', 'landscape');
            return $pdf->stream();
        } else {
            return $view;
        }
    }

    private function atb(array $data)
    {
        $data['laporan'] = 'Aset Tak Berwujud';
        $thn = $data['tahun'];
        $bln = $data['bulan'];
        $hari = $data['hari'];

        $tgl = $thn . '-' . $bln . '-' . $hari;
        $data['sub_judul'] = 'Tahun ' . Tanggal::tahun($tgl);
        $data['tgl'] = Tanggal::tahun($tgl);
        if ($data['bulanan']) {
            $data['sub_judul'] = 'Bulan ' . Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
            $data['tgl'] = Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
        }

        $data['inventaris'] = Rekening::where('kode_akun', 'LIKE', '1.2.03%')
            ->with([
                'inventaris' => function ($query) use ($data) {
                    $query->where([
                        ['jenis', '3'],
                        ['status', '!=', '0'],
                        ['tgl_beli', '<=', $data['tgl_kondisi']],
                        ['tgl_beli', 'NOT LIKE', '']
                    ])->orderBy('tgl_beli', 'ASC');
                }
            ])
            ->get();

        $view = view('pelaporan.view.aset_tak_berwujud', $data)->render();

        if ($data['type'] == 'pdf') {
            $pdf = PDF::loadHTML($view)->setPaper('A4', 'landscape');
            return $pdf->stream();
        } else {
            return $view;
        }
    }

    private function tingkat_kesehatan(array $data)
    {
        $thn = $data['tahun'];
        $bln = $data['bulan'];
        $hari = $data['hari'];

        $tgl = $thn . '-' . $bln . '-' . $hari;
        $data['sub_judul'] = 'Tahun ' . Tanggal::tahun($tgl);
        $data['tgl'] = Tanggal::tahun($tgl);
        if ($data['bulanan']) {
            $data['sub_judul'] = 'Bulan ' . Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
            $data['tgl'] = Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
        }

        $data['dir'] = User::where([
            ['level', $data['kec']->ttd_mengetahui_lap],
            ['jabatan', '1'],
            ['lokasi', Session::get('lokasi')]
        ])->first();

        $data['pengawas'] = User::where([
            ['level', '3'],
            ['jabatan', '1'],
            ['lokasi', Session::get('lokasi')]
        ])->first();

        $data['bendahara'] = User::where([
            ['level', '1'],
            ['jabatan', '3'],
            ['lokasi', Session::get('lokasi')]
        ])->first();

        $view = view('pelaporan.view.penilaian_kesehatan', $data)->render();

        if ($data['type'] == 'pdf') {
            $pdf = PDF::loadHTML($view)->setPaper('A4', 'landscape');
            return $pdf->stream();
        } else {
            return $view;
        }
    }

    private function EB(array $data)
    {
        $keuangan = new Keuangan;
        $thn = $data['tahun'];
        $bln = $data['bulan'];
        $hari = $data['hari'];

        $title = [
            '1,2,3' => 'Januari - Maret',
            '4,5,6' => 'April - Juni',
            '7,8,9' => 'Juli - September',
            '10,11,12' => 'Oktober - Desember'
        ];

        $tgl = $thn . '-' . $bln . '-' . $hari;
        $data['sub_judul'] = 'Tahun ' . Tanggal::tahun($tgl);
        if ($data['bulanan']) {
            $data['sub_judul'] = 'Bulan ' . Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
        }

        $data['tgl'] = $title[$data['sub']] . ' ' . $thn;

        $bulan = explode(',', $data['sub']);
        $awal = $bulan[0];
        $akhir = end($bulan);

        $data['bulan_akhir'] = $awal - 1;
        $data['bulan_tampil'] = $bulan;
        $data['triwulan'] = array_search($data['sub'], array_keys($title)) + 1;
        $data['akun1'] = AkunLevel1::where('lev1', '>=', '4')->with([
            'akun2',
            'akun2.akun3',
            'akun2.akun3.rek',
            'akun2.akun3.rek.kom_saldo' => function ($query) use ($data, $awal, $akhir) {
                $tahun = date('Y', strtotime($data['tgl_kondisi']));
                $query->where('tahun', $tahun)->orderBy('bulan', 'ASC')->orderBy('kode_akun', 'ASC');
            },
            'akun2.akun3.rek.kom_saldo.eb'
        ])->get();

        $data['keuangan'] = $keuangan;
        $view = view('pelaporan.view.e_budgeting', $data)->render();

        if ($data['type'] == 'pdf') {
            $pdf = PDF::loadHTML($view)->setPaper('A4', 'landscape');
            return $pdf->stream();
        } else {
            return $view;
        }
    }

    private function pelunasan(array $data)
    {
        $thn = $data['tahun'];
        $bln = $data['bulan'];
        $hari = $data['hari'];

        $tgl = $thn . '-' . $bln . '-' . $hari;
        $data['sub_judul'] = 'Tahun ' . Tanggal::tahun($tgl);
        $data['tgl'] = Tanggal::tahun($tgl);
        if ($data['bulanan']) {
            $data['sub_judul'] = 'Bulan ' . Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
            $data['tgl'] = Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
        }

        $tb_pinkel = 'pinjaman_kelompok_' . Session::get('lokasi');
        $tb_kel = 'kelompok_' . Session::get('lokasi');
        $data['pinjaman_kelompok'] = PinjamanKelompok::select([
            $tb_pinkel . '.*',
            $tb_kel . '.nama_kelompok',
            $tb_kel . '.ketua',
            $tb_kel . '.alamat_kelompok',
            $tb_kel . '.telpon',
            'desa.nama_desa',
            'desa.kd_desa',
            'desa.kode_desa',
            'sebutan_desa.sebutan_desa',
            DB::raw('(TIMESTAMPDIFF(MONTH, DATE_ADD(' . $tb_pinkel . '.tgl_cair, INTERVAL ' . $tb_pinkel . '.jangka MONTH), CURRENT_DATE)) as sisa')
        ])->join($tb_kel, $tb_kel . '.id', '=', $tb_pinkel . '.id_kel')
            ->join('desa', $tb_kel . '.desa', '=', 'desa.kd_desa')
            ->join('sebutan_desa', 'sebutan_desa.id', '=', 'desa.sebutan')
            ->withSum(['real' => function ($query) use ($data) {
                $query->where('tgl_transaksi', 'LIKE', '%' . $data['tahun'] . '-' . $data['bulan'] . '-%');
            }], 'realisasi_pokok')
            ->withSum(['real' => function ($query) use ($data) {
                $query->where('tgl_transaksi', 'LIKE', '%' . $data['tahun'] . '-' . $data['bulan'] . '-%');
            }], 'realisasi_jasa')
            ->where([
                [$tb_pinkel . '.sistem_angsuran', '!=', '12'],
                [$tb_pinkel . '.status', 'A']
            ])
            ->whereRaw('(TIMESTAMPDIFF(MONTH, DATE_ADD(' . $tb_pinkel . '.tgl_cair, INTERVAL ' . $tb_pinkel . '.jangka MONTH), CURRENT_DATE)) BETWEEN -3 AND 0')
            ->with([
                'rencana1' => function ($query) use ($data, $tb_pinkel) {
                    $query->where('jatuh_tempo', '>=', $data['tahun'] . '-' . $data['bulan'] . '-01')->orWhere('jatuh_tempo', '<', $data['tahun'] . '-' . $data['bulan'] . '-01');
                }
            ])
            ->orderBy($tb_kel . '.desa', 'ASC')
            ->orderBy($tb_pinkel . '.id', 'ASC')->get();

        $view = view('pelaporan.view.perkembangan_piutang.pelunasan', $data)->render();
        $pdf = PDF::loadHTML($view)->setPaper('A4', 'landscape');
        return $pdf->stream();
    }

    private function alokasi_laba(array $data)
    {
        $keuangan = new Keuangan;
        $thn = $data['tahun'];
        $bln = 1;
        $hari = 1;

        $tgl = $thn . '-' . $bln . '-' . $hari;
        $trx_pembagian_laba = Transaksi::where([
            ['rekening_debit', '3.2.01.01'],
            ['keterangan_transaksi', 'LIKE', '%tahun ' . ($thn - 1) . '%']
        ])->first();

        $tgl_kondisi = $tgl;
        if ($trx_pembagian_laba) {
            $tgl_kondisi = $trx_pembagian_laba->tgl_transaksi;
        }

        $data['tanggal_kondisi'] = Tanggal::tglLatin(date('Y-m-d', strtotime($tgl_kondisi)));
        $data['sub_judul'] = 'Tahun ' . ($thn - 1);
        $data['tgl'] = Tanggal::tahun($tgl) - 1;

        $data['tahun_tb'] = $thn;
        $data['surplus'] = $keuangan->laba_rugi(($data['tahun'] - 1) . '-13-00');
        $data['akun3'] = AkunLevel3::where('kode_akun', 'LIKE', '2.1.01.%')
            ->orwhere('kode_akun', 'LIKE', '1.1.04.%')->with([
                'rek',
                'rek.saldo' => function ($query) use ($data) {
                    $query->where([
                        ['tahun', $data['tahun_tb'] - 1],
                        ['bulan', '13']
                    ]);
                }
            ])->get();

        $data['tgl_transaksi'] = $thn . '-12-31';
        $data['laporan'] = 'Alokasi Laba';
        $view = view('pelaporan.view.tutup_buku.alokasi_laba', $data)->render();

        if ($data['type'] == 'pdf') {
            $pdf = PDF::loadHTML($view);
            return $pdf->stream();
        } else {
            return $view;
        }
    }

    private function jurnal_tutup_buku(array $data)
    {
        $thn = $data['tahun'] - 1;
        $bln = 1;
        $hari = 1;

        $tgl = $thn . '-' . $bln . '-' . $hari;
        $data['tanggal_kondisi'] = Tanggal::tglLatin(date('Y-m-d', strtotime($tgl)));
        $data['sub_judul'] = 'Tahun ' . Tanggal::tahun($tgl);
        $data['tgl'] = Tanggal::tahun($tgl);
        $data['saldo'] = Saldo::where([
            ['tahun', $thn],
            ['bulan', '13']
        ])->with('rek')->orderBy('kode_akun', 'ASC')->get();
        $data['rek'] = Rekening::where('kode_akun', '3.2.01.01')->first();

        $data['tgl_transaksi'] = $thn . '-12-31';
        $data['laporan'] = 'Jurnal Awal Tahun';
        $view = view('pelaporan.view.tutup_buku.jurnal', $data)->render();

        if ($data['type'] == 'pdf') {
            $pdf = PDF::loadHTML($view);
            return $pdf->stream();
        } else {
            return $view;
        }
    }

    private function neraca_tutup_buku(array $data)
    {
        $keuangan = new Keuangan;

        $thn = $data['tahun'];
        $bln = 1;
        $hari = 1;

        $tgl = $thn . '-' . $bln . '-' . $hari;
        $data['tanggal_kondisi'] = Tanggal::tglLatin(date('Y-m-d', strtotime($tgl)));
        $data['sub_judul'] = 'Tahun' . ' ' . Tanggal::tahun($tgl);
        $data['tgl'] = Tanggal::tahun($tgl);

        $data['debit'] = 0;
        $data['kredit'] = 0;

        $data['akun1'] = AkunLevel1::where('lev1', '<=', '3')->with([
            'akun2',
            'akun2.akun3',
            'akun2.akun3.rek',
            'akun2.akun3.rek.saldo' => function ($query) use ($data) {
                $query->where([
                    ['tahun', $data['tahun']],
                    ['bulan', '0']
                ]);
            },
        ])->orderBy('kode_akun', 'ASC')->get();

        $data['laporan'] = 'Neraca Awal Tahun';
        $view = view('pelaporan.view.tutup_buku.neraca', $data)->render();

        if ($data['type'] == 'pdf') {
            $pdf = PDF::loadHTML($view);
            return $pdf->stream();
        } else {
            return $view;
        }
    }

    private function CALK_tutup_buku(array $data)
    {
        $keuangan = new Keuangan;

        $thn = $data['tahun'];
        $bln = 1;
        $hari = 1;

        $tgl = $thn . '-' . $bln . '-' . $hari;
        $data['tanggal_kondisi'] = Tanggal::tglLatin(date('Y-m-d', strtotime($tgl)));
        $data['tgl'] = Tanggal::tahun($tgl);
        $data['nama_tgl'] = 'Awal Tahun ' . $thn;
        $data['sub_judul'] = 'Awal Tahun ' . $thn;

        $data['debit'] = 0;
        $data['kredit'] = 0;

        $data['akun1'] = AkunLevel1::where('lev1', '<=', '3')->with([
            'akun2',
            'akun2.akun3',
            'akun2.akun3.rek',
            'akun2.akun3.rek.kom_saldo' => function ($query) use ($data) {
                $query->where([
                    ['tahun', $data['tahun']],
                    ['bulan', '0']
                ]);
            }
        ])->orderBy('kode_akun', 'ASC')->get();

        $data['keterangan'] = Calk::where([
            ['lokasi', Session::get('lokasi')],
            ['tanggal', 'LIKE', $data['tahun'] . '-' . $data['bulan'] . '%']
        ])->first();

        $data['sekr'] = User::where([
            ['level', '1'],
            ['jabatan', '2'],
            ['lokasi', Session::get('lokasi')],
        ])->first();

        $data['bend'] = User::where([
            ['level', '1'],
            ['jabatan', '3'],
            ['lokasi', Session::get('lokasi')],
        ])->first();

        $data['pengawas'] = User::where([
            ['level', '3'],
            ['jabatan', '1'],
            ['lokasi', Session::get('lokasi')],
        ])->first();

        $data['dir_utama'] = User::where([
            ['level', '2'],
            ['jabatan', '65'],
            ['lokasi', Session::get('lokasi')],
        ])->first();

        $data['saldo_calk'] = Saldo::where([
            ['kode_akun', $data['kec']->kd_kec],
            ['tahun', $thn]
        ])->get();

        $data['laporan'] = 'CALK Awal Tahun';
        $view = view('pelaporan.view.tutup_buku.calk', $data)->render();

        if ($data['type'] == 'pdf') {
            $pdf = PDF::loadHTML($view);
            return $pdf->stream();
        } else {
            return $view;
        }
    }

    private function laba_rugi_tutup_buku(array $data)
    {
        $keuangan = new Keuangan;

        $thn = $data['tahun'];
        $bln = 1;
        $hari = 1;
        $awal_tahun = $thn . '-01-01';

        $tgl = $thn . '-' . $bln . '-' . $hari;
        $data['tanggal_kondisi'] = Tanggal::tglLatin(date('Y-m-d', strtotime($tgl)));
        $data['sub_judul'] = 'Awal Tahun ' . $thn;
        $data['tgl'] = Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
        $data['bulan_lalu'] = date('Y-m-t', strtotime('-1 month', strtotime($thn . '-' . $bln . '-10')));
        $data['header_lalu'] = 'Bulan Lalu';
        $data['header_sekarang'] = 'Bulan Ini';

        $jenis = 'Tahunan';
        if ($data['bulanan']) {
            $jenis = 'Bulanan';
        }

        $laba_rugi = $keuangan->laporan_laba_rugi($tgl, $jenis);
        $data['pph'] = $keuangan->beban_pajak($tgl, $jenis);
        $data['pendapatan'] = $laba_rugi['pendapatan'];
        $data['beban'] = $laba_rugi['beban'];
        $data['pendapatanNOP'] = $laba_rugi['pendapatan_non_ops'];
        $data['bebanNOP'] = $laba_rugi['beban_non_ops'];

        $view = view('pelaporan.view.laba_rugi', $data)->render();

        if ($data['type'] == 'pdf') {
            $pdf = PDF::loadHTML($view);
            return $pdf->stream();
        } else {
            return $view;
        }
    }

    public function beritaAcara()
    {
        $data['kec'] = Kecamatan::where('id', Session::get('lokasi'))->with([
            'kabupaten'
        ])->first();

        $tgl_pakai = $data['kec']->tgl_pakai;
        $minimal_pakai = '2023-01-01';
        if (strtotime($tgl_pakai) < strtotime($minimal_pakai)) {
            $tgl_pakai = $minimal_pakai;
        }

        $tahun_pakai = Tanggal::tahun($tgl_pakai);
        $data['rekening'] = Rekening::with([
            'kom_saldo' => function ($query) use ($tahun_pakai) {
                $query->where([
                    ['tahun', $tahun_pakai],
                    ['bulan', '0']
                ]);
            },
            'saldo' => function ($query) use ($tahun_pakai) {
                $query->where([
                    ['tahun', $tahun_pakai - 1],
                    ['bulan', '12']
                ]);
            }
        ])->get();

        $data['kom_aset'] = AkunLevel1::where('lev1', '1')->with([
            'saldo_awal',
            'saldo' => function ($query) use ($tahun_pakai) {
                $query->where([
                    ['tahun', $tahun_pakai],
                    ['bulan', '0']
                ]);
            }
        ])->first();

        $data['direktur'] = User::where([
            ['jabatan', '1'],
            ['level', '1'],
            ['lokasi', Session::get('lokasi')]
        ])->first();

        $data['bendahara'] = User::where([
            ['jabatan', '3'],
            ['level', '1'],
            ['lokasi', Session::get('lokasi')]
        ])->first();

        $view = view('pelaporan.view.ba_pergantian_laporan', $data)->render();

        $pdf = PDF::loadHTML($view);
        return $pdf->stream();
    }

    public function mou()
    {
        $keuangan = new Keuangan;
        $kec = Kecamatan::where('id', Session::get('lokasi'))->with('kabupaten', 'desa', 'ttd')->first();
        $kab = $kec->kabupaten;

        $data['logo'] = $kec->logo;
        $data['nama_lembaga'] = $kec->nama_lembaga_sort;
        $data['nama_kecamatan'] = $kec->sebutan_kec . ' ' . $kec->nama_kec;

        if (Keuangan::startWith($kab->nama_kab, 'KOTA') || Keuangan::startWith($kab->nama_kab, 'KAB')) {
            $data['nama_kecamatan'] .= ' ' . ucwords(strtolower($kab->nama_kab));
            $data['nama_kabupaten'] = ucwords(strtolower($kab->nama_kab));
        } else {
            $data['nama_kecamatan'] .= ' Kabupaten ' . ucwords(strtolower($kab->nama_kab));
            $data['nama_kabupaten'] = ' Kabupaten ' . ucwords(strtolower($kab->nama_kab));
        }

        $jabatan = '1';
        $level = '1';
        if (Session::get('lokasi') == '207') {
            $jabatan = '1';
            $level = '2';
        }

        $data['dir'] = User::where([
            ['lokasi', Session::get('lokasi')],
            ['jabatan', $jabatan],
            ['level', $level]
        ])->first();

        $data['kec'] = $kec;
        $data['keu'] = $keuangan;

        $view = view('pelaporan.view.mou', $data)->render();

        $pdf = PDF::loadHTML($view)->setPaper('A4', 'potrait');
        return $pdf->stream();
    }

    public function ts()
    {
        $data['kec'] = Kecamatan::where('id', Session::get('lokasi'))->first();

        $view = view('pelaporan.view.ts', $data)->render();
        $pdf = PDF::loadHTML($view)->setPaper([0, 0, 595.28, 352], 'potrait');
        return $pdf->stream();
    }

    public function invoice(AdminInvoice $invoice)
    {
        $data['app_name'] = env('APP_NAME');
        $data['inv'] = AdminInvoice::where('idv', $invoice->idv)->with([
            'jp',
            'trx',
            'usaha',
            'usaha.d',
            'usaha.d.kec',
            'usaha.d.kec.kabupaten',
        ])->first();

        $view = view('pelaporan.view.invoice', $data)->render();
        $pdf = PDF::loadHTML($view)->setPaper('A4', 'potrait');
        return $pdf->stream();
    }
}
