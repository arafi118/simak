@extends('pelaporan.layout.base')

@section('content')
    @if (Session::get('jenis_akun') == 8)
    @else
        <br>
    @endif
    <table border="0">
        <tr>
            <td width="5%">Nomor</td>
            @if (Session::get('jenis_akun') == 8)
                <td width="50%">: </td>
            @else
                <td width="50%">: ______________________</td>
            @endif
            <td width="45%" align="right">
                @if (Session::get('jenis_akun') == 8)
                    {{ $kab->nama_kab }},
                @else
                    {{ $kec->nama_kec }},
                @endif
                {{ $tgl }}
            </td>
        </tr>
        <tr>
            <td>Lampiran</td>
            <td>: 1 Bendel</td>
        </tr>
        <tr>
            <td>Perihal</td>
            <td>: Laporan Keuangan</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td style="padding-left: 8px;">
                @if (Session::get('jenis_akun') == 8)
                    &nbsp; Sampai Dengan {{ $sub_judul }}
                @else
                    &nbsp; <u>Sampai Dengan {{ $sub_judul }}</u>
                @endif

            </td>
        </tr>
        <tr>
            <td colspan="3" height="15"></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td colspan="2" align="left" style="padding-left: 8px;">

                @if (Session::get('jenis_akun') == 8)
                    <div><b>Kepada Yth.</b></div>
                    <div><b> Kepala Dinas Koperasi dan UMKM Provinsi Jawa Tengah</b></div>
                @else
                    <div><b>Kepada Yth.</b></div>
                    <div><b>Kepala Dinas PMD {{ $nama_kabupaten }}</b></div>
                    <div><b>Di {{ $kab->alamat_kab }}.</b></div>
                @endif

            </td>
        </tr>
        <tr>
            <td colspan="3" height="15"></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td colspan="2" style="padding-left: 8px; text-align: justify;">
                <div>Dengan Hormat,</div>
                <div>
                    Bersama ini kami sampaikan Laporan Keuangan
                    {{ str_ireplace(['<br>', '<br/>', '<br />'], ' ', $usaha->nama_usaha) }}
                    {{ $usaha->d->sebutan_desa->sebutan_desa }} {{ $usaha->d->nama_desa }} {{ $kec->nama_kec }} sampai
                    dengan
                    {{ $sub_judul }} sebagai berikut:
                    <ol>
                        <li>Laporan Neraca</li>
                        @if (Session::get('jenis_akun') == 8)
                            <li>Laporan Perhitungan Hasil Usaha</li>
                        @else
                            <li>Laporan Rugi/Laba</li>
                        @endif
                        < <li>Neraca Saldo</li>
                            <li>Laporan Perubahan Ekuitas</li>
                            <li>Catatan Atas Laporan Keuangan (CALK)</li>
                    </ol>
                </div>
                <div>
                    Demikian laporan kami sampaikan, atas perhatiannya kami ucapkan terima kasih.
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="3" height="15"></td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td align="center">
                <div>{!! $nama_usaha !!} {{ $kec->nama_kec }}</div>
                <div>
                    @if (Session::get('jenis_akun') == 8)
                        Ketua,
                    @elseif ($dir_utama)
                        {{ $dir_utama->j->nama_jabatan }},
                    @else
                        {{ $dir->j->nama_jabatan }},
                    @endif
                </div>

                <br>
                <br>
                <br>
                <br>
                <div>
                    @if ($dir_utama)
                        <b>{{ $dir_utama->namadepan . ' ' . $dir_utama->namabelakang }}</b>
                    @else
                        <b>{{ $dir->namadepan . ' ' . $dir->namabelakang }}</b>
                    @endif
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <div>
                    Tembusan :
                    <ol>
                        <li>Arsip</li>
                    </ol>
                </div>
            </td>
        </tr>
    </table>
@endsection
