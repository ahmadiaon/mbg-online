@extends('app.layout.main')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Daftar Cache</h3>
                    </div>
                    <div class="card-body">

                        {{-- FORM INPUT KEY & TOMBOL GET/DELETE --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="cacheKey" placeholder="cache">
                                    <br>
                                    <input type="text" class="form-control" id="code_table"
                                        placeholder="table (opsional)">
                                    <br><input type="text" class="form-control" id="code_data"
                                        placeholder="data (opsional)">
                                    <button class="btn btn-info" id="btnGet" type="button">Get</button>
                                    <button class="btn btn-danger" id="btnDelete" type="button">Delete</button>
                                </div>
                                <button class="btn btn-primary" id="refresh-db-cache" type="button">refresh db</button>
                                <button class="btn btn-success" id="refresh-super-cache" type="button">refresh super</button>
                                <div style="display: none;" class="loadingLogin text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Masukkan key cache lalu klik Get untuk melihat isi, atau
                                    Delete untuk menghapus.</small>
                            </div>
                        </div>

                        {{-- JSON CONSOLE VIEWER --}}
                        <h3>JSON Console Viewer</h3>
                        <textarea id="jsonInput" placeholder='{"nama":"Andi","usia":30}' style="width: 100%; height: 300px;"></textarea>
                        <br>
                        <button onclick="renderJSON()" class="btn btn-primary mb-2">Tampilkan</button>
                        <div id="output"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Detail --}}
    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail Cache</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="detailContent">
                        <p><strong>Key:</strong> <span id="detailKey"></span></p>
                        <p><strong>Value (raw):</strong></p>
                        <pre id="detailValue" style="background: #f8f9fa; padding: 10px; border-radius: 4px;"></pre>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js_code')
    <style>
        /* Tambahan CSS untuk JSON viewer */
        #output {
            font-family: 'SF Mono', 'Menlo', 'Consolas', monospace;
            font-size: 13px;
            line-height: 1.5;
            background: #fff;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            overflow-x: auto;
        }

        .tree {
            white-space: nowrap;
        }

        .node {
            display: inline-block;
            vertical-align: top;
        }

        .arrow {
            cursor: pointer;
            display: inline-block;
            width: 12px;
            user-select: none;
            color: #888;
        }

        .arrow.expanded::before {
            content: '▼';
            font-size: 10px;
        }

        .arrow.collapsed::before {
            content: '▶';
            font-size: 10px;
        }

        .children {
            display: block;
            padding-left: 16px;
            border-left: 1px dotted #ddd;
            margin-left: 6px;
        }

        .children.hidden {
            display: none;
        }

        .key {
            color: #881280;
        }

        .string {
            color: #c41a16;
        }

        .number {
            color: #1c00cf;
        }

        .boolean {
            color: #0b4f6c;
        }

        .null {
            color: #808080;
        }

        .bracket {
            color: #333;
        }

        .comma {
            color: #333;
        }
    </style>

    <script>
        // Definisikan objek pengelola cache

        (async () => {
            let element_option_type_data = '';
            let element_option_chosee_table = '';
            // const masterData = new MasterData();
            // const db_master_data = await masterData.getDatadataTable('slips');
            // const data = await masterData.getDatadataTable('slips', 'nrp', db['FILTER_APP']['PROFILE']['NRP'][
            //     'value_data'
            // ]);

            // const get_cache = await masterData.getCache('db', 'database');
            // console.log('DATA', data);
            // console.log('cache data :', get_cache);
            // console.log('db_master_data', db_master_data);
        })();

        console.log('db', db);
        $('.loadingLogin').hide();
        

        // Event tombol Get & Delete
        document.addEventListener('DOMContentLoaded', function() {
            const keyInput = document.getElementById('cacheKey');
            // GET
            document.getElementById('btnGet').addEventListener('click', async function() {
                const key = keyInput.value.trim();
                let code_table = $('#code_table').val() || null;
                let code_data = $('#code_data').val() || null;
                if (!key) return alert('Masukkan key cache.');
                $('.loadingLogin').show();
                try {
                    const result = await CacheManager.getCache(key, code_table, code_data);
                    console.log('Cache retrieved:', result);
                    // document.getElementById('jsonInput').value = JSON.stringify(result, null, 2);
                    $('.loadingLogin').hide();
                    alert('Cache berhasil diambil. Klik "Tampilkan" untuk melihatnya.');
                } catch (error) {
                    $('.loadingLogin').hide();
                    alert('Error: ' + error.message);
                }
            });

            document.getElementById('btnDelete').addEventListener('click', async function() {
                const key = keyInput.value.trim();
                if (!key) return alert('Masukkan key cache.');
                if (!confirm(`Hapus cache "${key}"?`)) return;
                try {
                    const result = await CacheManager.deleteCache(key);
                    alert('Cache dihapus.\nRespon: ' + JSON.stringify(result, null, 2));
                } catch (error) {
                    alert('Error: ' + error.message);
                }
            });
        });
    </script>

    <script>
        // Fungsi untuk menampilkan JSON di viewer
        function renderJSON() {
            const input = document.getElementById('jsonInput').value.trim();
            const out = document.getElementById('output');
            out.innerHTML = '';
            if (!input) return;
            try {
                const data = JSON.parse(input);
                // out.appendChild(buildTree(data, true));
            } catch (e) {
                out.innerHTML = '<span style="color:red">JSON tidak valid: ' + e.message + '</span>';
            }
        }

        function buildTree(obj, isLast) {
            const container = document.createElement('span');
            container.className = 'tree';

            // Null, boolean, number, string
            if (obj === null) {
                container.innerHTML = '<span class="null">null</span>' + (isLast ? '' : '<span class="comma">,</span>');
                return container;
            }
            if (typeof obj === 'boolean') {
                container.innerHTML = '<span class="boolean">' + obj + '</span>' + (isLast ? '' :
                    '<span class="comma">,</span>');
                return container;
            }
            if (typeof obj === 'number') {
                container.innerHTML = '<span class="number">' + obj + '</span>' + (isLast ? '' :
                    '<span class="comma">,</span>');
                return container;
            }
            if (typeof obj === 'string') {
                container.innerHTML = '<span class="string">"' + escapeHtml(obj) + '"</span>' + (isLast ? '' :
                    '<span class="comma">,</span>');
                return container;
            }

            // Array
            if (Array.isArray(obj)) {
                const node = document.createElement('span');
                node.className = 'node';

                const arrow = document.createElement('span');
                arrow.className = 'arrow expanded';
                arrow.onclick = function(e) {
                    e.stopPropagation();
                    const children = this.parentNode.querySelector('.children');
                    if (children) {
                        children.classList.toggle('hidden');
                        this.classList.toggle('expanded');
                        this.classList.toggle('collapsed');
                    }
                };

                const openBracket = document.createElement('span');
                openBracket.className = 'bracket';
                openBracket.textContent = '[';

                const children = document.createElement('span');
                children.className = 'children';

                obj.forEach((item, index) => {
                    children.appendChild(buildTree(item, index === obj.length - 1));
                    if (index < obj.length - 1) {
                        children.appendChild(document.createTextNode(' '));
                    }
                });

                const closeBracket = document.createElement('span');
                closeBracket.className = 'bracket';
                closeBracket.textContent = ']';

                node.appendChild(arrow);
                node.appendChild(openBracket);
                node.appendChild(children);
                node.appendChild(closeBracket);

                container.appendChild(node);
                if (!isLast) container.appendChild(document.createTextNode(','));
                return container;
            }

            // Object
            const keys = Object.keys(obj);
            if (keys.length === 0) {
                container.innerHTML = '<span class="bracket">{}</span>' + (isLast ? '' : '<span class="comma">,</span>');
                return container;
            }

            const node = document.createElement('span');
            node.className = 'node';

            const arrow = document.createElement('span');
            arrow.className = 'arrow expanded';
            arrow.onclick = function(e) {
                e.stopPropagation();
                const children = this.parentNode.querySelector('.children');
                if (children) {
                    children.classList.toggle('hidden');
                    this.classList.toggle('expanded');
                    this.classList.toggle('collapsed');
                }
            };

            const openBrace = document.createElement('span');
            openBrace.className = 'bracket';
            openBrace.textContent = '{';

            const children = document.createElement('span');
            children.className = 'children';

            keys.forEach((key, index) => {
                const line = document.createElement('div');
                const keySpan = document.createElement('span');
                keySpan.className = 'key';
                keySpan.textContent = key + ': ';
                line.appendChild(keySpan);
                line.appendChild(buildTree(obj[key], index === keys.length - 1));
                children.appendChild(line);
            });

            const closeBrace = document.createElement('span');
            closeBrace.className = 'bracket';
            closeBrace.textContent = '}';

            node.appendChild(arrow);
            node.appendChild(openBrace);
            node.appendChild(children);
            node.appendChild(closeBrace);

            container.appendChild(node);
            if (!isLast) container.appendChild(document.createTextNode(','));
            return container;
        }

        function escapeHtml(text) {
            return String(text).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }
    </script>
@endsection
