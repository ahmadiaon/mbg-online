@extends('app.layout.main')

@section('content')
    <style>
        .wl-container {
            max-width: 1400px;
            margin: auto;
            padding: 20px
        }

        .wl-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px
        }

        .wl-header h1 {
            margin: 0;
            font-size: 25px;
            font-weight: 700;
            color: #111827
        }

        .wl-header p {
            margin: 4px 0 0;
            color: #6b7280;
            font-size: 13px
        }

        .wl-btn {
            border: 0;
            border-radius: 8px;
            padding: 9px 15px;
            cursor: pointer;
            font-weight: 600
        }

        .wl-btn-primary {
            background: #2563eb;
            color: #fff
        }

        .wl-btn-primary:hover {
            background: #1d4ed8
        }

        .wl-btn-secondary {
            background: #e5e7eb;
            color: #374151
        }

        .wl-filter-group {
            display: flex;
            gap: 7px;
            align-items: center
        }

        .wl-filter {
            padding: 7px 10px;
            border: 1px solid #d1d5db;
            border-radius: 7px;
            background: #fff;
            color: #374151;
            font-size: 11px;
            outline: 0
        }

        .wl-location-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 18px
        }

        .wl-location-grid.single {
            grid-template-columns: 1fr
        }

        .wl-location-card {
            background: #fff;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 3px 15px rgba(0, 0, 0, .06);
            border: 1px solid #f0f0f0
        }

        .wl-location-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 13px 16px;
            border-bottom: 1px solid #f1f5f9
        }

        .wl-location-name {
            font-size: 16px;
            font-weight: 700;
            color: #111827
        }

        .wl-location-label {
            font-size: 11px;
            color: #9ca3af
        }

        .wl-panorama {
            height: 210px;
            background: #f3f4f6;
            position: relative;
            cursor: pointer;
            overflow: hidden
        }

        .wl-panorama img {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
            transition: .25s
        }

        .wl-panorama:hover img {
            transform: scale(1.025)
        }

        .wl-panorama-overlay {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            padding: 35px 14px 10px;
            background: linear-gradient(transparent, rgba(0, 0, 0, .72));
            color: #fff;
            font-size: 11px
        }

        .wl-no-image {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 7px;
            color: #9ca3af;
            font-size: 12px
        }

        .wl-no-image i {
            font-size: 28px
        }

        .wl-location-info {
            padding: 13px 16px
        }

        .wl-value-row {
            display: flex;
            justify-content: space-between;
            align-items: center
        }

        .wl-water-value {
            font-size: 30px;
            font-weight: 700;
            color: #111827;
            line-height: 1
        }

        .wl-water-value span {
            font-size: 13px;
            font-weight: 500;
            color: #6b7280
        }

        .wl-change {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 700
        }

        .wl-change-up,
        .wl-change-down {
            color: #2563eb
        }

        .wl-change-neutral {
            color: #6b7280
        }

        .wl-change-icon {
            font-size: 22px;
            line-height: 1
        }

        .wl-location-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 9px;
            color: #9ca3af;
            font-size: 11px
        }

        .wl-chart-card {
            background: #fff;
            border-radius: 14px;
            padding: 18px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, .06);
            border: 1px solid #f0f0f0
        }

        .wl-section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px
        }

        .wl-section-title {
            margin: 0;
            font-size: 17px;
            font-weight: 700;
            color: #111827
        }

        .wl-section-subtitle {
            font-size: 11px;
            color: #9ca3af;
            margin-top: 3px
        }

        .wl-chart-container {
            height: 350px;
            position: relative
        }

        .wl-table-card {
            background: #fff;
            margin-top: 18px;
            border-radius: 14px;
            padding: 18px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, .06);
            border: 1px solid #f0f0f0
        }

        .wl-table-wrapper {
            overflow-x: auto
        }

        .wl-table {
            width: 100%;
            border-collapse: collapse
        }

        .wl-table th {
            padding: 10px;
            text-align: left;
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap
        }

        .wl-table td {
            padding: 10px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 12px;
            color: #374151;
            white-space: nowrap;
            vertical-align: middle
        }

        .wl-table tbody tr:hover {
            background: #f8fafc
        }

        .wl-badge {
            display: inline-flex;
            padding: 4px 8px;
            border-radius: 15px;
            font-size: 10px;
            font-weight: 700
        }

        .wl-badge-sri {
            background: #dbeafe;
            color: #1d4ed8
        }

        .wl-badge-mb {
            background: #dcfce7;
            color: #15803d
        }

        .wl-photo-thumb {
            width: 65px;
            height: 43px;
            border-radius: 5px;
            object-fit: cover;
            cursor: pointer;
            transition: .2s
        }

        .wl-photo-thumb:hover {
            transform: scale(1.05)
        }

        .wl-no-photo {
            font-size: 10px;
            color: #9ca3af
        }

        .wl-status-high {
            color: #dc2626;
            font-weight: 600
        }

        .wl-status-normal {
            color: #2563eb;
            font-weight: 600
        }

        .wl-status-low {
            color: #16a34a;
            font-weight: 600
        }

        .wl-empty {
            text-align: center !important;
            padding: 35px !important;
            color: #9ca3af !important
        }

        .wl-loading {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9ca3af;
            font-size: 12px
        }

        .wl-error {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #dc2626;
            font-size: 12px
        }

        .wl-modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .72);
            align-items: center;
            justify-content: center;
            padding: 20px;
            z-index: 99999
        }

        .wl-modal.active {
            display: flex
        }

        .wl-modal-box {
            background: #fff;
            border-radius: 14px;
            width: 100%;
            max-width: 900px;
            max-height: 90vh;
            overflow: auto;
            padding: 16px
        }

        .wl-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 13px
        }

        .wl-modal-header h3 {
            font-size: 16px;
            margin: 0;
            color: #111827
        }

        .wl-close {
            border: 0;
            background: #f3f4f6;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 18px;
            color: #374151
        }

        .wl-modal-image {
            width: 100%;
            max-height: 75vh;
            object-fit: contain;
            border-radius: 7px
        }

        .wl-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 13px
        }

        .wl-form-group label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 4px
        }

        .wl-form-control {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #d1d5db;
            border-radius: 7px;
            font-size: 12px;
            outline: 0
        }

        .wl-form-control:focus {
            border-color: #2563eb
        }

        .wl-modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 7px;
            margin-top: 18px
        }

        .wl-share-modal-box {
            max-width: 1000px
        }

        .wl-share-preview-container {
            width: 100%;
            max-height: 70vh;
            overflow: auto;
            background: #f3f4f6;
            padding: 12px;
            border-radius: 10px;
            text-align: center
        }

        .wl-share-preview-image {
            display: block;
            max-width: 100%;
            height: auto;
            margin: auto;
            border-radius: 7px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, .12)
        }

        @media(max-width:800px) {
            .wl-location-grid {
                grid-template-columns: 1fr
            }

            .wl-chart-container {
                height: 300px
            }
        }

        @media(max-width:600px) {
            .wl-container {
                padding: 12px
            }

            .wl-header {
                gap: 10px;
                flex-wrap: wrap
            }

            .wl-header h1 {
                font-size: 21px
            }

            .wl-header p {
                font-size: 11px
            }

            .wl-btn {
                padding: 8px 11px;
                font-size: 11px
            }

            .wl-header .wl-filter-group {
                width: 100%;
                flex-wrap: wrap
            }

            .wl-header .wl-filter {
                flex: 1
            }

            .wl-panorama {
                height: 180px
            }

            .wl-water-value {
                font-size: 26px
            }

            .wl-location-meta {
                font-size: 10px
            }

            .wl-section-header {
                align-items: flex-start;
                flex-direction: column;
                gap: 10px
            }

            .wl-chart-container {
                height: 270px
            }

            .wl-form-grid {
                grid-template-columns: 1fr
            }
        }
    </style>

    <div class="wl-container">
        <div class="wl-header">
            <div>
                <h1>Water Level Monitoring</h1>
                <p>Monitoring ketinggian air PT. SRI & PT. MB</p>
            </div>
            <div class="wl-filter-group">
                <select id="dashboardLocation" class="wl-filter">
                    <option value="ALL">Semua Lokasi</option>
                    <option value="PT. MB">PT. MB</option>
                    <option value="PT. SRI">PT. SRI</option>
                </select>
                <button type="button" class="wl-btn wl-btn-primary" id="btnShareWaterLevel">
                    <i class="bi bi-whatsapp"></i> Share WhatsApp
                </button>
                <button type="button" class="wl-btn wl-btn-primary" id="btnInputWaterLevel">
                    <i class="icon-copy bi bi-file-arrow-down"></i> Input Water Level
                </button>
            </div>
        </div>

        <div class="wl-location-grid">
            <div class="wl-location-card" id="cardMb">
                <div class="wl-location-head">
                    <div class="wl-location-name">PT. MB</div>
                    <div class="wl-location-label">Pengukuran terbaru</div>
                </div>
                <div id="panoramaMb" class="wl-panorama">
                    <div class="wl-loading">Mengambil data...</div>
                </div>
                <div class="wl-location-info">
                    <div class="wl-value-row">
                        <div class="wl-water-value">
                            <span id="valueMb">-</span> <span>cm</span>
                        </div>
                        <div id="changeMb" class="wl-change wl-change-neutral">
                            → <span>Menunggu data</span>
                        </div>
                    </div>
                    <div class="wl-location-meta">
                        <span id="dateMb">-</span>
                        <span id="timeMb">-</span>
                    </div>
                </div>
            </div>

            <div class="wl-location-card" id="cardSri">
                <div class="wl-location-head">
                    <div class="wl-location-name">PT. SRI</div>
                    <div class="wl-location-label">Pengukuran terbaru</div>
                </div>
                <div id="panoramaSri" class="wl-panorama">
                    <div class="wl-loading">Mengambil data...</div>
                </div>
                <div class="wl-location-info">
                    <div class="wl-value-row">
                        <div class="wl-water-value">
                            <span id="valueSri">-</span> <span>cm</span>
                        </div>
                        <div id="changeSri" class="wl-change wl-change-neutral">
                            → <span>Menunggu data</span>
                        </div>
                    </div>
                    <div class="wl-location-meta">
                        <span id="dateSri">-</span>
                        <span id="timeSri">-</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="wl-chart-card">
            <div class="wl-section-header">
                <div>
                    <h2 class="wl-section-title">Grafik Ketinggian Air</h2>
                    <div class="wl-section-subtitle" id="chartSubtitle">
                        Perkembangan pengukuran PT. MB dan PT. SRI • 7 hari terakhir
                    </div>
                </div>
            </div>
            <div class="wl-chart-container">
                <canvas id="waterChart"></canvas>
            </div>
        </div>

        <div class="wl-table-card">
            <div class="wl-section-header">
                <div>
                    <h2 class="wl-section-title">Data Pengukuran</h2>
                    <div class="wl-section-subtitle">Riwayat pengukuran water level</div>
                </div>
                <div class="wl-filter-group">
                    <select id="tableLocation" class="wl-filter">
                        <option value="ALL">Semua Lokasi</option>
                        <option value="PT. MB">PT. MB</option>
                        <option value="PT. SRI">PT. SRI</option>
                    </select>
                </div>
            </div>

            <div class="wl-table-wrapper">
                <table class="wl-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Lokasi</th>
                            <th>Tinggi</th>
                            <th>Status</th>
                            <th>Panorama</th>
                            <th>Draft Meter</th>
                        </tr>
                    </thead>
                    <tbody id="waterLevelTableBody">
                        <tr>
                            <td colspan="7" class="wl-empty">Mengambil data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="imageModal" class="wl-modal">
        <div class="wl-modal-box">
            <div class="wl-modal-header">
                <h3 id="modalTitle">Foto</h3>
                <button type="button" class="wl-close" id="btnCloseImage">&times;</button>
            </div>
            <img id="modalImage" class="wl-modal-image" src="" alt="Preview">
        </div>
    </div>

    <div id="inputModal" class="wl-modal">
        <div class="wl-modal-box">
            <div class="wl-modal-header">
                <h3>Input Water Level</h3>
                <button type="button" class="wl-close" id="btnCloseInput">&times;</button>
            </div>

            <form id="waterLevelForm" enctype="multipart/form-data">
                @csrf
                <div class="wl-form-grid">
                    <div class="wl-form-group">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal" id="inputTanggal" class="wl-form-control" required>
                    </div>

                    <div class="wl-form-group">
                        <label>Jam</label>
                        <input type="time" name="jam" id="inputJam" class="wl-form-control" required>
                    </div>

                    <div class="wl-form-group">
                        <label>Lokasi</label>
                        <select name="lokasi" id="inputLokasi" class="wl-form-control" required>
                            <option value="">Pilih Lokasi</option>
                            <option value="PT. MB">PT. MB</option>
                            <option value="PT. SRI">PT. SRI</option>
                        </select>
                    </div>

                    <div class="wl-form-group">
                        <label>Tinggi Air (cm)</label>
                        <input type="number" name="tinggi" id="inputTinggi" class="wl-form-control" min="0"
                            step="0.01" required>
                    </div>

                    <div class="wl-form-group">
                        <label>Foto Panorama Sungai</label>
                        <input type="file" name="foto_panorama" id="inputPanorama" class="wl-form-control"
                            accept="image/jpeg,image/png,image/webp">
                    </div>

                    <div class="wl-form-group">
                        <label>Foto Draft Meter</label>
                        <input type="file" name="foto_draft_meter" id="inputDraftMeter" class="wl-form-control"
                            accept="image/jpeg,image/png,image/webp">
                    </div>
                </div>

                <div class="wl-modal-footer">
                    <button type="button" class="wl-btn wl-btn-secondary" id="btnCancelInput">Batal</button>
                    <button type="submit" class="wl-btn wl-btn-primary" id="btnSaveWaterLevel">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="shareImageModal" class="wl-modal">
        <div class="wl-modal-box wl-share-modal-box">
            <div class="wl-modal-header">
                <h3>Preview Water Level</h3>
                <button type="button" class="wl-close" id="btnCloseShareImage">&times;</button>
            </div>

            <div class="wl-share-preview-container">
                <img id="shareImagePreview" class="wl-share-preview-image" src="" alt="Water Level Report">
            </div>

            <div class="wl-modal-footer">
                <button type="button" class="wl-btn wl-btn-secondary" id="btnCancelShare">
                    Tutup
                </button>
                <button type="button" class="wl-btn wl-btn-primary" id="btnConfirmShare">
                    <i class="bi bi-whatsapp"></i> Share WhatsApp
                </button>
            </div>
        </div>
    </div>
@endsection

@section('js_code')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function() {
            'use strict';

            const API_URL = '/feature/water-level/data';
            const STORE_URL = '/feature/water-level';
            const ASSETS_URL = 'https://assets.mitrabaritogroup.com';

            let waterData = [];
            let waterChart = null;

            window.waterLevelShareData = null;

            function init() {
                console.log('Water Level JS initialized');
                bindEvents();
                loadWaterLevel();
            }

            function loadWaterLevel() {
                fetch(API_URL, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json'
                        },
                        cache: 'no-store'
                    })
                    .then(function(response) {
                        if (!response.ok) throw new Error('HTTP ' + response.status);
                        return response.json();
                    })
                    .then(function(response) {
                        console.log('Water Level API:', response);
                        waterData = Array.isArray(response.data) ? response.data : [];
                        renderDashboard();
                    })
                    .catch(function(error) {
                        console.error('Water Level Error:', error);
                        showError();
                    });
            }

            function renderDashboard() {
                const location = document.getElementById('dashboardLocation')?.value || 'ALL';
                const grid = document.querySelector('.wl-location-grid');
                const mbCard = document.getElementById('cardMb');
                const sriCard = document.getElementById('cardSri');

                if (location === 'ALL') {
                    mbCard.style.display = '';
                    sriCard.style.display = '';
                    grid.classList.remove('single');
                    renderLocation('PT. MB');
                    renderLocation('PT. SRI');
                } else {
                    mbCard.style.display = location === 'PT. MB' ? '' : 'none';
                    sriCard.style.display = location === 'PT. SRI' ? '' : 'none';
                    grid.classList.add('single');
                    renderLocation(location);
                }

                renderChart(location);
                renderTable();
            }

            function sortedData() {
                return [...waterData].sort(function(a, b) {
                    return (b.tanggal + ' ' + b.jam).localeCompare(a.tanggal + ' ' + a.jam);
                });
            }

            function renderLocation(location) {
                const isMb = location === 'PT. MB';
                const prefix = isMb ? 'Mb' : 'Sri';

                const latest = sortedData().find(function(item) {
                    return item.lokasi === location;
                });

                const container = document.getElementById(
                    isMb ? 'panoramaMb' : 'panoramaSri'
                );

                if (!latest) {
                    container.innerHTML =
                        '<div class="wl-no-image"><i class="fas fa-image"></i><span>Belum ada data</span></div>';
                    setText('value' + prefix, '-');
                    setText('date' + prefix, '-');
                    setText('time' + prefix, '-');
                    return;
                }

                const imageUrl = assetUrl(
                    'water_level/panorama',
                    latest.foto_panorama
                );

                const change = getChangeFromYesterday(
                    location,
                    latest
                );

                if (imageUrl) {
                    container.setAttribute('data-photo', imageUrl);
                    container.setAttribute(
                        'data-title',
                        'Panorama Sungai ' + location
                    );

                    container.innerHTML =
                        '<img src="' + escapeAttr(imageUrl) + '" alt="Panorama ' + escapeAttr(location) + '">' +
                        '<div class="wl-panorama-overlay">' +
                        '<i class="fas fa-expand"></i> Lihat Foto' +
                        '</div>';
                } else {
                    container.innerHTML =
                        '<div class="wl-no-image">' +
                        '<i class="fas fa-image"></i>' +
                        '<span>Foto belum tersedia</span>' +
                        '</div>';

                    container.removeAttribute('data-photo');
                }

                setText('value' + prefix, latest.tinggi);
                setText('date' + prefix, formatDate(latest.tanggal));
                setText('time' + prefix, formatTime(latest.jam));

                const changeEl = document.getElementById(
                    'change' + prefix
                );

                changeEl.className =
                    'wl-change ' + change.className;

                changeEl.innerHTML =
                    '<span class="wl-change-icon">' + change.icon + '</span>' +
                    '<span>' + change.text + '</span>';
            }

            function getChangeFromYesterday(location, latest) {
                const yesterday = getYesterday(latest.tanggal);

                const previous = waterData
                    .filter(function(item) {
                        return item.lokasi === location &&
                            item.tanggal === yesterday;
                    })
                    .sort(function(a, b) {
                        return (b.jam || '').localeCompare(a.jam || '');
                    })[0];

                if (!previous) {
                    return {
                        icon: '→',
                        text: 'Belum ada data kemarin',
                        className: 'wl-change-neutral'
                    };
                }

                const difference =
                    Number(latest.tinggi) -
                    Number(previous.tinggi);

                if (difference > 0) {
                    return {
                        icon: '↑',
                        text: '+' + formatNumber(difference) + ' cm dari kemarin',
                        className: 'wl-change-up'
                    };
                }

                if (difference < 0) {
                    return {
                        icon: '↓',
                        text: formatNumber(difference) + ' cm dari kemarin',
                        className: 'wl-change-down'
                    };
                }

                return {
                    icon: '→',
                    text: '0 cm dari kemarin',
                    className: 'wl-change-neutral'
                };
            }

            function renderChart(location) {
                const canvas = document.getElementById('waterChart');

                if (!canvas) return;

                location =
                    location ||
                    document.getElementById('dashboardLocation')?.value ||
                    'ALL';

                const today = new Date();
                const dates = [];

                for (let i = 6; i >= 0; i--) {
                    const date = new Date(today);
                    date.setDate(today.getDate() - i);
                    dates.push(formatDatabaseDate(date));
                }

                const rows = dates.map(function(date) {

                    const dayData = waterData.filter(function(item) {
                        return item.tanggal === date;
                    });

                    const mb = dayData
                        .filter(function(item) {
                            return item.lokasi === 'PT. MB';
                        })
                        .sort(function(a, b) {
                            return (b.jam || '').localeCompare(a.jam || '');
                        })[0];

                    const sri = dayData
                        .filter(function(item) {
                            return item.lokasi === 'PT. SRI';
                        })
                        .sort(function(a, b) {
                            return (b.jam || '').localeCompare(a.jam || '');
                        })[0];

                    return {
                        date: date,
                        mb: mb ? Number(mb.tinggi) : null,
                        sri: sri ? Number(sri.tinggi) : null,
                        mbTime: mb ? mb.jam : null,
                        sriTime: sri ? sri.jam : null
                    };
                });

                const labels = rows.map(function(row) {
                    return formatShortDate(row.date);
                });

                const datasets = [];

                if (location === 'ALL' || location === 'PT. MB') {
                    datasets.push({
                        label: 'PT. MB',
                        data: rows.map(function(row) {
                            return row.mb;
                        }),
                        borderColor: '#16a34a',
                        backgroundColor: 'rgba(22,163,74,.07)',
                        borderWidth: 3,
                        tension: .3,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        spanGaps: true,
                        fill: true
                    });
                }

                if (location === 'ALL' || location === 'PT. SRI') {
                    datasets.push({
                        label: 'PT. SRI',
                        data: rows.map(function(row) {
                            return row.sri;
                        }),
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37,99,235,.07)',
                        borderWidth: 3,
                        tension: .3,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        spanGaps: true,
                        fill: true
                    });
                }

                setText(
                    'chartSubtitle',
                    location === 'ALL' ?
                    'Perkembangan pengukuran PT. MB dan PT. SRI • 7 hari terakhir' :
                    'Perkembangan ' + location + ' • 7 hari terakhir'
                );

                if (waterChart) {
                    waterChart.destroy();
                }

                waterChart = new Chart(canvas, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: datasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        plugins: {
                            legend: {
                                position: 'bottom'
                            },
                            tooltip: {
                                callbacks: {
                                    title: function(items) {
                                        if (!items.length) return '';
                                        return formatDate(
                                            rows[items[0].dataIndex].date
                                        );
                                    },
                                    label: function(context) {
                                        const row =
                                            rows[context.dataIndex];

                                        const time =
                                            context.dataset.label === 'PT. MB' ?
                                            row.mbTime :
                                            row.sriTime;

                                        if (context.raw === null) {
                                            return context.dataset.label +
                                                ': Tidak ada data';
                                        }

                                        return context.dataset.label +
                                            ': ' +
                                            context.raw +
                                            ' cm • ' +
                                            formatTime(time);
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                ticks: {
                                    maxRotation: 0,
                                    autoSkip: false
                                }
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: 'Ketinggian Air (cm)'
                                },
                                beginAtZero: false
                            }
                        }
                    }
                });
            }

            function renderTable() {
                const tbody =
                    document.getElementById(
                        'waterLevelTableBody'
                    );

                if (!tbody) return;

                let data = sortedData();

                const location =
                    document.getElementById(
                        'tableLocation'
                    )?.value || 'ALL';

                if (location !== 'ALL') {
                    data = data.filter(function(item) {
                        return item.lokasi === location;
                    });
                }

                if (!data.length) {
                    tbody.innerHTML =
                        '<tr>' +
                        '<td colspan="7" class="wl-empty">' +
                        'Belum ada data pengukuran' +
                        '</td>' +
                        '</tr>';

                    return;
                }

                tbody.innerHTML = data.map(function(item) {

                    const panorama = assetUrl(
                        'water_level/panorama',
                        item.foto_panorama
                    );

                    const meter = assetUrl(
                        'water_level/draft_meter',
                        item.foto_draft_meter
                    );

                    const status = getStatus(item.tinggi);

                    const badge =
                        item.lokasi === 'PT. MB' ?
                        'wl-badge-mb' :
                        'wl-badge-sri';

                    return (
                        '<tr>' +
                        '<td>' + escapeHtml(item.tanggal) + '</td>' +
                        '<td>' + escapeHtml(formatTime(item.jam)) + '</td>' +
                        '<td><span class="wl-badge ' + badge + '">' +
                        escapeHtml(item.lokasi) +
                        '</span></td>' +
                        '<td><strong>' +
                        escapeHtml(item.tinggi) +
                        '</strong> cm</td>' +
                        '<td><span class="' + status.className + '">' +
                        status.text +
                        '</span></td>' +
                        '<td>' +
                        (
                            panorama ?
                            '<img src="' + escapeAttr(panorama) +
                            '" class="wl-photo-thumb" data-photo="' +
                            escapeAttr(panorama) +
                            '" data-title="Panorama Sungai">' :
                            '<span class="wl-no-photo">Tidak ada</span>'
                        ) +
                        '</td>' +
                        '<td>' +
                        (
                            meter ?
                            '<img src="' + escapeAttr(meter) +
                            '" class="wl-photo-thumb" data-photo="' +
                            escapeAttr(meter) +
                            '" data-title="Draft Meter">' :
                            '<span class="wl-no-photo">Tidak ada</span>'
                        ) +
                        '</td>' +
                        '</tr>'
                    );

                }).join('');
            }

            function getStatus(value) {
                value = Number(value);

                if (value >= 120) {
                    return {
                        text: 'Tinggi',
                        className: 'wl-status-high'
                    };
                }

                if (value >= 100) {
                    return {
                        text: 'Normal',
                        className: 'wl-status-normal'
                    };
                }

                return {
                    text: 'Rendah',
                    className: 'wl-status-low'
                };
            }

            function bindEvents() {

                document
                    .getElementById('dashboardLocation')
                    ?.addEventListener(
                        'change',
                        renderDashboard
                    );

                document
                    .getElementById('tableLocation')
                    ?.addEventListener(
                        'change',
                        renderTable
                    );

                document
                    .getElementById('btnShareWaterLevel')
                    ?.addEventListener(
                        'click',
                        shareWaterLevel
                    );

                document
                    .getElementById('btnConfirmShare')
                    ?.addEventListener(
                        'click',
                        confirmShareWaterLevel
                    );

                document
                    .getElementById('btnCloseShareImage')
                    ?.addEventListener(
                        'click',
                        closeShareImage
                    );

                document
                    .getElementById('btnCancelShare')
                    ?.addEventListener(
                        'click',
                        closeShareImage
                    );

                document
                    .getElementById('shareImageModal')
                    ?.addEventListener(
                        'click',
                        function(event) {
                            if (event.target === this) {
                                closeShareImage();
                            }
                        }
                    );

                document.addEventListener(
                    'click',
                    function(event) {

                        const photo =
                            event.target.closest(
                                '[data-photo]'
                            );

                        if (photo) {
                            openPhoto(
                                photo.dataset.photo,
                                photo.dataset.title
                            );
                        }
                    }
                );

                document
                    .getElementById('btnCloseImage')
                    ?.addEventListener(
                        'click',
                        closePhoto
                    );

                document
                    .getElementById('imageModal')
                    ?.addEventListener(
                        'click',
                        function(event) {
                            if (event.target === this) {
                                closePhoto();
                            }
                        }
                    );

                document
                    .getElementById('btnInputWaterLevel')
                    ?.addEventListener(
                        'click',
                        openInputModal
                    );

                document
                    .getElementById('btnCloseInput')
                    ?.addEventListener(
                        'click',
                        closeInputModal
                    );

                document
                    .getElementById('btnCancelInput')
                    ?.addEventListener(
                        'click',
                        closeInputModal
                    );

                document
                    .getElementById('inputModal')
                    ?.addEventListener(
                        'click',
                        function(event) {
                            if (event.target === this) {
                                closeInputModal();
                            }
                        }
                    );

                document
                    .getElementById('waterLevelForm')
                    ?.addEventListener(
                        'submit',
                        saveWaterLevel
                    );
            }

            function shareWaterLevel() {

                const location =
                    document.getElementById(
                        'dashboardLocation'
                    )?.value || 'ALL';

                if (location === 'ALL') {
                    alert(
                        'Pilih lokasi PT. MB atau PT. SRI terlebih dahulu untuk membagikan laporan.'
                    );
                    return;
                }

                const latest = sortedData().find(function(item) {
                    return item.lokasi === location;
                });

                if (!latest) {
                    alert(
                        'Belum ada data water level untuk ' +
                        location + '.'
                    );
                    return;
                }

                const yesterdayDate =
                    getYesterday(latest.tanggal);

                const yesterdayData =
                    waterData
                    .filter(function(item) {
                        return item.lokasi === location &&
                            item.tanggal === yesterdayDate;
                    })
                    .sort(function(a, b) {
                        return (b.jam || '')
                            .localeCompare(b.jam || '');
                    })[0];

                const todayValue =
                    Number(latest.tinggi);

                const yesterdayValue =
                    yesterdayData ?
                    Number(yesterdayData.tinggi) :
                    null;

                let keterangan =
                    'Tidak ada data kemarin';

                if (yesterdayValue !== null) {

                    const difference =
                        todayValue - yesterdayValue;

                    if (difference > 0) {
                        keterangan =
                            'Naik ' +
                            formatNumber(difference) +
                            ' cm';
                    } else if (difference < 0) {
                        keterangan =
                            'Turun ' +
                            formatNumber(
                                Math.abs(difference)
                            ) +
                            ' cm';
                    } else {
                        keterangan = 'Tetap 0 cm';
                    }
                }

                const text = [
                    'WATER LEVEL MONITORING',
                    '',
                    'Lokasi : ' + location,
                    'Tanggal : ' + formatDate(latest.tanggal),
                    '',
                    'Tinggi Air Hari ini : ' +
                    formatNumber(todayValue) +
                    ' cm',
                    'Tinggi Air Kemarin : ' +
                    (
                        yesterdayValue !== null ?
                        formatNumber(yesterdayValue) + ' cm' :
                        'Tidak ada data'
                    ),
                    'Keterangan : ' + keterangan
                ].join('\n');

                shareWaterLevelImage(
                    location,
                    text,
                    latest
                );
            }

            async function shareWaterLevelImage(
                location,
                text,
                latest
            ) {

                try {

                    /*
                     * Load html2canvas hanya jika belum ada
                     */
                    if (typeof html2canvas === 'undefined') {

                        const script =
                            document.createElement('script');

                        script.src =
                            'https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js';

                        script.crossOrigin = 'anonymous';

                        document.head.appendChild(script);

                        await new Promise(
                            function(resolve, reject) {
                                script.onload = resolve;
                                script.onerror = reject;
                            }
                        );
                    }

                    const card =
                        document.getElementById(
                            location === 'PT. MB' ?
                            'cardMb' :
                            'cardSri'
                        );

                    const chartCanvas =
                        document.getElementById(
                            'waterChart'
                        );

                    if (!card || !chartCanvas) {
                        alert(
                            'Komponen dashboard tidak ditemukan.'
                        );
                        return;
                    }

                    /*
                     * Pastikan foto sudah selesai dimuat
                     */
                    const images =
                        card.querySelectorAll('img');

                    await Promise.all(
                        Array.from(images).map(
                            function(img) {

                                if (img.complete) {
                                    return Promise.resolve();
                                }

                                return new Promise(
                                    function(resolve) {
                                        img.onload = resolve;
                                        img.onerror = resolve;
                                    }
                                );
                            }
                        )
                    );

                    /*
                     * Screenshot card
                     */
                    const cardCanvas =
                        await html2canvas(
                            card, {
                                scale: 2,
                                useCORS: true,
                                allowTaint: false,
                                backgroundColor: '#ffffff',
                                logging: true,
                                imageTimeout: 15000
                            }
                        );

                    /*
                     * Screenshot grafik
                     */
                    const chartImage =
                        chartCanvas.toDataURL(
                            'image/png'
                        );

                    const chartImg =
                        new Image();

                    await new Promise(
                        function(resolve, reject) {

                            chartImg.onload = resolve;
                            chartImg.onerror = reject;

                            chartImg.src = chartImage;
                        }
                    );

                    /*
                     * Buat canvas laporan
                     */
                    const padding = 30;
                    const titleHeight = 60;
                    const gap = 25;

                    const width =
                        Math.max(
                            cardCanvas.width,
                            chartImg.width
                        );

                    const cardScale =
                        width / cardCanvas.width;

                    const cardHeight =
                        Math.round(
                            cardCanvas.height *
                            cardScale
                        );

                    const chartScale =
                        width / chartImg.width;

                    const chartHeight =
                        Math.round(
                            chartImg.height *
                            chartScale
                        );

                    const finalCanvas =
                        document.createElement(
                            'canvas'
                        );

                    finalCanvas.width =
                        width +
                        padding * 2;

                    finalCanvas.height =
                        padding +
                        titleHeight +
                        cardHeight +
                        gap +
                        chartHeight +
                        padding;

                    const ctx =
                        finalCanvas.getContext('2d');

                    /*
                     * Background
                     */
                    ctx.fillStyle = '#ffffff';

                    ctx.fillRect(
                        0,
                        0,
                        finalCanvas.width,
                        finalCanvas.height
                    );

                    /*
                     * Judul
                     */
                    ctx.fillStyle = '#111827';

                    ctx.font =
                        'bold 32px Arial';

                    ctx.fillText(
                        'Water Level Monitoring',
                        padding,
                        padding + 35
                    );

                    /*
                     * Card
                     */
                    ctx.drawImage(
                        cardCanvas,
                        padding,
                        padding + titleHeight,
                        width,
                        cardHeight
                    );

                    /*
                     * Grafik
                     */
                    ctx.drawImage(
                        chartImg,
                        padding,
                        padding +
                        titleHeight +
                        cardHeight +
                        gap,
                        width,
                        chartHeight
                    );

                    /*
                     * Convert ke image
                     */
                    const imageData =
                        finalCanvas.toDataURL(
                            'image/png',
                            1
                        );

                    /*
                     * Tampilkan preview
                     */
                    const previewModal =
                        document.getElementById(
                            'shareImageModal'
                        );

                    const previewImage =
                        document.getElementById(
                            'shareImagePreview'
                        );

                    if (!previewModal || !previewImage) {
                        alert(
                            'Modal preview screenshot belum tersedia.'
                        );
                        return;
                    }

                    previewImage.src = imageData;

                    previewModal.classList.add(
                        'active'
                    );

                    /*
                     * Simpan untuk tombol Share
                     */
                    window.waterLevelShareData = {
                        location: location,
                        text: text,
                        imageData: imageData
                    };

                } catch (error) {

                    console.error(
                        'Water Level Screenshot Error:',
                        error
                    );

                    alert(
                        'Gagal membuat screenshot.\n\n' +
                        error.message
                    );
                }
            }

            async function confirmShareWaterLevel() {

                const data =
                    window.waterLevelShareData;

                if (!data) {
                    alert(
                        'Screenshot belum tersedia.'
                    );
                    return;
                }

                try {

                    const response =
                        await fetch(
                            data.imageData
                        );

                    const blob =
                        await response.blob();

                    const file =
                        new File(
                            [blob],
                            'water-level-' +
                            data.location
                            .replace(
                                /[^a-zA-Z0-9]/g,
                                '-'
                            ) +
                            '.png', {
                                type: 'image/png'
                            }
                        );

                    /*
                     * Share native
                     */
                    if (
                        navigator.share &&
                        navigator.canShare &&
                        navigator.canShare({
                            files: [file]
                        })
                    ) {

                        await navigator.share({
                            title: 'Water Level Monitoring - ' +
                                data.location,
                            text: data.text,
                            files: [file]
                        });

                        return;
                    }

                    /*
                     * Fallback WhatsApp
                     */
                    window.open(
                        'https://wa.me/?text=' +
                        encodeURIComponent(
                            data.text
                        ),
                        '_blank'
                    );

                } catch (error) {

                    if (error.name === 'AbortError') {
                        return;
                    }

                    console.error(
                        'Share WhatsApp Error:',
                        error
                    );

                    window.open(
                        'https://wa.me/?text=' +
                        encodeURIComponent(
                            data.text
                        ),
                        '_blank'
                    );
                }
            }

            function closeShareImage() {

                const modal =
                    document.getElementById(
                        'shareImageModal'
                    );

                if (modal) {
                    modal.classList.remove(
                        'active'
                    );
                }

                const image =
                    document.getElementById(
                        'shareImagePreview'
                    );

                if (image) {
                    image.src = '';
                }

                window.waterLevelShareData = null;
            }

            function openPhoto(url, title) {

                const modal =
                    document.getElementById(
                        'imageModal'
                    );

                const image =
                    document.getElementById(
                        'modalImage'
                    );

                if (!modal || !image) return;

                image.src = url;

                setText(
                    'modalTitle',
                    title || 'Foto'
                );

                modal.classList.add(
                    'active'
                );
            }

            function closePhoto() {

                document
                    .getElementById('imageModal')
                    ?.classList.remove(
                        'active'
                    );

                const image =
                    document.getElementById(
                        'modalImage'
                    );

                if (image) {
                    image.src = '';
                }
            }

            function openInputModal() {

                const modal =
                    document.getElementById(
                        'inputModal'
                    );

                if (!modal) return;

                const now = new Date();

                const tanggal =
                    document.getElementById(
                        'inputTanggal'
                    );

                const jam =
                    document.getElementById(
                        'inputJam'
                    );

                if (tanggal) {
                    tanggal.value =
                        formatDatabaseDate(now);
                }

                if (jam) {
                    jam.value =
                        now.toTimeString()
                        .substring(0, 5);
                }

                modal.classList.add(
                    'active'
                );
            }

            function closeInputModal() {

                document
                    .getElementById('inputModal')
                    ?.classList.remove(
                        'active'
                    );
            }

            function saveWaterLevel(event) {

                event.preventDefault();

                const form =
                    document.getElementById(
                        'waterLevelForm'
                    );

                const button =
                    document.getElementById(
                        'btnSaveWaterLevel'
                    );

                if (!form) return;

                if (button) {
                    button.disabled = true;

                    button.innerHTML =
                        '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
                }

                const csrf =
                    form.querySelector(
                        'input[name="_token"]'
                    );

                fetch(
                        STORE_URL, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrf ? csrf.value : ''
                            },
                            body: new FormData(form)
                        }
                    )
                    .then(
                        function(response) {

                            if (!response.ok) {

                                return response
                                    .json()
                                    .then(
                                        function(data) {
                                            throw data;
                                        }
                                    );
                            }

                            return response.json();
                        }
                    )
                    .then(
                        function(response) {

                            console.log(
                                'Water Level Saved:',
                                response
                            );

                            form.reset();

                            closeInputModal();

                            loadWaterLevel();
                        }
                    )
                    .catch(
                        function(error) {

                            console.error(
                                'Save Water Level Error:',
                                error
                            );

                            alert(
                                error.message ||
                                'Gagal menyimpan data'
                            );
                        }
                    )
                    .finally(
                        function() {

                            if (button) {

                                button.disabled = false;

                                button.innerHTML =
                                    '<i class="fas fa-save mr-1"></i> Simpan';
                            }
                        }
                    );
            }

            function getYesterday(dateString) {

                const date =
                    new Date(
                        dateString +
                        'T00:00:00'
                    );

                date.setDate(
                    date.getDate() - 1
                );

                return formatDatabaseDate(
                    date
                );
            }

            function formatDatabaseDate(date) {

                return date.getFullYear() +
                    '-' +
                    String(
                        date.getMonth() + 1
                    ).padStart(2, '0') +
                    '-' +
                    String(
                        date.getDate()
                    ).padStart(2, '0');
            }

            function formatDate(date) {

                if (!date) return '-';

                const p = date.split('-');

                return p.length === 3 ?
                    p[2] + '/' + p[1] + '/' + p[0] :
                    date;
            }

            function formatShortDate(date) {

                if (!date) return '-';

                const p = date.split('-');

                return p.length === 3 ?
                    p[2] + '/' + p[1] :
                    date;
            }

            function formatTime(time) {

                return time ?
                    time.substring(0, 5) :
                    '-';
            }

            function formatNumber(value) {

                return Number(value)
                    .toLocaleString(
                        'id-ID', {
                            maximumFractionDigits: 2
                        }
                    );
            }

            function assetUrl(folder, filename) {

                if (!filename) return null;

                return ASSETS_URL +
                    '/uploads/' +
                    folder +
                    '/' +
                    encodeURIComponent(
                        filename
                    );
            }

            function setText(id, value) {

                const element =
                    document.getElementById(id);

                if (element) {
                    element.textContent = value;
                }
            }

            function escapeHtml(value) {

                const div =
                    document.createElement(
                        'div'
                    );

                div.textContent =
                    value ?? '';

                return div.innerHTML;
            }

            function escapeAttr(value) {

                return String(value || '')
                    .replace(
                        /&/g,
                        '&amp;'
                    )
                    .replace(
                        /"/g,
                        '&quot;'
                    )
                    .replace(
                        /</g,
                        '&lt;'
                    )
                    .replace(
                        />/g,
                        '&gt;'
                    );
            }

            function showError() {

                const mb =
                    document.getElementById(
                        'panoramaMb'
                    );

                const sri =
                    document.getElementById(
                        'panoramaSri'
                    );

                if (mb) {
                    mb.innerHTML =
                        '<div class="wl-error">' +
                        'Gagal mengambil data' +
                        '</div>';
                }

                if (sri) {
                    sri.innerHTML =
                        '<div class="wl-error">' +
                        'Gagal mengambil data' +
                        '</div>';
                }
            }

            if (
                document.readyState ===
                'loading'
            ) {
                document.addEventListener(
                    'DOMContentLoaded',
                    init
                );
            } else {
                init();
            }

        })();
    </script>
@endsection
