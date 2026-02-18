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

        /* ================= BLOK KANAN ATAS ================= */
        .right-box {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            text-align: right;
        }

        .right-box h1,
        .right-box h2,
        .right-box h3 {
            margin: 6px 0;
            white-space: nowrap;
        }

        .logo {
            margin-top: -10px;
            /* naikkan lagi supaya lebih dekat */
            text-align: right;
            padding-right: 60px;
            /* geser lebih ke kiri */
        }

        .logo img {
            display: inline-block;
        }


        /* ================= GRAFIK BAWAH ================= */
        .graphic-wrap {
            position: fixed;
            bottom: 30px;
            left: 0;
            right: 0;
            height: 520px;
            text-align: right;
        }

        /* ================= AREA BAR ================= */
        .bars-area {
            position: absolute;
            bottom: 105px;
            right: 0;
            white-space: nowrap;
            font-size: 0;
        }

        /* GROUP */
        .group {
            display: inline-block;
            vertical-align: bottom;
            margin-left: 51px;
        }

        /* ANGKA DI ATAS BATANG */
        .bar-value {
            position: absolute;
            bottom: 100%;
            /* tepat di atas batang */
            left: 50%;
            transform: translateX(-50%);
            font-size: 12px;
            font-weight: bold;
            color: #000;
            line-height: 1;
            margin-bottom: 4px;
            /* jarak kecil dari ujung batang */
            white-space: nowrap;
        }

        /* WRAP BATANG */
        .bar-wrap {
            position: relative;
            display: inline-block;
            vertical-align: bottom;
            width: 23px;
            height: 260px;
            /* ⬅️ tinggi tetap supaya semua sejajar */
            margin-right: 6px;
        }

        /* BATANG */
        .bar {
            position: absolute;
            bottom: 0;
            /* ⬅️ ini bikin semua nempel garis bawah */
            left: 0;
            width: 29px;
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

        /* ================= FOOTER GRAFIK ================= */
        .graphic-base {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 29px;
            line-height: 23px;
            background: #919191;
            font-size: 16px;
            font-weight: bold;
            text-align: right;
            padding-right: 10px;
            color: #ffffff;
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
            “{{ strtoupper(str_ireplace('PROVINSI JAWA TENGAH', '', preg_replace('/<br\s*\/?>/i', ' ', $nama_usaha))) }}”
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
                    <div class="bar orange" style="height:90px">
                        <div class="bar-value">212</div>
                    </div>
                </div>

                <div class="bar-wrap">
                    <div class="bar green" style="height:120px">
                        <div class="bar-value">215</div>
                    </div>
                </div>

                <div class="bar-wrap">
                    <div class="bar blue" style="height:150px">
                        <div class="bar-value">218</div>
                    </div>
                </div>

                <div class="bar-wrap">
                    <div class="bar yellow" style="height:180px">
                        <div class="bar-value">222</div>
                    </div>
                </div>

                <div class="bar-wrap">
                    <div class="bar red" style="height:210px">
                        <div class="bar-value">226</div>
                    </div>
                </div>
            </div>

            <!-- GROUP 2 -->
            <div class="group">
                <div class="bar-wrap">
                    <div class="bar orange" style="height:50px">
                        <div class="bar-value">210</div>
                    </div>
                </div>

                <div class="bar-wrap">
                    <div class="bar green" style="height:80px">
                        <div class="bar-value">211</div>
                    </div>
                </div>

                <div class="bar-wrap">
                    <div class="bar blue" style="height:100px">
                        <div class="bar-value">213</div>
                    </div>
                </div>

                <div class="bar-wrap">
                    <div class="bar yellow" style="height:125px">
                        <div class="bar-value">216</div>
                    </div>
                </div>

                <div class="bar-wrap">
                    <div class="bar red" style="height:150px">
                        <div class="bar-value">219</div>
                    </div>
                </div>
            </div>

            <!-- GROUP 3 -->
            <div class="group">
                <div class="bar-wrap">
                    <div class="bar orange" style="height:160px">
                        <div class="bar-value">220</div>
                    </div>
                </div>

                <div class="bar-wrap">
                    <div class="bar green" style="height:190px">
                        <div class="bar-value">224</div>
                    </div>
                </div>

                <div class="bar-wrap">
                    <div class="bar blue" style="height:220px">
                        <div class="bar-value">227</div>
                    </div>
                </div>

                <div class="bar-wrap">
                    <div class="bar yellow" style="height:240px">
                        <div class="bar-value">228</div>
                    </div>
                </div>

                <div class="bar-wrap">
                    <div class="bar red" style="height:270px">
                        <div class="bar-value">229</div>
                    </div>
                </div>
            </div>

            <!-- GROUP 4 -->
            <div class="group">
                <div class="bar-wrap">
                    <div class="bar orange" style="height:125px">
                        <div class="bar-value">217</div>
                    </div>
                </div>

                <div class="bar-wrap">
                    <div class="bar green" style="height:100px">
                        <div class="bar-value">214</div>
                    </div>
                </div>

                <div class="bar-wrap">
                    <div class="bar blue" style="height:180px">
                        <div class="bar-value">223</div>
                    </div>
                </div>

                <div class="bar-wrap">
                    <div class="bar yellow" style="height:160px">
                        <div class="bar-value">221</div>
                    </div>
                </div>

                <div class="bar-wrap">
                    <div class="bar red" style="height:205px">
                        <div class="bar-value">225</div>
                    </div>
                </div>
            </div>

        </div>

        <div class="graphic-base">
            {{ strtoupper($nama_kecamatan) }},
            {{ strtoupper(Tanggal::namaBulan($tgl)) }} {{ $tahun }}
        </div>
    </div>
</body>

</html>
