{{-- 
    1.0 Modal Succes

--}}

{{-- 1.0 Modal Success --}}

{{-- 1.0 Modal Success --}}

{{-- 2.0 modal filter datatable --}}
<div class="modal fade" id="modal-filter-datatable" tabindex="-1" role="dialog"
    aria-labelledby="header-modal-filter-datatable">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="header-modal-filter-datatable">
                    Filter Data
                </h4>
                <button type="button" class="close close-modal" data-dismiss="modal">
                    ×
                </button>
            </div>
            <div class="modal-body" id="body-modal-filter-datatable">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn close-modal btn-secondary" data-dismiss="modal">
                    Close
                </button>
                <button type="button" id="store-filter-datatable" onclick="storeDatatable('filter-datatable')"
                    class="btn btn-primary">
                    Save changes
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Filter -->
<div class="modal fade" id="filter-datatable-modal" tabindex="-1" aria-labelledby="filter-datatable-modalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filter-datatable-modalLabel">Filter DataTable</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="btn-list" id="filter-datatable-list-field">

                </div>
                <div class="mb-3 d-flex gap-2">
                    <button id="checkAll" class="btn btn-success btn-sm">Check All</button>
                    <button id="uncheckAll" class="btn btn-secondary btn-sm">Uncheck All</button>
                </div>

                <table id="popupTable" class="display table table-striped table-bordered" style="width:100%;">
                    <thead>
                        <tr>
                            <th style="width:50px;">Select</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="applyFilterBtn">Apply Filter</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
{{-- modal filter datatable --}}

<!-- Modal Filter -->
<div class="modal fade" id="DATA_filter-datatable-modal" tabindex="-1"
    aria-labelledby="DATA_filter-datatable-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="DATA_filter-datatable-modalLabel">Filter DataTable</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="btn-list" id="DATA_filter-datatable-list-field">

                </div>
                <div class="mb-3 d-flex gap-2">
                    <button id="DATA_checkAll" class="btn btn-success btn-sm">Check All</button>
                    <button id="DATA_uncheckAll" class="btn btn-secondary btn-sm">Uncheck All</button>
                </div>

                <table id="DATA_popupTable" class="display table table-striped table-bordered" style="width:100%;">
                    <thead>
                        <tr>
                            <th style="width:50px;">Select</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="DATA_applyFilterBtn">Apply Filter</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
{{-- modal filter datatable --}}

{{-- success modal --}}
<div class="modal fade" id="success-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center font-18">
                <h3 class="mb-20">Data Tersimpan</h3>
                <div class="mb-30 text-center">
                    <img src="/assets/vendors/images/success.png" />
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-primary" onclick="stopLoading()" data-dismiss="modal">
                    Done
                </button>
            </div>
        </div>
    </div>
</div>



{{-- LOADING MODAL --}}
<div class="modal fade" id="loading-modal" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <br>
            <br>
            <div class="modal-body text-center">
                <div class="spinner-grow text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <div class="spinner-grow text-secondary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <div class="spinner-grow text-success" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <div class="spinner-grow text-danger" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <div class="spinner-grow text-warning" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <div class="spinner-grow text-info" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <div class="spinner-grow text-light" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <div class="spinner-grow text-dark" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
            <br>
            <br>
        </div>
    </div>
</div>
