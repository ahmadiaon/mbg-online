@extends('app.layout.main')

@section('src_css')
    <style>
        :root {
            --node-bg: #ffffff;
            --node-border: #0d6efd;
            --line-color: #0d6efd;
            --node-min-height: 140px;
            --node-gap-vertical: 28px;
            --node-gap-horizontal: 24px;
        }

        body {
            font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
            background: #f5f7fb;
            margin: 0;
            padding: 20px;
        }

        .wrap {
            border-radius: 8px;
            background: white;
            padding: 16px;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
        }

        .org-chart {
            position: relative;
            display: flex;
            flex-direction: column;
            gap: var(--node-gap-vertical);
            padding: 24px;
            overflow-x: auto;
            overflow-y: visible;
            min-width: 900px;
        }

        .org-level {
            display: flex;
            gap: var(--node-gap-horizontal);
            justify-content: center;
        }

        .org-node {
            background: var(--node-bg);
            border: 2px solid var(--node-border);
            border-radius: 8px;
            padding: 10px 14px;
            min-height: var(--node-min-height);
            box-sizing: border-box;
            text-align: center;
            width: 160px;
            cursor: default;
            transition: all .18s ease;
            position: relative;
            opacity: 1;
            transition: opacity 0.3s ease;
        }

        .org-node.fade-out {
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .org-node .photo {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 8px auto;
            border: 2px solid var(--node-border);
        }

        .org-node .title {
            font-weight: 600;
            color: #0f172a;
        }

        .org-node .meta {
            font-size: 12px;
            color: #475569;
            margin-top: 6px;
        }

        .org-node .person {
            margin-top: 8px;
            font-weight: 500;
            color: #1e40af;
        }

        .org-node .toggle-btn {
            position: absolute;
            top: 4px;
            right: 4px;
            background: #0d6efd;
            color: white;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            line-height: 18px;
            cursor: pointer;
            padding: 0;
        }

        /* SVG overlay */
        .svg-overlay {
            position: absolute;
            top: 0;
            left: 0;
            pointer-events: none;
            z-index: 1;
            overflow: visible;
        }

        .org-node.highlight {
            background: #e8f0ff;
            border-color: #1e6fff;
            box-shadow: 0 6px 16px rgba(30, 111, 255, 0.12);
        }
    </style>
@endsection()

@section('content')
    <div id="chartArea" style="position:relative;">
        <div id="orgChart" class="org-chart">
            <svg id="svgOverlay" class="svg-overlay"></svg>
        </div>
    </div>
@endsection()


@section('js_code')
    <script>
        $(document).ready(function() {
            $('.body-content').show();
            $('.loading').hide();

        });
    </script>
    <script>
        const sampleData = [{
                id: 1,
                name: "Komisaris",
                title: "Ketua Komisaris",
                person: "Bapak Joko",
                parent: null,
                photo: "https://i.pravatar.cc/60?img=1"
            },
            {
                id: 2,
                name: "General Manager",
                title: "GM Utama",
                person: "Ibu Sari",
                parent: 1,
                photo: "https://i.pravatar.cc/60?img=2"
            },

            {
                id: 3,
                name: "Direktur PT Mitra Barito",
                title: "Direktur Utama",
                person: "Bapak Anton",
                parent: 2,
                photo: "https://i.pravatar.cc/60?img=3"
            },
            {
                id: 4,
                name: "Direktur PT Mitra Barito Lumbung Energi",
                title: "Direktur Keuangan",
                person: "Ibu Rina",
                parent: 2,
                photo: "https://i.pravatar.cc/60?img=4"
            },
            {
                id: 5,
                name: "Direktur CV Bunda Kandung",
                title: "Direktur Operasional",
                person: "Bapak Dedi",
                parent: 2,
                photo: "https://i.pravatar.cc/60?img=5"
            },

            // Projects
            {
                id: 100,
                name: "Project Alpha",
                title: "Project Manager",
                person: "Pak Agus",
                parent: 3,
                photo: "https://i.pravatar.cc/60?img=20"
            },
            {
                id: 101,
                name: "Project Beta",
                title: "Project Manager",
                person: "Bu Reni",
                parent: 4,
                photo: "https://i.pravatar.cc/60?img=21"
            },
            {
                id: 102,
                name: "Project Gamma",
                title: "Project Manager",
                person: "Pak Wawan",
                parent: 5,
                photo: "https://i.pravatar.cc/60?img=22"
            },

            // Departments for Project Alpha
            {
                id: 201,
                name: "Departemen HR",
                title: "Kepala HR",
                person: "Agus Sunarno",
                parent: 100,
                photo: "https://i.pravatar.cc/60?img=6"
            },
            {
                id: 202,
                name: "Departemen HSE",
                title: "Kepala HSE",
                person: "Sari",
                parent: 100,
                photo: "https://i.pravatar.cc/60?img=7"
            },
            {
                id: 203,
                name: "Departemen Produksi",
                title: "Kepala Produksi",
                person: "Reni",
                parent: 100,
                photo: "https://i.pravatar.cc/60?img=8"
            },
            {
                id: 204,
                name: "Departemen Workshop",
                title: "Kepala Workshop",
                person: "Wawan",
                parent: 100,
                photo: "https://i.pravatar.cc/60?img=9"
            },
            {
                id: 205,
                name: "Departemen Engineering",
                title: "Kepala Engineering",
                person: "Budi",
                parent: 100,
                photo: "https://i.pravatar.cc/60?img=10"
            },

            // Divisions under each department Project Alpha (HR)
            {
                id: 301,
                name: "Divisi Rekrutmen",
                title: "Manager Rekrutmen",
                person: "Rina",
                parent: 201,
                photo: "https://i.pravatar.cc/60?img=11"
            },
            {
                id: 302,
                name: "Divisi Payroll",
                title: "Manager Payroll",
                person: "Dedi",
                parent: 201,
                photo: "https://i.pravatar.cc/60?img=12"
            },

            // Divisions under HSE
            {
                id: 303,
                name: "Divisi Keselamatan",
                title: "Manager Keselamatan",
                person: "Anton",
                parent: 202,
                photo: "https://i.pravatar.cc/60?img=13"
            },
            {
                id: 304,
                name: "Divisi Lingkungan",
                title: "Manager Lingkungan",
                person: "Maya",
                parent: 202,
                photo: "https://i.pravatar.cc/60?img=14"
            },

            // Divisions under Produksi
            {
                id: 305,
                name: "Divisi Line A",
                title: "Supervisor Line A",
                person: "Sari",
                parent: 203,
                photo: "https://i.pravatar.cc/60?img=15"
            },
            {
                id: 306,
                name: "Divisi Line B",
                title: "Supervisor Line B",
                person: "Wawan",
                parent: 203,
                photo: "https://i.pravatar.cc/60?img=16"
            },

            // Divisions under Workshop
            {
                id: 307,
                name: "Divisi Pemeliharaan",
                title: "Manager Pemeliharaan",
                person: "Reni",
                parent: 204,
                photo: "https://i.pravatar.cc/60?img=17"
            },

            // Divisions under Engineering
            {
                id: 308,
                name: "Divisi Desain",
                title: "Koordinator Desain",
                person: "Budi",
                parent: 205,
                photo: "https://i.pravatar.cc/60?img=18"
            },
            {
                id: 309,
                name: "Divisi Konstruksi",
                title: "Koordinator Konstruksi",
                person: "Dedi",
                parent: 205,
                photo: "https://i.pravatar.cc/60?img=19"
            },

            // Repeat similar departments/divisions for Project Beta
            {
                id: 210,
                name: "Departemen HR",
                title: "Kepala HR",
                person: "Budi",
                parent: 101,
                photo: "https://i.pravatar.cc/60?img=20"
            },
            {
                id: 211,
                name: "Departemen HSE",
                title: "Kepala HSE",
                person: "Rina",
                parent: 101,
                photo: "https://i.pravatar.cc/60?img=21"
            },
            {
                id: 212,
                name: "Departemen Produksi",
                title: "Kepala Produksi",
                person: "Anton",
                parent: 101,
                photo: "https://i.pravatar.cc/60?img=22"
            },
            {
                id: 213,
                name: "Departemen Workshop",
                title: "Kepala Workshop",
                person: "Maya",
                parent: 101,
                photo: "https://i.pravatar.cc/60?img=23"
            },
            {
                id: 214,
                name: "Departemen Engineering",
                title: "Kepala Engineering",
                person: "Sari",
                parent: 101,
                photo: "https://i.pravatar.cc/60?img=24"
            },

            // Divisions Project Beta (HR)
            {
                id: 315,
                name: "Divisi Rekrutmen",
                title: "Koordinator Rekrutmen",
                person: "Dedi",
                parent: 210,
                photo: "https://i.pravatar.cc/60?img=25"
            },
            {
                id: 324,
                name: "Divisi GA",
                title: "Koordinator GA",
                person: "Icuk",
                parent: 210,
                photo: "https://i.pravatar.cc/60?img=25"
            },
            //   { id: 325, name: "Divisi GA", title: "Koordinator GA", person: "Icuk", parent: 210, photo: "https://i.pravatar.cc/60?img=25" },
            {
                id: 316,
                name: "Divisi Payroll",
                title: "Koordinator Payroll",
                person: "Reni",
                parent: 210,
                photo: "https://i.pravatar.cc/60?img=26"
            },

            // Divisions HSE
            {
                id: 317,
                name: "Divisi Keselamatan",
                title: "Koordinator Keselamatan",
                person: "Wawan",
                parent: 211,
                photo: "https://i.pravatar.cc/60?img=27"
            },
            {
                id: 318,
                name: "Divisi Lingkungan",
                title: "Koordinator Lingkungan",
                person: "Rina",
                parent: 211,
                photo: "https://i.pravatar.cc/60?img=28"
            },

            // Divisions Produksi
            {
                id: 319,
                name: "Divisi Line A",
                title: "Supervisor Line A",
                person: "Anton",
                parent: 212,
                photo: "https://i.pravatar.cc/60?img=29"
            },
            {
                id: 320,
                name: "Divisi Line B",
                title: "Supervisor Line B",
                person: "Maya",
                parent: 212,
                photo: "https://i.pravatar.cc/60?img=30"
            },

            // Divisions Workshop
            {
                id: 321,
                name: "Divisi Pemeliharaan",
                title: "Koordinator Pemeliharaan",
                person: "Sari",
                parent: 213,
                photo: "https://i.pravatar.cc/60?img=31"
            },

            // Divisions Engineering
            {
                id: 322,
                name: "Divisi Desain",
                title: "Koordinator Desain",
                person: "Budi",
                parent: 214,
                photo: "https://i.pravatar.cc/60?img=32"
            },
            {
                id: 323,
                name: "Divisi Konstruksi",
                title: "Koordinator Konstruksi",
                person: "Dedi",
                parent: 214,
                photo: "https://i.pravatar.cc/60?img=33"
            },

            // Project Gamma (you can add similarly if needed)
        ];
        const orgChartEl = document.getElementById('orgChart');
        const svgEl = document.getElementById('svgOverlay');
        const hiddenBranches = new Set();

        function buildLevels(data) {
            const roots = data.filter(d => d.parent === null || d.parent === undefined);
            const levels = [];
            let currentLevelNodes = roots;

            while (currentLevelNodes.length > 0) {
                levels.push(currentLevelNodes);
                const nextLevelNodes = [];
                currentLevelNodes.forEach(node => {
                    data.forEach(d => {
                        if (d.parent === node.id) nextLevelNodes.push(d);
                    });
                });
                currentLevelNodes = nextLevelNodes;
            }
            return levels;
        }

        function renderChart(data) {
            orgChartEl.querySelectorAll('.org-level').forEach(n => n.remove());

            const levels = buildLevels(data);

            levels.forEach((nodes, i) => {
                const levelEl = document.createElement('div');
                levelEl.className = 'org-level';
                levelEl.dataset.level = i;

                nodes.forEach(node => {
                    const nodeEl = document.createElement('div');
                    nodeEl.className = 'org-node';
                    nodeEl.dataset.id = node.id;
                    nodeEl.dataset.parent = node.parent ?? '';

                    nodeEl.innerHTML = `
        <button class="toggle-btn" data-id="${node.id}">–</button>
        <img class="photo" src="${escapeHtml(node.photo)}" alt="Foto ${escapeHtml(node.person)}" />
        <div class="title">${escapeHtml(node.name)}</div>
        <div class="meta">${escapeHtml(node.title ?? '')}</div>
        <div class="person">${escapeHtml(node.person ?? '')}</div>
      `;

                    nodeEl.addEventListener('click', (e) => {
                        if (!e.target.classList.contains('toggle-btn')) {
                            clearHighlights();
                            highlightBranch(node.id);
                        }
                    });

                    levelEl.appendChild(nodeEl);
                });

                orgChartEl.appendChild(levelEl);
            });

            setTimeout(() => {
                drawConnectors();
            }, 30);
        }

        function escapeHtml(str) {
            return String(str).replace(/[&<>"']/g, m => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            })[m]);
        }

        function clearHighlights() {
            document.querySelectorAll('.org-node').forEach(n => n.classList.remove('highlight'));
            svgEl.querySelectorAll('path, circle').forEach(el => {
                el.setAttribute('stroke-opacity', '1');
                if (el.tagName.toLowerCase() === 'path') el.setAttribute('stroke-width', '2');
                el.style.opacity = '';
            });
        }

        function getBranchIds(rootId) {
            const ids = [Number(rootId)];
            sampleData.forEach(n => {
                if (n.parent === Number(rootId)) {
                    ids.push(...getBranchIds(n.id));
                }
            });
            return ids;
        }

        function highlightBranch(rootId) {
            const toHighlight = getBranchIds(rootId);
            toHighlight.forEach(id => {
                const nodeEl = orgChartEl.querySelector(`.org-node[data-id='${id}']`);
                if (nodeEl) nodeEl.classList.add('highlight');
            });

            svgEl.querySelectorAll('path').forEach(p => {
                const pid = p.getAttribute('data-parent');
                const cid = p.getAttribute('data-child');
                if (toHighlight.includes(Number(pid)) && toHighlight.includes(Number(cid))) {
                    p.setAttribute('stroke-width', '3');
                    p.style.opacity = '1';
                } else {
                    p.style.opacity = '0.14';
                }
            });
            svgEl.querySelectorAll('circle').forEach(c => {
                const pid = c.getAttribute('data-parent');
                const cid = c.getAttribute('data-child');
                if (toHighlight.includes(Number(pid)) && toHighlight.includes(Number(cid))) {
                    c.style.opacity = '1';
                } else c.style.opacity = '0.2';
            });
        }

        function drawConnectors() {
            while (svgEl.firstChild) svgEl.removeChild(svgEl.firstChild);

            const width = Math.max(orgChartEl.scrollWidth, orgChartEl.clientWidth);
            const height = Math.max(orgChartEl.scrollHeight, orgChartEl.clientHeight);

            svgEl.setAttribute('width', width);
            svgEl.setAttribute('height', height);
            svgEl.setAttribute('viewBox', `0 0 ${width} ${height}`);
            svgEl.style.width = width + 'px';
            svgEl.style.height = height + 'px';

            const chartRect = orgChartEl.getBoundingClientRect();
            const domById = {};
            orgChartEl.querySelectorAll('.org-node').forEach(n => domById[n.dataset.id] = n);

            Object.values(domById).forEach(childEl => {
                if (childEl.style.display === 'none') return;
                const pid = childEl.dataset.parent;
                if (!pid) return;
                const parentEl = domById[pid];
                if (!parentEl || parentEl.style.display === 'none') return;

                const rChild = childEl.getBoundingClientRect();
                const rParent = parentEl.getBoundingClientRect();

                const fromX = (rParent.left - chartRect.left) + parentEl.offsetWidth / 2 + orgChartEl.scrollLeft;
                const fromY = (rParent.bottom - chartRect.top) + orgChartEl.scrollTop;
                const toX = (rChild.left - chartRect.left) + childEl.offsetWidth / 2 + orgChartEl.scrollLeft;
                const toY = (rChild.top - chartRect.top) + orgChartEl.scrollTop;

                const pathD = `M${fromX} ${fromY} V${(fromY + toY) / 2} H${toX} V${toY}`;
                const path = document.createElementNS("http://www.w3.org/2000/svg", 'path');
                path.setAttribute('d', pathD);
                path.setAttribute('stroke', getComputedStyle(document.documentElement).getPropertyValue(
                    '--line-color') || '#0d6efd');
                path.setAttribute('stroke-width', '2');
                path.setAttribute('fill', 'none');
                path.setAttribute('data-parent', pid);
                path.setAttribute('data-child', childEl.dataset.id);
                svgEl.appendChild(path);

                const circle = document.createElementNS("http://www.w3.org/2000/svg", 'circle');
                circle.setAttribute('cx', toX);
                circle.setAttribute('cy', toY);
                circle.setAttribute('r', 3.5);
                circle.setAttribute('fill', getComputedStyle(document.documentElement).getPropertyValue(
                    '--line-color') || '#0d6efd');
                circle.setAttribute('data-parent', pid);
                circle.setAttribute('data-child', childEl.dataset.id);
                svgEl.appendChild(circle);
            });
        }

        orgChartEl.addEventListener('click', (e) => {
            if (e.target.classList.contains('toggle-btn')) {
                const id = Number(e.target.dataset.id);
                if (hiddenBranches.has(id)) {
                    hiddenBranches.delete(id);
                    e.target.textContent = '–';
                } else {
                    hiddenBranches.add(id);
                    e.target.textContent = '+';
                }
                applyHideShow();
            }
        });

        function applyHideShow() {
            document.querySelectorAll('.org-node').forEach(n => {
                n.style.display = '';
                n.classList.remove('fade-out');
            });
            const idsToHide = new Set();
            hiddenBranches.forEach(id => {
                getBranchIds(id).forEach(hid => idsToHide.add(hid));
            });
            idsToHide.forEach(id => {
                const el = orgChartEl.querySelector(`.org-node[data-id='${id}']`);
                if (el) {
                    el.classList.add('fade-out');
                    setTimeout(() => {
                        el.style.display = 'none';
                        drawConnectors();
                    }, 300);
                }
            });
            drawConnectors();
        }

        renderChart(sampleData);
        window.addEventListener('resize', () => {
            clearTimeout(window.resizeTimer);
            window.resizeTimer = setTimeout(() => {
                drawConnectors();
            }, 150);
        });
    </script>
@endsection()
