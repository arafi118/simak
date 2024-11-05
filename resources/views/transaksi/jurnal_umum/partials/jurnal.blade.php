@php
    use App\Utils\Tanggal;
    $total_saldo = 0;

    if ($rek->jenis_mutasi == 'debet') {
        $saldo_awal_tahun = $saldo['debit'] - $saldo['kredit'];
        $saldo_awal_bulan = $d_bulan_lalu - $k_bulan_lalu;
        $total_saldo = $saldo_awal_tahun + $saldo_awal_bulan;
    } else {
        $saldo_awal_tahun = $saldo['kredit'] - $saldo['debit'];
        $saldo_awal_bulan = $k_bulan_lalu - $d_bulan_lalu;
        $total_saldo = $saldo_awal_tahun + $saldo_awal_bulan;
    }

    $total_debit = 0;
    $total_kredit = 0;
@endphp

<table border="0" width="100%" cellspacing="0" cellpadding="0" class="table table-striped midle">
    <thead class="bg-dark text-white">
        <tr>
            <th height="40" align="center">No</th>
            <th align="center">Tanggal</th>
            <th align="center">Kode Akun</th>
            <th align="center">Keterangan</th>
            <th align="center">Kode Trx.</th>
            <th align="center">Debit</th>
            <th align="center">Kredit</th>
            <th align="center">Saldo</th>
            <th align="center">Ins</th>
            <th align="center">&nbsp;</th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td align="center"></td>
            <td align="center">{{ Tanggal::tglIndo($tahun . '-01-01') }}</td>
            <td align="center"></td>
            <td>Komulatif Transaksi Awal Tahun {{ $tahun }}</td>
            <td>&nbsp;</td>
            <td align="right">{{ number_format($saldo['debit'], 2) }}</td>
            <td align="right">{{ number_format($saldo['kredit'], 2) }}</td>
            <td align="right">{{ number_format($saldo_awal_tahun, 2) }}</td>
            <td align="center"></td>
            <td align="center"></td>
        </tr>
        <tr>
            <td align="center"></td>
            <td align="center">{{ Tanggal::tglIndo($tahun . '-' . $bulan . '-01') }}</td>
            <td align="center"></td>
            <td>Komulatif Transaksi s/d Bulan Lalu</td>
            <td>&nbsp;</td>
            <td align="right">{{ number_format($d_bulan_lalu, 2) }}</td>
            <td align="right">{{ number_format($k_bulan_lalu, 2) }}</td>
            <td align="right">{{ number_format($total_saldo, 2) }}</td>
            <td align="center"></td>
            <td align="center"></td>
        </tr>

        @foreach ($transaksi as $trx)
            @php
                if ($trx->rekening_debit == $rek->kode_akun) {
                    $ref = $trx->rekening_kredit;
                    $debit = $trx->jumlah;
                    $kredit = 0;
                } else {
                    $ref = $trx->rekening_debit;
                    $debit = 0;
                    $kredit = $trx->jumlah;
                }

                if ($rek->jenis_mutasi == 'debet') {
                    $_saldo = $debit - $kredit;
                } else {
                    $_saldo = $kredit - $debit;
                }

                $total_saldo += $_saldo;
                $total_debit += $debit;
                $total_kredit += $kredit;

                $kuitansi = false;
                $files = 'bm';
                if (
                    $keuangan->startWith($trx->rekening_debit, '1.1.01') &&
                    !$keuangan->startWith($trx->rekening_kredit, '1.1.01')
                ) {
                    $files = 'bkm';
                    $kuitansi = true;
                }
                if (
                    !$keuangan->startWith($trx->rekening_debit, '1.1.01') &&
                    $keuangan->startWith($trx->rekening_kredit, '1.1.01')
                ) {
                    $files = 'bkk';
                    $kuitansi = true;
                }
                if (
                    $keuangan->startWith($trx->rekening_debit, '1.1.01') &&
                    $keuangan->startWith($trx->rekening_kredit, '1.1.01')
                ) {
                    $files = 'bm';
                    $kuitansi = false;
                }
                if (
                    $keuangan->startWith($trx->rekening_debit, '1.1.02') &&
                    !(
                        $keuangan->startWith($trx->rekening_kredit, '1.1.01') ||
                        $keuangan->startWith($trx->rekening_kredit, '1.1.02')
                    )
                ) {
                    $files = 'bkm';
                    $kuitansi = true;
                }
                if (
                    $keuangan->startWith($trx->rekening_debit, '1.1.02') &&
                    $keuangan->startWith($trx->rekening_kredit, '1.1.02')
                ) {
                    $files = 'bm';
                    $kuitansi = false;
                }
                if (
                    $keuangan->startWith($trx->rekening_debit, '1.1.02') &&
                    $keuangan->startWith($trx->rekening_kredit, '1.1.01')
                ) {
                    $files = 'bm';
                    $kuitansi = false;
                }
                if (
                    $keuangan->startWith($trx->rekening_debit, '1.1.01') &&
                    $keuangan->startWith($trx->rekening_kredit, '1.1.02')
                ) {
                    $files = 'bm';
                    $kuitansi = false;
                }
                if (
                    $keuangan->startWith($trx->rekening_debit, '5.') &&
                    !(
                        $keuangan->startWith($trx->rekening_kredit, '1.1.01') ||
                        $keuangan->startWith($trx->rekening_kredit, '1.1.02')
                    )
                ) {
                    $files = 'bm';
                    $kuitansi = false;
                }
                if (
                    !(
                        $keuangan->startWith($trx->rekening_debit, '1.1.01') ||
                        $keuangan->startWith($trx->rekening_debit, '1.1.02')
                    ) &&
                    $keuangan->startWith($trx->rekening_kredit, '1.1.02')
                ) {
                    $files = 'bm';
                    $kuitansi = false;
                }
                if (
                    !(
                        $keuangan->startWith($trx->rekening_debit, '1.1.01') ||
                        $keuangan->startWith($trx->rekening_debit, '1.1.02')
                    ) &&
                    $keuangan->startWith($trx->rekening_kredit, '4.')
                ) {
                    $files = 'bm';
                    $kuitansi = false;
                }

                $ins = '';
                if (isset($trx->user->ins)) {
                    $ins = $trx->user->ins;
                }
            @endphp


            <tr>
                <td align="center">{{ $loop->iteration }}.</td>
                <td align="center">{{ Tanggal::tglIndo($trx->tgl_transaksi) }}</td>
                <td align="center">{{ $ref }}</td>
                <td>{{ $trx->keterangan_transaksi }}</td>
                <td align="center">{{ $trx->idt }}</td>
                <td align="right">{{ number_format($debit, 2) }}</td>
                <td align="right">{{ number_format($kredit, 2) }}</td>
                <td align="right">{{ number_format($total_saldo, 2) }}</td>
                <td align="center">{{ $ins }}</td>
                <td align="center">
                    <div class="dropdown dropleft">
                        <button class="btn btn-info btn-sm dropdown-toggle" type="button" id="{{ $trx->id }}"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-info"></i>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="{{ $trx->id }}">
                            @if ($kuitansi && in_array('jurnal_umum.cetak_kuitansi', Session::get('tombol')))
                                <a class="dropdown-item" target="_blank"
                                    href="/transaksi/dokumen/kuitansi/{{ $trx->idt }}">
                                    Kuitansi
                                </a>
                                <a class="dropdown-item" target="_blank"
                                    href="/transaksi/dokumen/kuitansi_thermal/{{ $trx->idt }}">
                                    Kuitansi Thermal
                                </a>
                            @endif

                            @if (in_array('jurnal_umum.cetak_voucher', Session::get('tombol')))
                                <a class="dropdown-item btn-link" target="_blank"
                                    data-action="/transaksi/dokumen/{{ $files }}/{{ $trx->idt }}"
                                    href="#">
                                    @if ($files == 'bkm')
                                        Bukti Kas Masuk
                                    @elseif ($files == 'bkk')
                                        Bukti Kas Keluar
                                    @else
                                        Bukti Memorial
                                    @endif
                                </a>
                            @endif

                            @if (in_array('jurnal_umum.transaksi_reversal', Session::get('tombol')) ||
                                    in_array('jurnal_umum.edit_transaksi', Session::get('tombol')) ||
                                    in_array('jurnal_umum.hapus_transaksi', Session::get('tombol')))
                                <div class="dropdown-divider"></div>
                                @if (in_array('jurnal_umum.transaksi_reversal', Session::get('tombol')))
                                    <a class="dropdown-item btn-reversal" data-idt="{{ $trx->idt }}"
                                        href="#">
                                        Reversal
                                    </a>
                                @endif

                                @if (in_array('jurnal_umum.edit_transaksi', Session::get('tombol')))
                                    <a class="dropdown-item text-warning btn-edit" data-idt="{{ $trx->idt }}"
                                        href="#">
                                        Edit Transaksi
                                    </a>
                                @endif

                                @if (in_array('jurnal_umum.hapus_transaksi', Session::get('tombol')))
                                    <a class="dropdown-item text-danger btn-delete" data-idt="{{ $trx->idt }}"
                                        href="#">
                                        Hapus Transaksi
                                    </a>
                                @endif
                            @endif
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach

        <tr>
            <td colspan="5">
                <b>Total Transaksi {{ ucwords($sub_judul) }}</b>
            </td>
            <td align="right">
                <b>{{ number_format($total_debit, 2) }}</b>
            </td>
            <td align="right">
                <b>{{ number_format($total_kredit, 2) }}</b>
            </td>
            <td colspan="3" rowspan="3" align="center" style="vertical-align: middle">
                <b>{{ number_format($total_saldo, 2) }}</b>
            </td>
        </tr>

        <tr>
            <td colspan="5">
                <b>Total Transaksi sampai dengan {{ ucwords($sub_judul) }}</b>
            </td>
            <td align="right">
                <b>{{ number_format($d_bulan_lalu + $total_debit, 2) }}</b>
            </td>
            <td align="right">
                <b>{{ number_format($k_bulan_lalu + $total_kredit, 2) }}</b>
            </td>
        </tr>

        <tr>
            <td colspan="5">
                <b>Total Transaksi Komulatif sampai dengan Tahun {{ $tahun }}</b>
            </td>
            <td align="right">
                <b>{{ number_format($saldo['debit'] + $d_bulan_lalu + $total_debit, 2) }}</b>
            </td>
            <td align="right">
                <b>{{ number_format($saldo['kredit'] + $k_bulan_lalu + $total_kredit, 2) }}</b>
            </td>
        </tr>
    </tbody>

</table>

<script>
    $(document).ready(function() {
        initializeBootstrapTooltip()
    })
</script>
