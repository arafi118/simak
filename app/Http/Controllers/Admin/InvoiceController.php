<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminApp;
use App\Models\AdminInvoice;
use App\Models\AdminJenisPembayaran;
use App\Models\AdminRekening;
use App\Models\AdminTransaksi;
use App\Models\Usaha;
use App\Utils\Tanggal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\DataTables;

class InvoiceController extends Controller
{
    public function index()
    {
        $app = AdminApp::first();
        $jenis = AdminJenisPembayaran::all();
        $invoice = $this->InvoiceNo(date('Y-m-d'))->getData();

        $usaha = Usaha::with([
            'd.sebutan_desa',
            'd.kec.kabupaten'
        ])->get();

        $title = 'Tambah Invoice';
        return view('admin.invoice.index')->with(compact('title', 'app', 'usaha', 'invoice', 'jenis'));
    }

    public function store(Request $request)
    {
        $data = $request->only([
            'tgl_invoice',
            'nomor_invoice',
            'nama_usaha',
            'jenis_pembayaran',
            'tgl_pakai',
            'nominal',
        ]);

        $validate = Validator::make($data, [
            'tgl_invoice' => 'required',
            'nomor_invoice' => 'required',
            'nama_usaha' => 'required',
            'jenis_pembayaran' => 'required',
            'tgl_pakai' => 'required',
            'nominal' => 'required'
        ]);

        if ($validate->fails()) {
            if ($validate->fails()) {
                return response()->json($validate->errors(), Response::HTTP_MOVED_PERMANENTLY);
            }
        }

        $data['usaha'] = json_decode($request->nama_usaha, true);
        $jp = AdminJenisPembayaran::where('id', $request->jenis_pembayaran)->first();
        $invoice = AdminInvoice::create([
            'lokasi' => $data['usaha']['id'],
            'nomor' => $request->nomor_invoice,
            'jenis_pembayaran' => $request->jenis_pembayaran,
            'tgl_invoice' => Tanggal::tglNasional($request->tgl_invoice),
            'tgl_lunas' => Tanggal::tglNasional($request->tgl_invoice),
            'status' => 'UNPAID',
            'jumlah' => str_replace(',', '', str_replace('.00', '', $request->nominal)),
            'id_user' => auth()->guard('master')->user()->id
        ]);

        return response()->json([
            'success' => true,
            'msg' => 'Invoice ' . $jp->nama_jp . ' No. ' . $request->nomor_invoice . ' berhasil disimpan.',
            'nomor' => $request->nomor_invoice,
            'id' => $invoice->idv
        ]);
    }

    public function paid()
    {
        if (request()->ajax()) {
            $adminInvoice = AdminInvoice::with('usaha')->where('status', 'PAID')->get();
            return DataTables::of($adminInvoice)
                ->editColumn('jumlah', function ($row) {
                    return number_format($row->jumlah);
                })->make();
        }

        $title = 'Invoice Paid';
        return view('admin.invoice.paid')->with(compact('title'));
    }

    public function unpaid()
    {
        if (request()->ajax()) {
            $adminInvoice = AdminInvoice::with('usaha')->withsum('trx', 'jumlah')->where('status', 'UNPAID')->get();
            return DataTables::of($adminInvoice)
                ->editColumn('jumlah', function ($row) {
                    return number_format($row->jumlah);
                })->addColumn('saldo', function ($row) {
                    if ($row->trx_sum_jumlah) {
                        return number_format($row->jumlah - $row->trx_sum_jumlah);
                    }

                    return number_format($row->jumlah);
                })->make();
        }

        $title = 'Invoice Unpaid';
        return view('admin.invoice.unpaid')->with(compact('title'));
    }

    public function show(AdminInvoice $invoice)
    {
        $rekening = AdminRekening::where('kd_rekening', '111.1001')->orwhere('kd_rekening', '121.1001')->orderBy('kd_rekening', 'DESC')->get();
        $jumlah_trx = AdminTransaksi::where('idv', $invoice->idv)->sum('jumlah');

        $title = 'Invoice #' . $invoice->nomor . ' - ' . $invoice->usaha->nama_usaha;
        return view('admin.invoice.detail')->with(compact('title', 'rekening', 'jumlah_trx', 'invoice'));
    }

    public function InvoiceNo($tgl = null)
    {
        $tanggal = ($tgl == null) ? Tanggal::tglNasional(request()->get('tgl_invoice')) : $tgl;

        $nomor_invoice = date('ymd', strtotime($tanggal));
        $invoice = AdminInvoice::where('tgl_invoice', $tanggal)->count();
        $nomor_urut = str_pad($invoice + 1, '2', '0', STR_PAD_LEFT);
        $nomor_invoice .= $nomor_urut;

        $batas_waktu = date('Y-m-d', strtotime('+1 month', strtotime($tanggal)));
        return response()->json([
            'nomor' => $nomor_invoice,
            'batas_waktu' => Tanggal::tglIndo($batas_waktu)
        ]);
    }

    public function update(Request $request, AdminInvoice $invoice)
    {
        $data = $request->only([
            'tgl_bayar',
            'nominal',
            'keterangan',
            'metode_pembayaran'
        ]);

        $validate = Validator::make($data, [
            'tgl_bayar' => 'required',
            'nominal' => 'required',
            'keterangan' => 'required',
            'metode_pembayaran' => 'required'
        ]);

        if ($validate->fails()) {
            if ($validate->fails()) {
                return response()->json($validate->errors(), Response::HTTP_MOVED_PERMANENTLY);
            }
        }

        $nominal = str_replace(',', '', str_replace('.00', '', $request->nominal));
        $invoice = AdminInvoice::where('idv', $invoice->idv)->withSum('trx', 'jumlah')->first();

        $rek = AdminRekening::where('kd_rekening', $request->metode_pembayaran)->first();
        $rek_debit = $rek->kd_rekening;
        $rek_kredit = $rek->pasangan;

        $saldo = $invoice->jumlah - ($invoice->trx_sum_jumlah + $nominal);
        $persen = round($nominal / $invoice->jumlah * 100);

        $lunas = false;
        if (($invoice->trx_sum_jumlah + $nominal) >= $invoice->jumlah) {
            $lunas = true;
            $inv = AdminInvoice::where('idv', $invoice->idv)->update([
                'tgl_lunas' => Tanggal::tglNasional($request->tgl_bayar),
                'status' => 'PAID'
            ]);

            $usaha = Usaha::where('id', $invoice->lokasi)->update([
                'masa_aktif' => date('Y-m-d', strtotime('+' . $invoice->usaha->tagihan_invoice . ' months', strtotime(Tanggal::tglNasional($request->tgl_bayar))))
            ]);
        }

        $trx = AdminTransaksi::create([
            'tgl_transaksi' => Tanggal::tglNasional($request->tgl_bayar),
            'rekening_debit' => $rek_debit,
            'rekening_kredit' => $rek_kredit,
            'idv' => $invoice->idv,
            'keterangan_transaksi' => $request->keterangan . ' (' . $persen . '%)',
            'jumlah' => $nominal,
            'urutan' => '0',
            'id_user' => auth()->guard('master')->user()->id
        ]);

        return response()->json([
            'success' => true,
            'msg' => 'Pembayaran Invoice Berhasil Disimpan.',
            'lunas' => $lunas,
            'nomor' => $invoice->nomor
        ]);
    }
}
