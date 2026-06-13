@extends('app.layout.main')
@section('src_css')
<style>
        .calendar th, .calendar td {
            width: 14.28%;
            height: 100px;
            vertical-align: top;
            position: relative;
            text-align: center;
        }
        .tanggal {
            position: absolute;
            top: 4px;
            left: 6px;
            font-size: 11px;
            font-weight: bold;
            color: #333;
        }
        .ds  { background: #c8f7c5; }
        .dl  { background: #ffeaa7; }
        .off { background: #dfe6e9; }
        .a   { background: #fab1a0; }
        .kode {
            margin-top: 30px;
            font-size: 18px;
            font-weight: bold;
        }
        .legend span {
            margin-right: 10px;
            padding: 5px 10px;
            border-radius: 4px;
        }
    </style>
@endsection()

@section('content')
    <div class="container mt-4">
        <h4 class="text-center mb-3">Absensi - Januari 2026</h4>

        <table class="table table-bordered calendar">
            <thead class="thead-dark text-center">
                <tr>
                    <th>Min</th>
                    <th>Sen</th>
                    <th>Sel</th>
                    <th>Rab</th>
                    <th>Kam</th>
                    <th>Jum</th>
                    <th>Sab</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td></td>
                    <td class="ds"><span class="tanggal">1</span>
                        <div class="kode">DS</div>
                    </td>
                    <td class="ds"><span class="tanggal">2</span>
                        <div class="kode">DS</div>
                    </td>
                    <td class="dl"><span class="tanggal">3</span>
                        <div class="kode">DL</div>
                    </td>
                    <td class="ds"><span class="tanggal">4</span>
                        <div class="kode">DS</div>
                    </td>
                    <td class="a"><span class="tanggal">5</span>
                        <div class="kode">A</div>
                    </td>
                    <td class="off"><span class="tanggal">6</span>
                        <div class="kode">OFF</div>
                    </td>
                </tr>
                <tr>
                    <td class="ds"><span class="tanggal">7</span>
                        <div class="kode">DS</div>
                    </td>
                    <td class="ds"><span class="tanggal">8</span>
                        <div class="kode">DS</div>
                    </td>
                    <td class="ds"><span class="tanggal">9</span>
                        <div class="kode">DS</div>
                    </td>
                    <td class="dl"><span class="tanggal">10</span>
                        <div class="kode">DL</div>
                    </td>
                    <td class="ds"><span class="tanggal">11</span>
                        <div class="kode">DS</div>
                    </td>
                    <td class="ds"><span class="tanggal">12</span>
                        <div class="kode">DS</div>
                    </td>
                    <td class="off"><span class="tanggal">13</span>
                        <div class="kode">OFF</div>
                    </td>
                </tr>
                <!-- Tambahkan baris berikutnya sesuai tanggal -->
            </tbody>
        </table>

        <div class="legend mt-3">
            <span class="ds">DS</span> = Dinas Siang
            <span class="dl">DL</span> = Dinas Libur
            <span class="off">OFF</span> = Libur
            <span class="a">A</span> = Alpha
        </div>
    </div>
@endsection()
