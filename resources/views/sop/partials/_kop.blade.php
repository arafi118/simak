<form action="/pengaturan/kop_laporan/{{ $usaha->id }}" method="post" id="KopLaporan">
    @csrf
    @method('PUT')

    <textarea name="kop-laporan" id="kop-laporan" rows="20">{!! $usaha->kop_laporan !!}</textarea>
    <textarea name="kop_laporan" id="kop_laporan" class="d-none">{!! $usaha->kop_laporan !!}</textarea>
</form>

<div class="d-flex justify-content-end mt-2">
    <button type="button" id="SimpanKopLaporan" data-target="#KopLaporan"
        class="btn btn-sm btn-warning mb-0 btn-simpan">
        Simpan Perubahan
    </button>
</div>
