<script>
    $('.close-modal').click(function() {
        $('.modal').modal('hide');
    });

    function showAlertSuccess() {
        $('.modal').modal('hide');
        $('.modal').hide();
        $('.modal-backdrop').remove();
        $('.modal-backdrop').remove(); // Hapus backdrop yang tertinggal
        $('body').removeClass('modal-open');
        swal({
            position: 'top-end',
            type: 'success',
            title: 'Data Success Tersimpan',
            showConfirmButton: false,
            timer: 1500
        })
    }

    function errorModalSweet() {
        swal({
            type: 'error',
            title: 'Oops...',
            text: 'Something went wrong!',
        })
    }



    document.addEventListener('click', function(e) {
        if (e.target.closest('.close-modal')) {
            $('.modal.show').modal('hide');

            const canvas = document.getElementById('pdf-canvas');
            if (canvas) {
                const ctx = canvas.getContext('2d');
                ctx.clearRect(0, 0, canvas.width, canvas.height);
            }
        }
    });
</script>

<script>
    // FILTER DATATABLE
    function orderTable(columnIndex) {
        const table = $('#datatable-Group-Form').DataTable();
        const currentOrder = table.order();
        let newOrder;

        if (currentOrder.length > 0 && currentOrder[0][0] === columnIndex) {
            newOrder = [
                [columnIndex, currentOrder[0][1] === 'asc' ? 'desc' : 'asc']
            ];
        } else {
            newOrder = [
                [columnIndex, 'asc']
            ];
        }
        table.order(newOrder).draw();
    }

    function filterDatatable(columnID) {
        $('.filter-column').prop("hidden", true);
        $('.number-filter-' + columnID).removeAttr("hidden");
        // filterTable();
        $('#modal-filter-datatable').modal('show');


        $('#faq-' + columnID).collapse('show');
        // Scroll ke panel yang dibuka (opsional)
        setTimeout(function() {
            const $target = $('#faq-' + columnID);
            if ($target.length) {
                $('.modal-filter-datatable .modal-body').animate({
                    scrollTop: $target.offset().top - $('.modal-filter-datatable .modal-body').offset()
                        .top + $('.modal-filter-datatable .modal-body').scrollTop()
                }, 300);
            }
        }, 300);
    }

    function updateFilterOptions(exceptIndex) {
        const table = $('#datatable-Group-Form').DataTable();
        table.rows().invalidate().draw(false);

        $(".multi-filter").each(function() {
            const columnIndex = $(this).data("col-index");
            const select = $(this);

            if (columnIndex === exceptIndex) return;

            const selectedValues = select.val() || [];
            select.empty();

            const filteredData = table
                .column(columnIndex, {
                    search: "applied"
                })
                .data()
                .unique()
                .sort();

            select.append('<option value="all">[Pilih Semua]</option>');
            filteredData.each(function(d) {
                select.append('<option value="' + d + '">' + d + '</option>');
            });

            select.val(selectedValues);


            conLog('filteredData', filteredData);
        });
    }

    function filterTable() {
        const table = $('#datatable-Group-Form').DataTable();
        const filters = {};
        let activeIndex = null;

        $(".multi-filter").each(function() {
            const columnIndex = $(this).data("col-index");
            let selectedValues = $(this).val();

            if (selectedValues && selectedValues.includes("all")) {
                $(this).val($(this).find("option:not([value='all'])").map(function() {
                    return this.value;
                }).get());
                selectedValues = $(this).val();
            }

            if (selectedValues && selectedValues.length > 0) {
                filters[columnIndex] = selectedValues;
                if (activeIndex === null) {
                    activeIndex = columnIndex;
                }
            }
        });

        table.columns().every(function(index) {
            conLog('index', index);
            conLog('filters', filters);
            if (filters[index] && filters[index].length > 0) {
                this.search(filters[index].join("|"), true, false).draw();
            } else {
                this.search("").draw();
            }
        });



        updateFilterOptions(activeIndex);
    }


    function selectVisibleOptions(select) {
        console.log("selectVisibleOptions");
        conLog('select', select);
        // Ambil hasil pencarian select2 yang sedang tampil di dropdown
        let visibleValues = [];
        // Cari option yang sedang tampil di dropdown select2
        $('.select2-results__option[aria-selected="false"]').each(function() {
            // Ambil value dari data-select2-id, lalu cari option di select asli
            let text = $(this).text();
            // Cari option di select yang text-nya sama persis
            select.find('option').each(function() {
                if ($(this).text() === text && $(this).val() !== '__all__') {
                    visibleValues.push($(this).val());
                }
            });
        });
        // Set value select2 ke visibleValues
        select.val(visibleValues).trigger('change');
    }




    // Event delegation untuk select-all dan deselect-all
    $(document).on("click", ".select-all", function() {
        const colIndex = $(this).data("col");
        const select = $(".multi-filter[data-col-index='" + colIndex + "']");
        if (select.length) {
            select.val(select.find("option:not([value='all'])").map(function() {
                return this.value;
            }).get()).trigger("change");
        }
    });

    // delete localStorage
    $(document).on("click", ".btn-hapus-localstorage", function() {
        localStorage.removeItem('DATABASE_KARYAWAN');
        localStorage.removeItem('DATABASE_KARYAWAN_PUBLIC');
        localStorage.removeItem('DATABASE');
        // atau localStorage.removeItem('DATABASE_KARYAWAN');
        alert('LocalStorage berhasil dihapus!');
    });

    $(document).on("click", ".deselect-all", function() {
        const colIndex = $(this).data("col");
        const select = $(".multi-filter[data-col-index='" + colIndex + "']");
        if (select.length) {
            select.val([]).trigger("change");
        }
    });

    // Misal pada tombol khusus:
    $(document).on('click', '.pilih-semua-terfilter', function() {
        const colIndex = $(this).data('col');
        // conLog('colIndex', colIndex);
        const select = $(".multi-filter[data-col-index='" + colIndex + "']");
        select.find('option').each(function() {
            console.log('Value:', $(this).val(), 'Text:', $(this).text());
        });
        selectVisibleOptions(select);
    });


    $(document).on('click', '.closeModal', function() {
        $('.modal').modal('hide');
        $('.modal').hide();
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');
    });

    function closeModal() {
        $('.modal').modal('hide');
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');
    }



    // Event filter
    $(document).on("change", ".multi-filter", filterTable);
</script>
