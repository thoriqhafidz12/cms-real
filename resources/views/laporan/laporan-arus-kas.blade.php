@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="m-0">{{ $titlePage }}</h3>
    </div>

    {{-- Filter Form --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter mr-2"></i>Filter Laporan</h6>
        </div>
        <div class="card-body">
            <form id="formFilter">
                <div class="row align-items-end">
                    @foreach ($form as $field)
                        <div class="{{ $field['col'] ?? 'col-md-6' }} mb-2">
                            <label>{{ $field['label'] }}
                                @if (!empty($field['required']))
                                    <span class="text-danger">*</span>
                                @endif
                            </label>
                            <input type="{{ $field['type'] }}" name="{{ $field['name'] }}" id="{{ $field['name'] }}"
                                class="form-control" value="{{ old($field['name'], $field['default'] ?? '') }}"
                                placeholder="{{ $field['placeholder'] }}">
                        </div>
                    @endforeach
                    <div class="col-md-12 mt-2">
                        <button type="submit" id="btnTampilkan" class="btn btn-primary btn-sm">
                            <i class="fas fa-search mr-1"></i>Tampilkan
                        </button>
                        <button type="button" id="btnReset" class="btn btn-secondary btn-sm ml-2">
                            <i class="fas fa-sync-alt mr-1"></i>Reset
                        </button>
                        <span class="ml-3 text-muted small" id="infoRange"></span>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Summary --}}
    <div id="summaryCard" class="row mb-3" style="display:none;">
        <div class="col-6 col-md-3">
            <div class="card border-left-secondary shadow h-100 py-1">
                <div class="card-body py-2">
                    <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Saldo Akhir</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="sumSaldoAkhir">-</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-left-success shadow h-100 py-1">
                <div class="card-body py-2">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Penerimaan</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="sumTotalTerima">-</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-left-danger shadow h-100 py-1">
                <div class="card-body py-2">
                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Pengeluaran</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="sumTotalKeluar">-</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-left-info shadow h-100 py-1">
                <div class="card-body py-2">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Transaksi</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="sumTotal">-</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-table mr-2"></i>Hasil Laporan</h6>
        </div>
        <div class="card-body">
            {{-- Loading --}}
            <div id="loadingIndicator" class="text-center py-5" style="display:none;">
                <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                <p class="mt-2 text-muted">Memuat data laporan...</p>
            </div>

            {{-- Empty state --}}
            <div id="emptyState" class="text-center py-4">
                <i class="fas fa-search fa-3x text-muted mb-2"></i>
                <p class="text-muted">Silakan isi filter tanggal lalu klik <strong>Tampilkan</strong>.</p>
            </div>

            {{-- Table --}}
            <div id="tableWrapper" class="table-responsive" style="display:none;">
                <table class="table table-bordered table-hover table-sm table-mobile-card" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr class="text-center">
                            <th class="text-center" width="5%">No</th>
                            @foreach ($grid as $col)
                                <th class="{{ $col['class'] ?? 'text-center' }}">{{ $col['label'] }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        {{-- diisi via AJAX --}}
                    </tbody>
                </table>
            </div>

            {{-- Error state --}}
            <div id="errorState" class="text-center py-4" style="display:none;">
                <i class="fas fa-exclamation-triangle fa-3x text-danger mb-2"></i>
                <p class="text-danger" id="errorMessage">Gagal memuat data.</p>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            const $form = $('#formFilter');
            const $btnLoad = $('#btnTampilkan');
            const $loading = $('#loadingIndicator');
            const $empty = $('#emptyState');
            const $tableWrap = $('#tableWrapper');
            const $tbody = $('#tableBody');
            const $error = $('#errorState');
            const $errorMsg = $('#errorMessage');
            const $summary = $('#summaryCard');
            const $sumSaldo = $('#sumSaldoAkhir');
            const $sumTotal = $('#sumTotal');
            const $infoRange = $('#infoRange');
            const $totalTerima = $('#sumTotalTerima');
            const $totalKeluar = $('#sumTotalKeluar');


            // Grid column definitions
            const gridColumns = @json($grid);

            // Set default tanggal: awal bulan ini — akhir bulan ini
            const now = new Date();
            const firstDay = new Date(now.getFullYear(), now.getMonth(), 1).toISOString().split('T')[0];
            const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0).toISOString().split('T')[0];
            $('#rpTglAwal').val(firstDay);
            $('#rpTglAkhir').val(lastDay);

            /**
             * Format angka ke format ribuan (10.000)
             */
            function formatRibuan(num) {
                return parseFloat(num || 0).toLocaleString('id-ID', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                });
            }

            /**
             * Escape & dan " untuk atribut data-label
             */
            function escAttr(s) {
                return String(s ?? '').replace(/&/g, '&amp;').replace(/"/g, '&quot;');
            }

            /**
             * Render satu baris tabel
             */
            function renderRow(item, index) {
                let html = '<tr>';
                html += '<td class="text-center" data-label="No">' + (index + 1) + '</td>';
                gridColumns.forEach(function(col) {
                    const val = item[col.field] ?? '';
                    if (col.type === 'date') {
                        html += '<td class="' + (col.class || 'text-center') + '" data-label="' + escAttr(col.label) + '">' +
                            (val ? new Date(val).toLocaleDateString('id-ID', {
                                day: '2-digit',
                                month: 'long',
                                year: 'numeric'
                            }) : '-') + '</td>';
                    } else if (col.type === 'angka') {
                        html += '<td class="' + (col.class || 'text-right') + '" data-label="' + escAttr(col.label) + '">' +
                            (val != null ? formatRibuan(val) : '-') + '</td>';
                    } else {
                        html += '<td class="' + (col.class || '') + '" data-label="' + escAttr(col.label) + '">' +
                            (val || '-') + '</td>';
                    }
                });
                html += '</tr>';
                return html;
            }

            /**
             * Load data via AJAX
             */
            function loadData() {
                const tglAwal = $('#rpTglAwal').val();
                const tglAkhir = $('#rpTglAkhir').val();

                if (!tglAwal || !tglAkhir) {
                    swalError('Filter Tidak Lengkap', 'Silakan isi tanggal awal dan tanggal akhir.');
                    return;
                }

                // Show loading, hide others
                $loading.show();
                $empty.hide();
                $tableWrap.hide();
                $error.hide();
                $summary.hide();
                $btnLoad.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Memuat...');

                $.ajax({
                    url: '{{ route($route . '.load') }}',
                    type: 'GET',
                    data: {
                        rpTglAwal: tglAwal,
                        rpTglAkhir: tglAkhir
                    },
                    dataType: 'json',
                    success: function(response) {
                        $loading.hide();
                        $btnLoad.prop('disabled', false).html(
                            '<i class="fas fa-search mr-1"></i>Tampilkan');
                        $infoRange.text('Menampilkan data ' +
                            new Date(tglAwal).toLocaleDateString('id-ID', {
                                day: '2-digit',
                                month: 'long',
                                year: 'numeric'
                            }) +
                            ' s/d ' +
                            new Date(tglAkhir).toLocaleDateString('id-ID', {
                                day: '2-digit',
                                month: 'long',
                                year: 'numeric'
                            }));

                        const data = response.data || [];

                        if (data.length === 0) {
                            $empty.html(
                                '<i class="fas fa-info-circle fa-3x text-info mb-2"></i>' +
                                '<p class="text-muted">Tidak ada transaksi pada rentang tanggal tersebut.</p>'
                            ).show();
                            $summary.hide();
                            return;
                        }

                        // Render rows
                        $tbody.empty();
                        data.forEach(function(item, i) {
                            $tbody.append(renderRow(item, i));
                        });

                        $tableWrap.show();
                        $summary.show();
                        $sumSaldo.text('Rp ' + formatRibuan(response.saldoAkhir || 0));
                        $sumTotal.text(formatRibuan(response.total || data.length) + ' Transaksi');
                        $totalTerima.text('Rp ' + formatRibuan(response.totalPenerimaan || 0));
                        $totalKeluar.text('Rp ' + formatRibuan(response.totalPengeluaran || 0));
                    },
                    error: function(xhr) {
                        $loading.hide();
                        $btnLoad.prop('disabled', false).html(
                            '<i class="fas fa-search mr-1"></i>Tampilkan');
                        $error.show();
                        const msg = xhr.responseJSON?.message || 'Gagal memuat data laporan.';
                        $errorMsg.text(msg);
                    }
                });
            }

            // ── Event handlers ──────────────────────────
            $form.on('submit', function(e) {
                e.preventDefault();
                loadData();
            });

            $('#btnReset').on('click', function() {
                $('#rpTglAwal').val(firstDay);
                $('#rpTglAkhir').val(lastDay);
                $tableWrap.hide();
                $summary.hide();
                $error.hide();
                $empty.show();
            });

            // Auto-load on page ready (opsional)
            // loadData();
        });
    </script>
@endpush
