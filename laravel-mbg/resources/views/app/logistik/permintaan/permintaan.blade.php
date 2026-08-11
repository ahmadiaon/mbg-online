@extends('app.layout.main')
@section('src_css')
    <link rel="stylesheet" href="/assets/src/plugins/datatables/css/jquery.dataTables.min.css">
@endsection()
@section('content')
    <div class="row">
        <div class="col-md-2 mb-20">
            <div class="tab-pane fade show card-box pd-20" id="timeline" role="tabpanel">
                <form>
                    <!-- Compact Tanggal Field -->
                    <div class="mb-2">
                        <label for="tanggalInput" class="form-label small mb-1">Tanggal</label>
                        <input type="date" class="form-control form-control-sm" id="tanggalInput">
                    </div>

                    <!-- Compact perusahaan Field -->
                    <div class="mb-2">
                        <label for="perusahaanInput" class="form-label small mb-1">Perusahaan</label>
                        <input type="text" class="form-control form-control-sm" id="perusahaanInput"
                            placeholder="Perusahaan">
                    </div>

                    <!-- Compact Project Field -->
                    <div class="mb-2">
                        <label for="projectInput" class="form-label small mb-1">Project</label>
                        <input type="text" class="form-control form-control-sm" id="projectInput" placeholder="Project">
                    </div>

                    <!-- Compact Department Field -->
                    <div class="mb-2">
                        <label for="departmentInput" class="form-label small mb-1">Department</label>
                        <input type="text" class="form-control form-control-sm" id="departmentInput"
                            placeholder="Department">
                    </div>

                    <!-- Compact Divisi Field -->
                    <div class="mb-2">
                        <label for="divisiInput" class="form-label small mb-1">Divisi</label>
                        <input type="text" class="form-control form-control-sm" id="divisiInput" placeholder="Divisi">
                    </div>

                    <!-- Compact Keperluan Field -->
                    <div class="mb-2">
                        <label for="keperluanInput" class="form-label small mb-1">Keperluan</label>
                        <input type="text" class="form-control form-control-sm" id="keperluanInput"
                            placeholder="Keperluan">
                    </div>

                    <!-- Compact No Permintaan Field -->
                    <div class="mb-2">
                        <label for="noPermintaanInput" class="form-label small mb-1">No Permintaan</label>
                        <input type="text" class="form-control form-control-sm" id="noPermintaanInput"
                            placeholder="No Permintaan">
                    </div>

                    <!-- Small Button -->
                    <button type="submit" class="btn btn-primary btn-sm w-100">Sign In</button>
                </form>

            </div>
        </div>

        <div class="col-md-10 mb-20">
            <div class="tab-pane fade show card-box pd-20" id="timeline" role="tabpanel">
                <form>
                    <div class="row">
                        <div class="col-md-2">
                            <!-- Compact Part Number Field -->
                            <div class="mb-2">
                                <label for="partNumberInput" class="form-label small mb-1">Part Number</label>
                                <input type="text" class="form-control form-control-sm" id="partNumberInput"
                                    placeholder="Part Number">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <!-- Compact Nama Barang Field -->
                            <div class="mb-2">
                                <label for="namaBarangInput" class="form-label small mb-1">Nama Barang</label>
                                <input type="text" class="form-control form-control-sm" id="namaBarangInput"
                                    placeholder="Nama Barang">
                            </div>
                        </div>

                        <div class="col-md-1">
                            <!-- Compact Quantity Field -->
                            <div class="mb-2">
                                <label for="quantityInput" class="form-label small mb-1">Qty</label>
                                <input type="number" class="form-control form-control-sm" id="quantityInput"
                                    placeholder="Quantity">
                            </div>
                        </div>

                        <div class="col-md-1">
                            <!-- Compact Satuan Field -->
                            <div class="mb-2">
                                <label for="satuanInput" class="form-label small mb-1">Satuan</label>
                                <input type="text" class="form-control form-control-sm" id="satuanInput"
                                    placeholder="Satuan">
                            </div>
                        </div>
                        <div class="col-md-1">
                            <!-- Compact PR Status Field -->
                            <div class="mb-2">
                                <label for="prStatusInput" class="form-label small mb-1">PR Status</label>
                                <input type="text" class="form-control form-control-sm" id="prStatusInput"
                                    placeholder="PR Status">
                            </div>
                        </div>

                        <div class="col-md-1">
                            <!-- Compact Stok Update Field -->
                            <div class="mb-2">
                                <label for="stokUpdateInput" class="form-label small mb-1">Stok</label>
                                <input type="text" class="form-control form-control-sm" id="stokUpdateInput"
                                    placeholder="Stok Update">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <!-- Compact Remark Field -->
                            <div class="mb-2">
                                <label for="remarkInput" class="form-label small mb-1">Remark</label>
                                <input type="text" class="form-control form-control-sm" id="remarkInput"
                                    placeholder="Remark">
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mb-20">
            <div class="card-box pd-20">
                <h5 class="h4 mb-20">Data Permintaan</h5>
                <table class="data-table table stripe hover nowrap">
                    <thead>
                        <tr>
                            <th class="table-plus">No Permintaan</th>
                            <th>Keperluan</th>
                            <th>Nama Barang</th>
                            <th>Qty</th>
                            <th>Satuan</th>
                            <th>Urgensi</th>
                            <th>PR Status</th>
                            <th>Stok</th>
                            <th>Remark</th>
                            <th>Gambar</th>
                            <th class="datatable-nosort">Action</th>
                        </tr>
                    <tbody>
                        <tr>
                            <td class="table-plus"><span class="badge badge-pill"
                                    style="background-color: #e3165b;">PN-001</span></td>
                            <td>Keperluan Item</td>
                            <td>Deskripsi Part</td>
                            <td>10</td>
                            <td>Pcs</td>
                            <td>Normal</td>
                            <td>Approved</td>
                            <td>5</td>
                            <td>-</td>
                            <td><img src="#" style="width: 50px;"></td>
                            <td>
                                <div class="dropdown">
                                    <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle"
                                        href="#" role="button" data-toggle="dropdown">
                                        <i class="dw dw-more-2"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                        <a class="dropdown-item" href="#"><i class="dw dw-eye"></i> View</a>
                                        <a class="dropdown-item" href="#"><i class="dw dw-edit2"></i> Edit</a>
                                        <a class="dropdown-item" href="#"><i class="dw dw-delete-3"></i> Delete</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="table-plus"><span class="badge badge-pill"
                                    style="background-color: #e3165b;">PN-001</span></td>
                            <td>Keperluan Item</td>
                            <td>Deskripsi Part</td>
                            <td>10</td>
                            <td>Pcs</td>
                            <td>Normal</td>
                            <td>Approved</td>
                            <td>5</td>
                            <td>-</td>
                            <td><img src="#" style="width: 50px;"></td>
                            <td>
                                <div class="dropdown">
                                    <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle"
                                        href="#" role="button" data-toggle="dropdown">
                                        <i class="dw dw-more-2"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                        <a class="dropdown-item" href="#"><i class="dw dw-eye"></i> View</a>
                                        <a class="dropdown-item" href="#"><i class="dw dw-edit2"></i> Edit</a>
                                        <a class="dropdown-item" href="#"><i class="dw dw-delete-3"></i> Delete</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-12 mb-20">
            <div class="card-box pd-20">
                <h5 class="h4 mb-20">Data Purchasing Order</h5>
                <table class="data-table table stripe hover nowrap">
                    <thead>
                        <tr>
                            <th class="table-plus">No Permintaan</th>
                            <th>Nama Barang</th>
                            <th>Qty</th>
                            <th>Urgensi</th>
                            <th>Harga</th>
                            <th>Gambar</th>
                            <th>Vendor</th>
                            <th class="datatable-nosort">Action</th>
                        </tr>
                    <tbody>
                        <tr>
                            <td class="table-plus"><span class="badge badge-pill"
                                    style="background-color: #e3165b;">PN-001</span><br>Sumur Bor Santilik</td>
                            <td>Elbow <br> - </td>
                            <td>10 <br> Pcs</td>
                            <td>Normal <br> process</td>
                            <td>Rp. 50.000</td>
                            <td><img src="#" style="width: 50px;"></td>
                            <td>Haikal Air <br>Banjarmasin</td>
                            <td>
                                <div class="dropdown">
                                    <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle"
                                        href="#" role="button" data-toggle="dropdown">
                                        <i class="dw dw-more-2"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                        <a class="dropdown-item" href="#"><i class="dw dw-eye"></i> View</a>
                                        <a class="dropdown-item" href="#"><i class="dw dw-edit2"></i> Edit</a>
                                        <a class="dropdown-item" href="#"><i class="dw dw-delete-3"></i> Delete</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
@endsection()

@section('js_code')

@endsection()
