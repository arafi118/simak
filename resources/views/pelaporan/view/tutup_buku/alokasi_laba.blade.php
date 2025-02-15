@php
    use App\Utils\Tanggal;

    $title_form = [
        1 => 'Penambahan Modal Bumdes',
    ];

    $jumlah_laba_ditahan = $surplus;
    $total = 0;
@endphp

@extends('pelaporan.layout.base')

@section('content')
    <table border="0" width="100%" cellspacing="0" cellpadding="0" style="font-size: 11px;">
        <tr>
            <td align="center">
                <div style="font-size: 18px;">
                    <b>ALOKASI PEMBAGIAN LABA USAHA</b>
                </div>
                <div style="font-size: 16px;">
                    <b>{{ strtoupper($sub_judul) }}</b>
                </div>
            </td>
        </tr>
    </table>

    <table border="0" width="100%" cellspacing="0" cellpadding="0" style="font-size: 11px;">
        <tr style="background: rgb(232, 232, 232); font-weight: bold; font-size: 12px;">
            <td height="20">
                <b>Laba/Rugi Tahun {{ $tahun - 1 }}</b>
            </td>
            <td align="right">
                <b>Rp. {{ number_format($surplus, 2) }}</b>
            </td>
        </tr>
        <tr style="background: rgb(74, 74, 74); color: #fff;">
            <td colspan="2" height="20">
                <b>Alokasi Laba Usaha</b>
            </td>
        </tr>
        @foreach ($akun3 as $akn3)
            <tr style="background: rgb(167, 167, 167); font-weight: bold;">
                <td colspan="2">{{ $akn3->nama_akun }}</td>
            </tr>

            @php
                $jumlah = 0;
            @endphp
            @foreach ($akn3->rek as $rek)
                @php
                    $saldo = 0;
                    if ($rek->saldo) {
                        $saldo = $rek->saldo->kredit;
                    }
                    $jumlah += floatval($saldo);
                    $total += floatval($saldo);

                    $bg = 'rgb(230, 230, 230)';
                    if ($loop->iteration % 2 == 0) {
                        $bg = 'rgba(255, 255, 255)';
                    }
                @endphp
                <tr style="background: {{ $bg }}">
                    <td>{{ $rek->nama_akun }}</td>
                    <td align="right">
                        Rp. {{ number_format(floatval($saldo), 2) }}
                    </td>
                </tr>
            @endforeach
            <tr style="background: rgb(150, 150, 150); font-weight: bold;">
                <td height="15">
                    <b>Jumlah</b>
                </td>
                <td align="right">
                    <b>Rp. {{ number_format($jumlah, 2) }}</b>
                </td>
            </tr>
        @endforeach

        <tr style="background: rgb(167, 167, 167); font-weight: bold;">
            <td colspan="2">LABA DITAHAN</td>
        </tr>

        @php
            $laba_ditahan = $surplus - $total;
        @endphp
        <tr style="background: rgb(230, 230, 230)">
            <td>Pemupukan Modal</td>
            <td align="right">
                Rp. {{ number_format($laba_ditahan, 2) }}
            </td>
        </tr>

        <tr style="background: rgb(150, 150, 150); font-weight: bold;">
            <td height="15">
                <b>Jumlah</b>
            </td>
            <td align="right">
                <b>Rp. {{ number_format($laba_ditahan, 2) }}</b>
            </td>
        </tr>


        <tr>
            <td colspan="2" style="padding: 0px !important;">
                <table class="p" border="0" width="100%" cellspacing="0" cellpadding="0"
                    style="font-size: 11px;">
                    <tr style="background: rgb(74, 74, 74); color: #fff;">
                        <td align="center" height="20">
                            <b>Total Alokasi Laba Usaha</b>
                        </td>
                        <td align="right">
                            <b>Rp. {{ number_format($total + $laba_ditahan, 2) }}</b>
                        </td>
                    </tr>
                </table>

                <div style="margin-top: 16px;"></div>
                {!! json_decode(str_replace('{tanggal}', $tanggal_kondisi, $usaha->ttd->tanda_tangan_pelaporan), true) !!}
            </td>
        </tr>
    </table>
@endsection
