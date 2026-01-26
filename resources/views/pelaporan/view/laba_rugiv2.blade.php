@extends('pelaporan.layout.base')
<title>{{ $title }}</title>
@section('content')
    <style>
        .t {
            border-top: 1px solid #000
        }

        .l {
            border-left: 1px solid #000
        }

        .b {
            border-bottom: 1px solid #000
        }

        .r {
            border-right: 1px solid #000
        }

        .bg {
            background: #e6e6e6;
            font-weight: bold
        }
    </style>
    @php
        function formatKurung($angka)
        {
            if ($angka < 0) {
                return '(' . number_format(abs($angka), 2, ',', '.') . ')';
            }
            return number_format($angka, 2, ',', '.');
        }
    @endphp

    <table width="100%" style="font-size:11px;">
        <tr>
            <td align="center">
                <b style="font-size:18px;">LABA RUGI</b><br>
                <b style="font-size:14px;">{{ strtoupper($sub_judul) }}</b>
            </td>
        </tr>
    </table>

    <br>

    <table width="100%" cellspacing="0" cellpadding="0" style="font-size:11px;">
        @php
            $penjualan = collect($sections['penjualan']);
            $pembelian = collect($sections['pembelian']);

            $total_penjualan = $penjualan->sum('saldo');
            $total_pembelian = $pembelian->sum('saldo');

            // jika belum ada dari controller
            $persediaan_awal = $persediaan_awal ?? 0;
            $persediaan_akhir = $persediaan_akhir ?? 0;

            $total_persediaan = $persediaan_awal + $total_pembelian;
            $hpp = $total_persediaan - $persediaan_akhir;
            $laba_kotor = $total_penjualan - $hpp;

        @endphp

        <tr class="bg">
            <td class="t l b r" colspan="3" align="center"><b>LABA KOTOR</b></td>
        </tr>

        {{-- PENJUALAN --}}
        @foreach ($penjualan as $acc)
            <tr>
                <td class="l b" width="10%">{{ $acc['kode_akun'] }}</td>
                <td class="l b r" width="70%">{{ $acc['nama'] }}</td>
                <td class="b r" width="20%" align="right">
                    {{ formatKurung($acc['saldo'], 2, ',', '.') }}
                </td>
            </tr>
        @endforeach

        <tr>
            <td class="l b"></td>
            <td class="l b r"><b>Penjualan Bersih</b></td>
            <td class="b r" align="right"><b>{{ formatKurung($total_penjualan, 2, ',', '.') }}</b></td>
        </tr>

        <tr>
            <td class="l b"></td>
            <td class="l b r">Persediaan Awal</td>
            <td class="b r" align="right">{{ formatKurung($persediaan_awal, 2, ',', '.') }}</td>
        </tr>

        {{-- PEMBELIAN --}}
        @foreach ($pembelian as $acc)
            <tr>
                <td class="l b">{{ $acc['kode_akun'] }}</td>
                <td class="l b r">{{ $acc['nama'] }}</td>
                <td class="b r" align="right">{{ formatKurung($acc['saldo'], 2, ',', '.') }}</td>
            </tr>
        @endforeach

        <tr>
            <td class="l b"></td>
            <td class="l b r"><b>Total Pembelian</b></td>
            <td class="b r" align="right"><b>{{ formatKurung($total_pembelian, 2, ',', '.') }}</b></td>
        </tr>

        <tr>
            <td class="l b"></td>
            <td class="l b r">Persediaan Akhir</td>
            <td class="b r" align="right">{{ formatKurung($persediaan_akhir, 2, ',', '.') }}</td>
        </tr>

        <tr>
            <td class="l b"></td>
            <td class="l b r"><b>Harga Pokok Penjualan</b></td>
            <td class="b r" align="right"><b>{{ formatKurung($hpp, 2, ',', '.') }}</b></td>
        </tr>

        <tr>
            <td class="l b"></td>
            <td class="l b r"><b>Laba Kotor</b></td>
            <td class="b r" align="right"><b>{{ formatKurung($laba_kotor, 2, ',', '.') }}</b></td>
        </tr>

        {{-- SECTION LAIN --}}
        @foreach ([
            'pendapatan_lain' => 'PENDAPATAN LAIN-LAIN',
            'beban_operasional' => 'BEBAN OPERASIONAL',
            'pendapatan_non_usaha' => 'PENDAPATAN NON USAHA',
            'beban_non_usaha' => 'BEBAN NON USAHA',
            'beban_pajak' => 'BEBAN PAJAK',
        ] as $key => $label)
            @php
                $total = collect($sections[$key])->sum('saldo');
            @endphp

            <tr class="bg">
                <td class="t l b r" colspan="3" align="center"><b>{{ $label }}</b></td>
            </tr>

            @foreach ($sections[$key] as $acc)
                <tr>
                    <td class="l b">{{ $acc['kode_akun'] }}</td>
                    <td class="l b r">{{ $acc['nama'] }}</td>
                    <td class="b r" align="right">{{ formatKurung($acc['saldo'], 2, ',', '.') }}</td>
                </tr>
            @endforeach

            <tr>
                <td class="l b"></td>
                <td class="l b r"><b>Jumlah {{ $label }}</b></td>
                <td class="b r" align="right"><b>{{ formatKurung($total, 2, ',', '.') }}</b></td>
            </tr>
        @endforeach
    </table>
@endsection
