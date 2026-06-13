<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Stok & Barang - Tambang</title>
    <style>
        :root {
            --bg: #0d1b2a;
            --gold: #e0a800;
            --card-bg: rgba(255, 255, 255, 0.05);
            --border: rgba(255, 255, 255, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            display: flex;
            min-height: 100vh;
            background: var(--bg);
            color: #e0e1dd;
            overflow-x: hidden;
        }

        canvas#bgCanvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
        }

        .sidebar {
            width: 260px;
            background: rgba(13, 27, 42, 0.95);
            backdrop-filter: blur(15px);
            border-right: 1px solid var(--border);
            position: fixed;
            height: 100vh;
            z-index: 10;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .sidebar h2 {
            color: var(--gold);
            font-size: 1.3rem;
            margin-bottom: 30px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border);
        }

        .sidebar a {
            text-decoration: none;
            color: #cfd8dc;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 8px;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.03);
            cursor: pointer;
            font-weight: 500;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: rgba(224, 168, 0, 0.2);
            color: var(--gold);
            border: 1px solid rgba(224, 168, 0, 0.4);
        }

        .main {
            margin-left: 260px;
            flex: 1;
            z-index: 5;
            padding: 30px;
            width: calc(100% - 260px);
            background: rgba(13, 27, 42, 0.5);
            backdrop-filter: blur(5px);
        }

        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            align-items: center;
        }

        .header h1 {
            font-size: 2rem;
            font-weight: 600;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            border: 1px solid var(--border);
        }

        .card .label {
            font-size: 0.9rem;
            color: #aaa;
            margin-bottom: 8px;
        }

        .card .value {
            font-size: 2rem;
            font-weight: bold;
            color: var(--gold);
        }

        .panel {
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            padding: 20px;
            border: 1px solid var(--border);
            margin-bottom: 30px;
        }

        .panel h3 {
            color: var(--gold);
            margin-bottom: 15px;
        }

        .search-box {
            margin-bottom: 15px;
            display: flex;
            gap: 10px;
        }

        .search-box input {
            padding: 10px 15px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: rgba(0, 0, 0, 0.4);
            color: white;
            width: 250px;
            outline: none;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            color: #ddd;
        }

        th {
            text-align: left;
            padding: 12px 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            font-weight: 600;
            color: var(--gold);
            cursor: pointer;
            user-select: none;
        }

        th:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        td {
            padding: 12px 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .badge {
            background: #1b3a4b;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
        }

        .badge.warning {
            background: #b68b00;
            color: black;
        }

        .badge.success {
            background: #2e7d32;
        }

        .text-warning {
            color: #ffc107;
        }

        .text-success {
            color: #4caf50;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }
            .main {
                margin-left: 200px;
                width: calc(100% - 200px);
            }
        }
    </style>
</head>
<body>
    <canvas id="bgCanvas"></canvas>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>⛏️ Gudang Tambang</h2>
        <a class="active" data-page="dashboard">📊 Dashboard</a>
        <a data-page="master-barang">📋 Master Barang</a>
        <a data-page="stok-gudang">📦 Stok Gudang</a>
    </div>

    <!-- Main -->
    <div class="main">
        <div class="header">
            <h1 id="pageTitle">Dashboard</h1>
        </div>
        <div id="dynamicContent"></div>
    </div>

    <script>
        // ======================= DATA DUMMY =======================
        // Tabel referensi (FK)
        const departments = [
            { id: 1, kode: 'PRD', nama: 'Produksi' },
            { id: 2, kode: 'WH', nama: 'Workshop' },
            { id: 3, kode: 'HSE', nama: 'HSE' },
            { id: 4, kode: 'LOG', nama: 'Logistik' }
        ];
        const unitTypes = [
            { id: 1, nama: 'Unit Alat Berat' },
            { id: 2, nama: 'Unit Kendaraan Ringan' },
            { id: 3, nama: 'Unit Listrik' }
        ];

        // Master Barang (kode: DEPT-Part-nourut)
        const items = [
            { id: 1, kode_barang: 'PRD-001-0001', nama_barang: 'Kertas A4 80gsm', part_number: 'KRT-A4-001',
                department_id: 1, jenis_unit_id: null },
            { id: 2, kode_barang: 'WH-002-0001', nama_barang: 'Bearing 6205', part_number: 'BRG-6205', department_id: 2,
                jenis_unit_id: 1 },
            { id: 3, kode_barang: 'HSE-003-0001', nama_barang: 'Safety Helmet', part_number: 'SH-01', department_id: 3,
                jenis_unit_id: null },
            { id: 4, kode_barang: 'LOG-004-0001', nama_barang: 'Lakban Kain Hitam', part_number: 'LK-01',
                department_id: 4, jenis_unit_id: null },
            { id: 5, kode_barang: 'PRD-001-0002', nama_barang: 'Tinta Printer', part_number: 'INK-001',
                department_id: 1, jenis_unit_id: null },
        ];

        // Stok (letak format A1B, rak B, stok)
        const stocks = [
            { id: 1, kode_barang: 'PRD-001-0001', letak: 'A1B', rak: 'B', stok: 150 },
            { id: 2, kode_barang: 'WH-002-0001', letak: 'C3A', rak: 'A', stok: 4 },
            { id: 3, kode_barang: 'HSE-003-0001', letak: 'D5C', rak: 'C', stok: 20 },
            { id: 4, kode_barang: 'LOG-004-0001', letak: 'B2B', rak: 'B', stok: 2 },
            { id: 5, kode_barang: 'PRD-001-0002', letak: 'A1A', rak: 'A', stok: 8 },
        ];

        // Fungsi bantu: ambil departemen & unit berdasarkan ID
        function getDeptName(id) { return departments.find(d => d.id == id)?.nama || '-'; }

        function getUnitName(id) { return unitTypes.find(u => u.id == id)?.nama || '-'; }

        // ======================= NAVIGASI =======================
        const links = document.querySelectorAll('.sidebar a');
        const pageTitle = document.getElementById('pageTitle');
        const dynamicContent = document.getElementById('dynamicContent');

        links.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                links.forEach(l => l.classList.remove('active'));
                this.classList.add('active');
                loadPage(this.dataset.page);
            });
        });

        function loadPage(page) {
            switch (page) {
                case 'dashboard':
                    pageTitle.textContent = 'Dashboard';
                    dynamicContent.innerHTML = renderDashboard();
                    break;
                case 'master-barang':
                    pageTitle.textContent = 'Master Barang';
                    dynamicContent.innerHTML = renderMasterBarang();
                    initTableSearch('table-master-barang');
                    break;
                case 'stok-gudang':
                    pageTitle.textContent = 'Stok Gudang';
                    dynamicContent.innerHTML = renderStokGudang();
                    initTableSearch('table-stok-gudang');
                    break;
            }
        }

        // ======================= DASHBOARD =======================
        function renderDashboard() {
            const totalItems = items.length;
            const totalStock = stocks.reduce((sum, s) => sum + s.stok, 0);
            const lowStockItems = stocks.filter(s => s.stok <= 5).length;
            return `
                <div class="cards">
                    <div class="card"><div class="label">Total Barang</div><div class="value">${totalItems}</div></div>
                    <div class="card"><div class="label">Total Stok</div><div class="value">${totalStock}</div></div>
                    <div class="card"><div class="label">Barang Menipis (≤5)</div><div class="value">${lowStockItems}</div></div>
                </div>
                <div class="panel">
                    <h3>📋 Stok Terbaru</h3>
                    <table>
                        <thead><tr><th>Kode Barang</th><th>Nama Barang</th><th>Letak</th><th>Stok</th></tr></thead>
                        <tbody>
                            ${stocks.slice(0,5).map(s => {
                                const item = items.find(i => i.kode_barang === s.kode_barang);
                                return `<tr>
                                    <td>${s.kode_barang}</td>
                                    <td>${item?.nama_barang || '-'}</td>
                                    <td>${s.letak} (Rak ${s.rak})</td>
                                    <td><span class="badge ${s.stok <=5 ? 'warning' : 'success'}">${s.stok}</span></td>
                                </tr>`;
                            }).join('')}
                        </tbody>
                    </table>
                </div>
            `;
        }

        // ======================= MASTER BARANG =======================
        function renderMasterBarang() {
            let html = `
            <div class="panel">
                <h3>Daftar Barang</h3>
                <div class="search-box">
                    <input type="text" id="searchInputMaster" placeholder="Cari kode, nama, part number...">
                </div>
                <table id="table-master-barang">
                    <thead>
                        <tr>
                            <th data-sort="kode_barang">Kode Barang</th>
                            <th data-sort="nama_barang">Nama Barang</th>
                            <th data-sort="part_number">Part Number</th>
                            <th data-sort="department">Departemen</th>
                            <th data-sort="jenis_unit">Jenis Unit</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${items.map(it => `
                            <tr>
                                <td>${it.kode_barang}</td>
                                <td>${it.nama_barang}</td>
                                <td>${it.part_number}</td>
                                <td>${getDeptName(it.department_id)}</td>
                                <td>${it.jenis_unit_id ? getUnitName(it.jenis_unit_id) : '-'}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>`;
            return html;
        }

        // ======================= STOK GUDANG =======================
        function renderStokGudang() {
            const enriched = stocks.map(s => {
                const item = items.find(i => i.kode_barang === s.kode_barang);
                return { ...s, nama_barang: item?.nama_barang || '-', part_number: item?.part_number || '-' };
            });
            let html = `
            <div class="panel">
                <h3>Data Stok</h3>
                <div class="search-box">
                    <input type="text" id="searchInputStok" placeholder="Cari kode, nama, letak...">
                </div>
                <table id="table-stok-gudang">
                    <thead>
                        <tr>
                            <th data-sort="kode_barang">Kode Barang</th>
                            <th data-sort="nama_barang">Nama Barang</th>
                            <th data-sort="letak">Letak</th>
                            <th data-sort="rak">Rak</th>
                            <th data-sort="stok">Stok</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${enriched.map(e => `
                            <tr>
                                <td>${e.kode_barang}</td>
                                <td>${e.nama_barang}</td>
                                <td>${e.letak}</td>
                                <td>${e.rak}</td>
                                <td>${e.stok}</td>
                                <td><span class="badge ${e.stok <=5 ? 'warning' : 'success'}">${e.stok <=5 ? 'Menipis' : 'Cukup'}</span></td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>`;
            return html;
        }

        // ======================= SEARCH & SORT SIMPLE =======================
        function initTableSearch(tableId) {
            const input = document.getElementById(tableId === 'table-master-barang' ? 'searchInputMaster' :
                'searchInputStok');
            if (!input) return;
            input.addEventListener('keyup', function() {
                const filter = this.value.toLowerCase();
                const rows = document.querySelectorAll(`#${tableId} tbody tr`);
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(filter) ? '' : 'none';
                });
            });

            // Sorting on click
            const headers = document.querySelectorAll(`#${tableId} th[data-sort]`);
            headers.forEach(th => {
                th.addEventListener('click', function() {
                    const table = this.closest('table');
                    const tbody = table.querySelector('tbody');
                    const rows = Array.from(tbody.querySelectorAll('tr'));
                    const colIndex = Array.from(this.parentNode.children).indexOf(this);
                    const sortKey = this.dataset.sort;
                    const asc = this.classList.contains('asc');
                    // reset arrows
                    headers.forEach(h => h.classList.remove('asc', 'desc'));
                    this.classList.add(asc ? 'desc' : 'asc');
                    rows.sort((a, b) => {
                        const aVal = a.children[colIndex].textContent.trim();
                        const bVal = b.children[colIndex].textContent.trim();
                        if (!isNaN(aVal) && !isNaN(bVal)) {
                            return asc ? bVal - aVal : aVal - bVal;
                        }
                        return asc ? bVal.localeCompare(aVal) : aVal.localeCompare(bVal);
                    });
                    rows.forEach(row => tbody.appendChild(row));
                });
            });
        }

        // ======================= BACKGROUND PARTICLES =======================
        const canvas = document.getElementById('bgCanvas');
        const ctx = canvas.getContext('2d');
        let particles = [];

        function resizeCanvas() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        }
        window.addEventListener('resize', () => {
            resizeCanvas();
            initParticles();
        });
        resizeCanvas();

        class Particle {
            constructor() {
                this.x = Math.random() * canvas.width;
                this.y = Math.random() * canvas.height;
                this.size = Math.random() * 2 + 0.5;
                this.speedX = (Math.random() - 0.5) * 0.4;
                this.speedY = (Math.random() - 0.5) * 0.4;
                this.color = `rgba(224,168,0,${Math.random()*0.25+0.05})`;
            }
            update() {
                this.x += this.speedX;
                this.y += this.speedY;
                if (this.x < 0 || this.x > canvas.width) this.speedX *= -1;
                if (this.y < 0 || this.y > canvas.height) this.speedY *= -1;
            }
            draw() {
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fillStyle = this.color;
                ctx.fill();
            }
        }

        function initParticles() {
            particles = [];
            const num = Math.floor((canvas.width * canvas.height) / 10000);
            for (let i = 0; i < num; i++) particles.push(new Particle());
        }
        initParticles();

        function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            for (let i = 0; i < particles.length; i++) {
                particles[i].update();
                particles[i].draw();
                for (let j = i + 1; j < particles.length; j++) {
                    const dx = particles[i].x - particles[j].x;
                    const dy = particles[i].y - particles[j].y;
                    const dist = Math.sqrt(dx * dx + dy * dy);
                    if (dist < 100) {
                        ctx.beginPath();
                        ctx.moveTo(particles[i].x, particles[i].y);
                        ctx.lineTo(particles[j].x, particles[j].y);
                        ctx.strokeStyle = `rgba(224,168,0,${0.06 * (1 - dist / 100)})`;
                        ctx.lineWidth = 0.5;
                        ctx.stroke();
                    }
                }
            }
            requestAnimationFrame(animate);
        }
        animate();

        // Initial load
        loadPage('dashboard');
    </script>
</body>
</html>