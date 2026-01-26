@php
    $peraturan_desa = $usaha->peraturan_desa;
@endphp

<form action="/pengaturan/lembaga/{{ $usaha->id }}" method="post" id="FormLembaga">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-md-2">
            <div class="form-group">
                <label for="id">ID.</label>
                <input autocomplete="off" type="text" name="id" id="id" class="form-control form-control-sm"
                    value="{{ $usaha->id }}" readonly>
                <small class="text-danger" id="msg_id"></small>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label for="kd_desa">Kode desa.</label>
                <input autocomplete="off" type="text" name="kd_desa" id="kd_desa"
                    class="form-control form-control-sm" value="{{ $usaha->kd_desa }}" readonly>
                <small class="text-danger" id="msg_kd_kec"></small>
            </div>
        </div>
        <div class="col-md-8">
            <div class="form-group">
                <label for="nama_bumdes">Nama Bumdes</label>
                <input autocomplete="off" type="text" name="nama_bumdes" id="nama_bumdes"
                    class="form-control form-control-sm" value="{{ $usaha->nama_usaha }}">
                <small class="text-danger" id="msg_nama_bumdes"></small>
            </div>
        </div>
        <div class="col-md-8">
            <div class="form-group">
                <label>NPWP</label>
                <input type="text" name="npwp" id="npwp" class="form-control form-control-sm"
                    placeholder="{{ $usaha->npwp }}" value="{{ $usaha->npwp }}">
                <small class="text-danger" id="msg_npwp"></small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="tanggal_npwp">Tanggal NPWP</label>
                <input autocomplete="off" type="text" name="tanggal_npwp" id="tanggal_npwp"
                    class="form-control form-control-sm date" value="{{ Tanggal::tglIndo($usaha->tgl_npwp) }}">
                <small class="text-danger" id="msg_tanggal_npwp"></small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="nomor_badan_hukum">Badan Hukum No. </label>
                <input autocomplete="off" type="text" name="nomor_badan_hukum" id="nomor_badan_hukum"
                    class="form-control form-control-sm" value="{{ $usaha->nomor_bh }}">
                <small class="text-danger" id="msg_nomor_badan_hukum"></small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="telpon">Telpon</label>
                <input autocomplete="off" type="text" name="telpon" id="telpon"
                    class="form-control form-control-sm" value="{{ $usaha->telpon }}">
                <small class="text-danger" id="msg_telpon"></small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="email">Email</label>
                <input autocomplete="off" type="email" name="email" id="email"
                    class="form-control form-control-sm" value="{{ $usaha->email }}">
                <small class="text-danger" id="msg_email"></small>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <label for="alamat">Alamat</label>
                <textarea name="alamat" id="alamat" class="form-control form-control-sm" placeholder="Alamat">{{ $usaha->alamat }}</textarea>
                <small class="text-danger" id="msg_alamat"></small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="web_utama">Web Utama</label>
                <input autocomplete="off" type="text" name="web_utama" id="web_utama"
                    class="form-control form-control-sm" value="{{ $usaha->domain }}" readonly>
                <small class="text-danger" id="msg_web_utama"></small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="web_alternatif">Web Alternatif</label>
                <input autocomplete="off" type="text" name="web_alternatif" id="web_alternatif"
                    class="form-control form-control-sm" value="{{ $usaha->domain_alt }}" readonly>
                <small class="text-danger" id="msg_web_alternatif"></small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="peraturan_desa">Peraturan Desa Nomor</label>
                <input autocomplete="off" type="text" name="peraturan_desa" id="peraturan_desa"
                    class="form-control form-control-sm" value="{{ $peraturan_desa }}">
                <small class="text-danger" id="msg_peraturan_desa"></small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group mb-1">
                <label class="form-label fw-bold mb-0">Jenis Pengelola Keuangan</label>

                <div class="d-flex mt-1" style="column-gap: 3rem;">
                    <div class="form-check d-flex align-items-center mb-0">
                        <input class="form-check-input mt-0" type="radio" name="jenis_akun" id="jenis_akun_umum"
                            value="5" {{ $usaha->jenis_akun == 5 ? 'checked' : '' }}>
                        <label class="form-check-label ms-1 lh-1" for="jenis_akun_umum">
                            A. Umum
                        </label>
                    </div>

                    <div class="form-check d-flex align-items-center mb-0">
                        <input class="form-check-input mt-0" type="radio" name="jenis_akun" id="jenis_akun_dagang"
                            value="7" {{ $usaha->jenis_akun == 7 ? 'checked' : '' }}>
                        <label class="form-check-label ms-1 lh-1" for="jenis_akun_dagang">
                            B. Perdagangan
                        </label>
                    </div>
                </div>

                <div class="text-muted mt-1" style="font-size: 13px;">
                    Pilih A / B untuk menentukan jenis pengelolaan keuangan usaha </div>
            </div>
        </div>
    </div>
</form>

<div class="d-flex justify-content-end">
    <button type="button" id="SimpanLembaga" data-target="#FormLembaga"
        class="btn btn-sm btn-warning mb-0 btn-simpan">
        Simpan Perubahan
    </button>
</div>
