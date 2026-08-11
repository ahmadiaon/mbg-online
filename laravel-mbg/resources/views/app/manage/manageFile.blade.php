@extends('app.layout.main')

@section('src_css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .file-item,
        .folder-item {
            cursor: pointer;
            transition: background 0.2s;
            border-radius: 8px;
            padding: 8px;
            text-align: center;
            word-break: break-all;
        }

        .file-item:hover,
        .folder-item:hover {
            background: #f0f0f0;
        }

        .file-item i,
        .folder-item i {
            font-size: 2rem;
            display: block;
            margin-bottom: 4px;
        }

        .breadcrumb-item a {
            text-decoration: none;
        }
    </style>
@endsection()

@section('content')
    <div class="container py-4">
        <h3 class="mb-3"><i class="bi bi-folder-symlink"></i> File Manager</h3>

        <!-- Toolbar -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <nav aria-label="breadcrumb" class="mb-0" id="breadcrumb">
                <ol class="breadcrumb mb-0"></ol>
            </nav>
            <div class="btn-group">
                <button class="btn btn-primary btn-sm" onclick="showUploadModal()"><i class="bi bi-upload"></i> Upload
                    File</button>
                <button class="btn btn-outline-secondary btn-sm" onclick="createFolder()"><i class="bi bi-folder-plus"></i>
                    New Folder</button>
            </div>
        </div>

        <!-- Daftar isi folder -->
        <div class="row" id="fileList"></div>

        <!-- Loading / Kosong -->
        <div id="loadingIndicator" class="text-center text-muted mt-5">
            <div class="spinner-border" role="status"></div>
            <p>Loading...</p>
        </div>
    </div>

    <!-- Modal Upload -->
    <div class="modal fade" id="uploadModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="file" id="fileInput" name="files" class="form-control" multiple>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="uploadFiles()">Upload</button>
                </div>
            </div>
        </div>
    </div>
@endsection()


@section('js_code')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // State
        const apiBase = '/api'; // Sesuaikan prefix API Laravel
        let currentFolderId = 1; // Root folder ID (pastikan ada)
        let currentPath = []; // Untuk breadcrumb [{id, name}]

        // CSRF Token (untuk Laravel)
        function csrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        }

        // Fetch contents of a folder
        async function fetchFolder(folderId) {
            showLoading(true);
            try {
                const res = await fetch(`${apiBase}/folders/${folderId}/contents`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                if (!res.ok) throw new Error('Gagal memuat folder');
                const data = await res.json();
                console.log('fetchFolder response:', data);
                renderFileList(data.folders || [], data.files || []);
            } catch (err) {
                alert('Error: ' + err.message);
            } finally {
                showLoading(false);
            }
        }

        // Render grid file/folder
        function renderFileList(folders, files) {
            const container = document.getElementById('fileList');
            container.innerHTML = '';

            // Folder items
            folders.forEach(folder => {
                const col = document.createElement('div');
                col.className = 'col-4 col-md-2';
                col.innerHTML = `
                    <div class="folder-item" onclick="navigateToFolder(${folder.id}, '${folder.name}')">
                        <i class="bi bi-folder-fill text-warning"></i>
                        <div>${folder.name}</div>
                        <small class="text-muted" onclick="event.stopPropagation(); deleteFolder(${folder.id})"><i class="bi bi-trash"></i></small>
                    </div>
                `;
                container.appendChild(col);
            });

            // File items
            files.forEach(file => {
                const col = document.createElement('div');
                col.className = 'col-4 col-md-2';
                col.innerHTML = `
                    <div class="file-item">
                        <i class="bi bi-file-earmark-fill text-primary"></i>
                        <div>${file.name}</div>
                        <small class="text-muted">${formatBytes(file.size)}</small>
                        <div><small class="text-danger" onclick="deleteFile(${file.id})"><i class="bi bi-trash"></i></small></div>
                    </div>
                `;
                container.appendChild(col);
            });

            if (folders.length === 0 && files.length === 0) {
                container.innerHTML = '<div class="col-12 text-muted text-center mt-4">Folder kosong</div>';
            }
        }

        // Navigasi ke folder tertentu
        function navigateToFolder(folderId, folderName) {
            // Update path breadcrumb
            if (folderId === 1) {
                currentPath = [];
            } else {
                // Cari apakah folder sudah ada di path (untuk menghindari duplikat saat kembali)
                const idx = currentPath.findIndex(p => p.id === folderId);
                if (idx >= 0) {
                    currentPath = currentPath.slice(0, idx + 1);
                } else {
                    currentPath.push({
                        id: folderId,
                        name: folderName
                    });
                }
            }
            currentFolderId = folderId;
            updateBreadcrumb();
            fetchFolder(folderId);
        }

        // Breadcrumb
        function updateBreadcrumb() {
            const bc = document.getElementById('breadcrumb').querySelector('ol');
            bc.innerHTML = '<li class="breadcrumb-item"><a href="#" onclick="navigateToFolder(1, \'Root\')">Root</a></li>';
            currentPath.forEach(p => {
                const li = document.createElement('li');
                li.className = 'breadcrumb-item';
                li.innerHTML = `<a href="#" onclick="navigateToFolder(${p.id}, '${p.name}')">${p.name}</a>`;
                bc.appendChild(li);
            });
        }

        // Upload file (menggunakan FormData)
        async function uploadFiles() {
            const input = document.getElementById('fileInput');
            if (!input.files.length) return alert('Pilih file terlebih dahulu');
            const formData = new FormData();
            formData.append('folder_id', currentFolderId);
            for (let file of input.files) {
                formData.append('files[]', file);
            }
            try {
                const res = await fetch(`${apiBase}/files`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken(),
                        'Accept': 'application/json' // ⬅️ wajib ditambahkan
                    },
                    body: formData
                });
                if (!res.ok) {
                    const err = await res.json(); // sekarang akan selalu JSON
                    throw new Error(err.message || 'Upload gagal');
                }
                bootstrap.Modal.getInstance(document.getElementById('uploadModal')).hide();
                fetchFolder(currentFolderId);
            } catch (err) {
                alert('Error: ' + err.message);
            }
        }

        // Buat folder baru
        async function createFolder() {
            const name = prompt('Nama folder:');
            if (!name) return;
            try {
                const res = await fetch(`${apiBase}/folders`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken()
                    },
                    body: JSON.stringify({
                        name: name,
                        parent_id: currentFolderId
                    })
                });
                if (!res.ok) {
                    const err = await res.json();
                    throw new Error(err.message || 'Gagal membuat folder');
                }
                fetchFolder(currentFolderId);
            } catch (err) {
                alert('Error: ' + err.message);
            }
        }

        // Hapus folder (dengan konfirmasi)
        async function deleteFolder(id) {
            if (!confirm('Hapus folder ini beserta isinya?')) return;
            try {
                await fetch(`${apiBase}/folders/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken()
                    }
                });
                fetchFolder(currentFolderId);
            } catch (err) {
                alert('Gagal menghapus folder');
            }
        }

        // Hapus file
        async function deleteFile(id) {
            if (!confirm('Hapus file ini?')) return;
            try {
                await fetch(`${apiBase}/files/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken()
                    }
                });
                fetchFolder(currentFolderId);
            } catch (err) {
                alert('Gagal menghapus file');
            }
        }

        // Tampilkan modal upload
        function showUploadModal() {
            document.getElementById('fileInput').value = '';
            const modal = new bootstrap.Modal(document.getElementById('uploadModal'));
            modal.show();
        }

        // Loading spinner
        function showLoading(show) {
            document.getElementById('loadingIndicator').style.display = show ? 'block' : 'none';
        }

        // Format bytes
        function formatBytes(bytes, decimals = 2) {
            if (!+bytes) return '0 B';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        }

        // Init
        document.addEventListener('DOMContentLoaded', () => {
            // Pastikan ada meta tag CSRF di layout Laravel Anda
            if (!document.querySelector('meta[name="csrf-token"]')) {
                const meta = document.createElement('meta');
                meta.name = 'csrf-token';
                meta.content = document.querySelector('meta[name="csrf-token"]')?.content || '';
                document.head.appendChild(meta);
            }
            navigateToFolder(1, 'Root');
        });
    </script>
@endsection()
