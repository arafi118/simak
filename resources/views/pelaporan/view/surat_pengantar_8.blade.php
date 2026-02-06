@extends('pelaporan.layout.base')

@section('content')
    <style>
        table {
            font-size: 14px;
        }
    </style>
    <table border="0">
        <tr>
            <td width="5%">Nomor</td>
            <td width="50%">: </td>
            <td width="45%" align="right">
                {{ $kab->nama_kab }}, {{ $tgl }}
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
                &nbsp; Sampai Dengan {{ $sub_judul }}
            </td>
        </tr>
        <tr>
            <td colspan="3" height="15"></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td colspan="2" align="left" style="padding-left: 8px;">
                <div><b>Kepada Yth.</b></div>
                <div><b> Kepala Dinas Koperasi dan UMKM Provinsi Jawa Tengah</b></div>
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
                        <li>Laporan Perhitungan Hasil Usaha</li>
                        <li>Neraca Saldo</li>
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
                <div>{!! ucwords(strtolower($nama_usaha)) !!}</div>
                <div>Ketua,</div>
                {{-- <div>
                    @if ($dir_utama)
                        {{ $dir_utama->j->nama_jabatan }},
                    @else
                        {{ $dir->j->nama_jabatan }},
                    @endif
                </div> --}}

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
                        <li>Kepala Dinas Koperasi dan UMKM Provinsi Jawa Tengah</li>
                        <li>Kepala Dinas Koperasi dan UMKM Kabupaten</li>
                        <li>Arsip</li>
                    </ol>
                </div>
            </td>
        </tr>
    </table>
@endsection
