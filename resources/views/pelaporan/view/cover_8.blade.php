<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>COVER</title>

    <style>
        @page {
            margin: 30px;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* BLOK KANAN ATAS */
        .right-box {
            position: fixed;
            top: 0;
            right: 0;
            width: 70%;
            text-align: right;
        }

        .right-box h1,
        .right-box h2,
        .right-box h3 {
            margin: 6px 0;
            white-space: nowrap;
        }

        .logo {
            margin-top: 20px;
        }

        /* ===== GRAFIK BAWAH (DOMPDF SAFE) ===== */
        .graphic-wrap {
            position: fixed;
            bottom: 30px;
            left: 50px;
            right: 50px;
            height: 420px;
            text-align: right;
        }


        .group {
            display: inline-block;
            vertical-align: bottom;
            margin-left: 30px;
        }


        .bar-wrap {
            display: inline-block;
            width: 20px;
            margin-right: 0px;
            vertical-align: bottom;
            text-align: center;
        }

        .bar-value {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 6px;
            /* ⬅️ SPASI antara angka & warna */
            line-height: 1;
        }

        /* bar tetap aman DOMPDF */
        .bar {
            width: 20px;
            display: block;
        }

        .yellow {
            background: #fbff00;
        }

        .green {
            background: #7cb342;
        }

        .blue {
            background: #42a5f5;
        }

        .red {
            background: #e53935;
        }

        .orange {
            background: #f57c00;
        }

        .graphic-base {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 26px;
            line-height: 26px;
            background: #bdbdbd;
            font-size: 11px;
            font-weight: bold;
            text-align: right;
            letter-spacing: 1px;
        }

        .bars-area {
            position: absolute;
            bottom: 80px;
            right: 0;
            /* ⬅️ dari 5px ke 0 */
        }
    </style>
</head>

<body>

    <!-- KANAN ATAS -->
    <div class="right-box">
        <h3>RAPAT ANGGOTA TAHUNAN (RAT)</h3>
        <h3>TUTUP BUKU TAHUN {{ $tahun - 1 }}</h3>
        <h3>DAN RAPAT ANGGOTA PERENCANAAN</h3>
        <h3>TAHUN {{ $tahun }}</h3>

        <br>

        <h2>KOPERASI SIMPAN PINJAM</h2>

        <h2>
            “{{ strtoupper(preg_replace('/<br\s*\/?>/i', ' ', $nama_usaha)) }}”
        </h2>

        <div class="logo">
            <img src="{{ public_path('storage/logo/' . $logo) }}" width="160">
        </div>
    </div>

    <!-- GRAFIK HIASAN BAWAH -->
    <div class="graphic-wrap">

        <div class="bars-area">

            <!-- GROUP 1 -->
            <div class="group">

                <div class="bar-wrap">
                    <div class="bar-value">60</div>
                    <div class="bar orange" style="height:100px"></div>
                </div>

                <div class="bar-wrap">
                    <div class="bar-value">80</div>
                    <div class="bar green" style="height:130px"></div>
                </div>

                <div class="bar-wrap">
                    <div class="bar-value">100</div>
                    <div class="bar blue" style="height:160px"></div>
                </div>

                <div class="bar-wrap">
                    <div class="bar-value">90</div>
                    <div class="bar yellow" style="height:190px"></div>
                </div>

                <div class="bar-wrap">
                    <div class="bar-value">130</div>
                    <div class="bar red" style="height:220px"></div>
                </div>

            </div>

            <!-- GROUP 2 -->
            <div class="group">

                <div class="bar-wrap">
                    <div class="bar-value">55</div>
                    <div class="bar orange" style="height:70px"></div>
                </div>

                <div class="bar-wrap">
                    <div class="bar-value">75</div>
                    <div class="bar green" style="height:90px"></div>
                </div>

                <div class="bar-wrap">
                    <div class="bar-value">95</div>
                    <div class="bar blue" style="height:120px"></div>
                </div>

                <div class="bar-wrap">
                    <div class="bar-value">85</div>
                    <div class="bar yellow" style="height:150px"></div>
                </div>

                <div class="bar-wrap">
                    <div class="bar-value">120</div>
                    <div class="bar red" style="height:180px"></div>
                </div>

            </div>

            <!-- GROUP 3 -->
            <div class="group">

                <div class="bar-wrap">
                    <div class="bar-value">70</div>
                    <div class="bar orange" style="height:200px"></div>
                </div>

                <div class="bar-wrap">
                    <div class="bar-value">95</div>
                    <div class="bar green" style="height:230px"></div>
                </div>

                <div class="bar-wrap">
                    <div class="bar-value">120</div>
                    <div class="bar blue" style="height:260px"></div>
                </div>

                <div class="bar-wrap">
                    <div class="bar-value">100</div>
                    <div class="bar yellow" style="height:290px"></div>
                </div>

                <div class="bar-wrap">
                    <div class="bar-value">200</div>
                    <div class="bar red" style="height:320px"></div>
                </div>

            </div>

            <!-- GROUP 4 -->
            <div class="group">

                <div class="bar-wrap">
                    <div class="bar-value">65</div>
                    <div class="bar orange" style="height:180px"></div>
                </div>

                <div class="bar-wrap">
                    <div class="bar-value">85</div>
                    <div class="bar green" style="height:150px"></div>
                </div>

                <div class="bar-wrap">
                    <div class="bar-value">110</div>
                    <div class="bar blue" style="height:210px"></div>
                </div>

                <div class="bar-wrap">
                    <div class="bar-value">95</div>
                    <div class="bar yellow" style="height:170px"></div>
                </div>

                <div class="bar-wrap">
                    <div class="bar-value">135</div>
                    <div class="bar red" style="height:240px"></div>
                </div>

            </div>

        </div>
        <div class="graphic-bebe">
            &nbsp;
        </div>
        <div class="graphic-base">
            {{ strtoupper($nama_kecamatan) }},
            {{ Tanggal::namaBulan($tgl) }} {{ $tahun }}
        </div>

    </div>



</body>

</html>
