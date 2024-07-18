<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\KecamatanController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\UpkController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DesaController;
use App\Http\Controllers\GenerateController;
use App\Http\Controllers\PelaporanController;
use App\Http\Controllers\SopController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\UserController;
use App\Models\Kecamatan;
use App\Models\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/master', [AdminAuthController::class, 'index'])->middleware('guest');
Route::post('/master/login', [AdminAuthController::class, 'login'])->middleware('guest');

Route::group(['prefix' => 'master', 'as' => 'master.', 'middleware' => 'master'], function () {
    Route::get('/dashboard', [AdminController::class, 'index']);
    Route::get('/simpan_saldo', [DashboardController::class, 'simpanSaldo']);

    Route::get('/kecamatan/{kd_prov}/{kd_kab}/{kd_kec}', [KecamatanController::class, 'index']);

    Route::resource('/users', AdminUserController::class);

    Route::get('/laporan', [AdminController::class, 'laporan']);

    Route::get('/buat_invoice', [InvoiceController::class, 'index']);
    Route::get('/nomor_invoice', [InvoiceController::class, 'InvoiceNo']);
    Route::get('/jumlah_tagihan', [InvoiceController::class, 'Tagihan']);

    Route::get('/unpaid', [InvoiceController::class, 'Unpaid']);
    Route::get('/{invoice}/unpaid', [InvoiceController::class, 'DetailUnpaid']);

    Route::get('/paid', [InvoiceController::class, 'Paid']);
    Route::get('/{invoice}/paid', [InvoiceController::class, 'DetailPaid']);

    Route::post('/buat_invoice', [InvoiceController::class, 'store']);
    Route::put('/{invoice}/simpan', [InvoiceController::class, 'simpan']);

    Route::resource('/menu', MenuController::class);

    Route::get('/migrasi_upk/server/{server}', [UpkController::class, 'Server']);
    Route::get('/migrasi_upk/{id}/rekening', [UpkController::class, 'Rekening']);
    Route::get('/migrasi_upk/{id}/rekening/insert', [UpkController::class, 'InsertRekening']);
    Route::get('/migrasi_upk/{id}/transaksi', [UpkController::class, 'Transaksi']);
    Route::get('/migrasi_upk/{id}/desa', [UpkController::class, 'Desa']);

    Route::resource('/migrasi_upk', UpkController::class);

    Route::post('/logout', [AdminAuthController::class, 'logout']);
});

Route::get('/', [AuthController::class, 'index'])->middleware('guest')->name('/');
Route::get('/register', [AuthController::class, 'register'])->middleware('guest')->name('/');
Route::get('/register/user', [AuthController::class, 'user'])->middleware('guest');

Route::get('/ambil_prov', [AuthController::class, 'provinsi'])->middleware('guest')->name('/');
Route::get('/ambil_kab/{kode}', [AuthController::class, 'kabupaten'])->middleware('guest')->name('/');
Route::get('/ambil_kec/{kode}', [AuthController::class, 'kecamatan'])->middleware('guest')->name('/');
Route::get('/ambil_des/{kode}', [AuthController::class, 'desa'])->middleware('guest')->name('/');

Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/register', [AuthController::class, 'store'])->middleware('guest');

Route::get('/pelaporan', [PelaporanController::class, 'index'])->middleware('basic');
Route::get('/pelaporan/sub_laporan/{file}', [PelaporanController::class, 'subLaporan'])->middleware('basic');
Route::post('/pelaporan/preview', [PelaporanController::class, 'preview'])->middleware('basic');
Route::post('/pelaporan/preview/{lokasi?}', [PelaporanController::class, 'preview'])->middleware('basic');

Route::get('/pelaporan/ba_bumdesma', [PelaporanController::class, 'beritaAcara'])->middleware('auth');
Route::get('/pelaporan/mou', [PelaporanController::class, 'mou'])->middleware('auth');
Route::get('/pelaporan/ts', [PelaporanController::class, 'ts'])->middleware('auth');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth');
Route::get('/piutang_jasa', [DashboardController::class, 'piutang'])->middleware('auth');
Route::get('/pelaporan/invoice/{invoice}', [PelaporanController::class, 'invoice']);
Route::get('/simpan_saldo', [DashboardController::class, 'simpanSaldo'])->middleware('auth');

Route::post('/dashboard/jatuh_tempo', [DashboardController::class, 'jatuhTempo'])->middleware('auth');
Route::post('/dashboard/nunggak', [DashboardController::class, 'nunggak'])->middleware('auth');
Route::post('/dashboard/tagihan', [DashboardController::class, 'tagihan'])->middleware('auth');
Route::get('/dashboard/pinjaman', [DashboardController::class, 'pinjaman'])->middleware('auth');
Route::get('/dashboard/pemanfaat', [DashboardController::class, 'pemanfaat'])->middleware('auth');

Route::get('/pengaturan/sop', [SopController::class, 'index'])->middleware('auth');
Route::get('/pengaturan/ttd_pelaporan', [SopController::class, 'ttdPelaporan'])->middleware('auth');
Route::get('/pengaturan/ttd_spk', [SopController::class, 'ttdSpk'])->middleware('auth');

Route::get('/pengaturan/coa', [SopController::class, 'coa'])->middleware('auth');
Route::post('/pengaturan/coa', [SopController::class, 'createCoa'])->middleware('auth');
Route::put('/pengaturan/coa/{rekening}', [SopController::class, 'updateCoa'])->middleware('auth');
Route::delete('/pengaturan/coa/{rekening}', [SopController::class, 'deleteCoa'])->middleware('auth');

Route::put('/pengaturan/lembaga/{usaha}', [SopController::class, 'lembaga'])->middleware('auth');
Route::put('/pengaturan/pengelola/{usaha}', [SopController::class, 'pengelola'])->middleware('auth');
Route::put('/pengaturan/pinjaman/{usaha}', [SopController::class, 'pinjaman'])->middleware('auth');
Route::put('/pengaturan/asuransi/{usaha}', [SopController::class, 'asuransi'])->middleware('auth');
Route::put('/pengaturan/spk/{usaha}', [SopController::class, 'spk'])->middleware('auth');
Route::put('/pengaturan/logo/{usaha}', [SopController::class, 'logo'])->middleware('auth');
Route::put('/pengaturan/calk/{usaha}', [SopController::class, 'calk'])->middleware('auth');
Route::put('/pengaturan/pesan_whatsapp/{usaha}', [SopController::class, 'pesanWhatsapp'])->middleware('auth');
Route::put('/pengaturan/berita_acara/{usaha}', [SopController::class, 'beritaAcara'])->middleware('auth');
Route::get('/pengaturan/local_view/{key}/{val?}', [SopController::class, 'localView'])->middleware('auth');

Route::post('/pengaturan/whatsapp/{token}', [SopController::class, 'whatsapp'])->middleware('auth');

Route::get('/pengaturan/invoice', [SopController::class, 'invoice'])->middleware('auth');
Route::get('/pengaturan/{inv}/invoice', [SopController::class, 'detailInvoice'])->middleware('auth');

Route::post('/pengaturan/sop/simpanttdpelaporan', [SopController::class, 'simpanTtdPelaporan'])->middleware('auth');

Route::resource('/database/desa', DesaController::class)->middleware('auth');

Route::get('/transaksi/jurnal_umum/', [TransaksiController::class, 'jurnalUmum'])->middleware('auth');
Route::get('/transaksi/jurnal_umum/{transaksi}', [TransaksiController::class, 'editTransaksi'])->middleware('auth');

Route::get('/transaksi/jurnal_angsuran', [TransaksiController::class, 'jurnalAngsuran'])->middleware('auth');
Route::get('/transaksi/tutup_buku', [TransaksiController::class, 'jurnalTutupBuku'])->middleware('auth');
Route::get('/trasaksi/saldo/{kode_akun}', [TransaksiController::class, 'saldo'])->middleware('auth');

Route::get('/transaksi/ambil_rekening/{id}', [TransaksiController::class, 'rekening'])->middleware('auth');
Route::get('/transaksi/form_nominal/', [TransaksiController::class, 'form'])->middleware('auth');
Route::get('/transaksi/form_angsuran/{id_pinkel}', [TransaksiController::class, 'formAngsuran'])->middleware('auth');
Route::get('/transaksi/angsuran/target/{id_pinkel}', [TransaksiController::class, 'targetAngsuran'])->middleware('auth');

Route::get('/transaksi/data/{idt}', [TransaksiController::class, 'data'])->middleware('auth');

Route::get('/transaksi/tutup_buku/saldo_awal/{tahun}', [TransaksiController::class, 'saldoAwal'])->middleware('auth');
Route::post('/transaksi/tutup_buku/saldo', [TransaksiController::class, 'saldoTutupBuku'])->middleware('auth');
Route::post('/transaksi/tutup_buku', [TransaksiController::class, 'simpanTutupBuku'])->middleware('auth');

Route::post('/transaksi/simpan_laba', [TransaksiController::class, 'simpanAlokasiLaba'])->middleware('auth');
Route::post('/transaksi/reversal', [TransaksiController::class, 'reversal'])->middleware('auth');
Route::post('/transaksi/hapus', [TransaksiController::class, 'hapus'])->middleware('auth');

Route::get('/transaksi/angsuran/lpp/{id}', [TransaksiController::class, 'lpp'])->middleware('auth');
Route::get('/transaksi/angsuran/detail_angsuran/{id}', [TransaksiController::class, 'detailAngsuran'])->middleware('auth');
Route::get('/transaksi/detail_transaksi/', [TransaksiController::class, 'detailTransaksi'])->middleware('auth');

Route::post('/transaksi/angsuran', [TransaksiController::class, 'angsuran'])->middleware('auth');
Route::post('/transaksi/angsuran/cetak_bkm', [TransaksiController::class, 'cetakBkm'])->middleware('auth');

Route::get('/transaksi/generate_real/{id_pinkel}', [TransaksiController::class, 'generateReal'])->middleware('auth');
Route::get('/transaksi/regenerate_real/{id_pinkel}', [TransaksiController::class, 'realisasi'])->middleware('auth');

Route::get('/transaksi/angsuran/form_anggota/{id_pinkel}', [TransaksiController::class, 'formAnggota'])->middleware('auth');
Route::get('/angsuran/notifikasi/{idtp}', [TransaksiController::class, 'notifikasi'])->middleware('auth');

Route::get('/transaksi/dokumen/kuitansi/{id}', [TransaksiController::class, 'kuitansi'])->middleware('auth');
Route::get('/transaksi/dokumen/kuitansi_thermal/{id}', [TransaksiController::class, 'kuitansi_thermal'])->middleware('auth');
Route::get('/transaksi/dokumen/bkk/{id}', [TransaksiController::class, 'bkk'])->middleware('auth');
Route::get('/transaksi/dokumen/bkm/{id}', [TransaksiController::class, 'bkm'])->middleware('auth');
Route::get('/transaksi/dokumen/bm/{id}', [TransaksiController::class, 'bm'])->middleware('auth');

Route::get('/transaksi/dokumen/struk/{id}', [TransaksiController::class, 'struk'])->middleware('auth');
Route::get('/transaksi/dokumen/struk_matrix/{id}', [TransaksiController::class, 'strukMatrix'])->middleware('auth');
Route::get('/transaksi/dokumen/struk_thermal/{id}', [TransaksiController::class, 'strukThermal'])->middleware('auth');
Route::get('/transaksi/dokumen/bkm_angsuran/{id}', [TransaksiController::class, 'bkmAngsuran'])->middleware('auth');
Route::get('/transaksi/dokumen/bkk_angsuran/{id}', [TransaksiController::class, 'bkkAngsuran'])->middleware('auth');
Route::post('/transaksi/dokumen/cetak', [TransaksiController::class, 'cetak'])->middleware('auth');

Route::get('/transaksi/ebudgeting', [TransaksiController::class, 'ebudgeting'])->middleware('auth');
Route::post('/transaksi/anggaran', [TransaksiController::class, 'formAnggaran'])->middleware('auth');
Route::post('/transaksi/simpan_anggaran', [TransaksiController::class, 'simpanAnggaran'])->middleware('auth');

Route::get('/transaksi/taksiran_pajak', [TransaksiController::class, 'taksiranPajak'])->middleware('auth');
Route::post('/transaksi/taksiran_pajak', [TransaksiController::class, 'cetakTaksiranPajak'])->middleware('auth');
Route::get('/transaksi/pendapatan/{tahun}/{bulan}', [TransaksiController::class, 'pendapatan'])->middleware('auth');

Route::resource('/transaksi', TransaksiController::class)->middleware('auth');

Route::resource('/profil', UserController::class);

Route::get('/sync/{lokasi}', [DashboardController::class, 'sync'])->middleware('auth');
Route::get('/link', function () {
    $target = '/home/siupk/public_html/simak_apps/storage/app/public';
    $shortcut = '/home/siupk/public_html/simak_apps/public/storage';
    symlink($target, $shortcut);
});

Route::get('/user', function () {
    $kec = Kecamatan::where('web_kec', request()->getHost())->orwhere('web_alternatif', request()->getHost())->with('kabupaten')->first();
    $users = User::where('lokasi', $kec->id)->with('l', 'j')->orderBy('level', 'ASC')->orderBy('jabatan', 'ASC')->get();

    return view('welcome', ['users' => $users, 'kec' => $kec]);
});

Route::get('/download/{file}', function ($file) {
    return response()->download(storage_path('app/public/docs/' . $file));
})->name('download');

Route::get('/generate', [GenerateController::class, 'index']);
Route::get('/generate/kelompok', [GenerateController::class, 'kelompok']);
Route::post('/generate/save/{offset?}', [GenerateController::class, 'generate']);

Route::get('/unpaid', [DashboardController::class, 'unpaid'])->middleware('auth');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

Route::get('/{invoice}', [PelaporanController::class, 'invoice']);
