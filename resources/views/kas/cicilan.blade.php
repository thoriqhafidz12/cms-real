@extends('layouts.app')

@section('content')
    <div class="row">
        {{-- TOP: Form --}}
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        {{ isset($editData) ? 'Edit Data' : 'Tambah Data' }}
                    </h6>
                </div>
                <div class="card-body">
                    @include('components.crud-form')
                </div>
            </div>
        </div>

        {{-- Left: Table Listing --}}
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ $titlePage }}</h6>
                </div>
                <div class="card-body">
                    {{-- Search --}}
                    <form method="GET" action="{{ route($route . '.index') }}" class="mb-3">
                        <div class="input-group" style="max-width: 300px;">
                            <input type="text" name="search" class="form-control bg-light border-0 small"
                                placeholder="Cari ..." value="{{ $search ?? '' }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr class="text-center">
                                    {{-- <th>No</th> --}}
                                    @foreach ($grid as $column)
                                        <th>{{ $column['label'] }}</th>
                                    @endforeach
                                    <th>Bayar</th>
                                    <th>Aksi</th>
                                    <th>Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($items as $item)
                                    <tr
                                        class="{{ isset($editData) && $editData->{$primaryKey} == $item->{$primaryKey} ? 'table-warning' : '' }}">
                                        {{-- <td class="text-center">{{ $loop->iteration + $items->firstItem() - 1 }}</td> --}}
                                        @foreach ($grid as $column)
                                            @if ($column['type'] === 'text')
                                                <td class="{{ $column['class'] ?? 'text-center' }}">
                                                    {{ $item->{$column['field']} ?? '-' }}</td>
                                            @elseif ($column['type'] === 'icon')
                                                <td class="{{ $column['class'] ?? 'text-center' }}"><i
                                                        class="fas {{ $item->{$column['field']} }}"></i></td>
                                            @elseif ($column['type'] === 'badge')
                                                <td class="{{ $column['class'] ?? 'text-center' }}"><span
                                                        class="badge badge-primary">{{ $item->{$column['field']} }}</span>
                                                </td>
                                            @elseif ($column['type'] === 'date')
                                                <td class="{{ $column['class'] ?? 'text-center' }}">
                                                    {{ $item->{$column['field']} ? \Carbon\Carbon::parse($item->{$column['field']})->format('d/m/Y') : '-' }}
                                                </td>
                                            @elseif ($column['type'] === 'datetime')
                                                <td class="{{ $column['class'] ?? 'text-center' }}">
                                                    {{ $item->{$column['field']} ? \Carbon\Carbon::parse($item->{$column['field']})->format('d/m/Y H:i:s') : '-' }}
                                                </td>
                                            @elseif ($column['type'] === 'angka')
                                                <td class="{{ $column['class'] ?? 'text-center' }}">
                                                    @php
                                                        $value = $item->{$column['field']} ?? 0;
                                                        echo number_format($value, 2, ',', '.');
                                                    @endphp
                                                </td>
                                            @elseif ($column['type'] === 'rupiah')
                                                <td class="{{ $column['class'] ?? 'text-center' }}">
                                                    @php
                                                        $value = $item->{$column['field']} ?? 0;
                                                        echo 'Rp ' . number_format($value, 2, ',', '.');
                                                    @endphp
                                                </td>
                                            @else
                                                <td class="{{ $column['class'] ?? 'text-center' }}">
                                                    {{ $item->{$column['field']} ?? '-' }}</td>
                                            @endif
                                            </td>
                                        @endforeach
                                        <td class="text-center">
                                            <a class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#bayarModal"
                                                onclick="bayarModal({{ $item->{$primaryKey} }})">
                                                <i class="fas fa-hand-holding-usd"></i>
                                            </a>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route($route . '.index', ['edit' => $item->{$primaryKey}]) }}"
                                                class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route($route . '.destroy', $item->{$primaryKey}) }}"
                                                method="POST" class="d-inline form-delete">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                        <td class="text-center">
                                            <a class="btn btn-info btn-sm btn-detail" data-toggle="collapse"
                                                href="#collapseDetail{{ $item->{$primaryKey} }}" role="button"
                                                aria-expanded="false" data-id="{{ $item->{$primaryKey} }}"
                                                onclick="loadDetail({{ $item->{$primaryKey} }})">
                                                <i class="fas fa-info-circle"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    {{-- Collapse row: detail cicilan --}}
                                    <tr class="collapse" id="collapseDetail{{ $item->{$primaryKey} }}">
                                        <td colspan="{{ count($grid) + 3 }}">
                                            <div class="p-3" id="detailContent{{ $item->{$primaryKey} }}">
                                                <div class="text-center text-muted py-2">
                                                    <i class="fas fa-spinner fa-pulse"></i> Memuat...
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ count($grid) + 3 }}" class="text-center text-muted">
                                            Tidak ada data.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <small class="text-muted">
                            Menampilkan {{ $items->firstItem() ?? 0 }} - {{ $items->lastItem() ?? 0 }}
                            dari {{ $items->total() }} data
                        </small>
                        <div>
                            {{ $items->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Bayar -->
    <div class="modal fade" id="bayarModal" tabindex="-1" aria-labelledby="bayarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title fs-5" id="bayarModalLabel">Form Pembayaran Cicilan</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{-- Info cicilan --}}
                    <div class="alert alert-info mb-3">
                        <strong>No. Transaksi:</strong> <span id="infoNoTrans">-</span><br>
                        <strong>Pinjaman:</strong> <span id="infoPinjaman">-</span><br>
                        <strong>Sudah Dibayar:</strong> <span id="infoDibayar">-</span><br>
                        <strong>Sisa:</strong> <span id="infoSisa">-</span>
                    </div>

                    @include('components.crud-form', [
                        'form' => $bayarForm,
                        'action' => route('cicilan.bayar'),
                        'editData' => null,
                        'submitLabel' => 'Bayar',
                        'showCancel' => false,
                    ])
                </div>
            </div>
        </div>
    </div>

    <script>
        // ============================================================
        // Detail Cicilan — collapse table
        // ============================================================

        function loadDetail(id) {
            var $content = $('#detailContent' + id);

            // Cegah fetch ulang
            if ($content.data('loaded')) return;

            // Loading
            $content.html('<div class="text-center text-muted py-2">' +
                '<i class="fas fa-spinner fa-pulse"></i> Memuat...</div>');

            $.get('/api/cicilan/' + id + '/detail')
                .done(function(response) {
                    if (response.success && response.data.length > 0) {
                        $content.html(buildDetailTable(response.data));
                    } else {
                        $content.html('<div class="text-center text-muted py-2">Belum ada pembayaran.</div>');
                    }
                    $content.data('loaded', true);
                })
                .fail(function() {
                    $content.html('<div class="text-center text-danger py-2">Gagal memuat data.</div>');
                });
        }

        function buildDetailTable(data) {
            var html = '<div class="table-responsive">' +
                '<table class="table table-sm table-striped mb-0">' +
                '<thead class="thead-light"><tr>' +
                '<th class="text-center">No</th><th class="text-center">Tanggal Bayar</th><th class="text-center">Nominal</th><th class="text-center">Keterangan</th><th class="text-center">Aksi</th>' +
                '</tr></thead><tbody>';

            $.each(data, function(i, item) {
                html += '<tr>' +
                    '<td class="text-center">' + (i + 1) + '</td>' +
                    '<td class="text-center">' + (item.trcdTanggal || '-') + '</td>' +
                    '<td class="text-right">Rp ' + formatRupiah(item.trcdNominal) + '</td>' +
                    '<td class="text-center">' + (item.trcdKeterangan || '-') + '</td>' +
                    '<td class="text-center">' + '<button type="button" class="btn btn-danger btn-sm" onclick="delDetail(' + item.trcdId + ')">' +
                    '                <i class="fas fa-trash"></i>' +
                    '            </button>' +
                    '</td>' +
                    '</tr>';
            });

            html += '</tbody></table></div>';
            return html;
        }

        // ============================================================
        // Bayar Modal
        // ============================================================

        function bayarModal(id) {
            resetBayarForm();
            $('#bayarModal').modal('show');
            fetchCicilanData(id);
        }

        function resetBayarForm() {
            $('#trcdHeadId').val('');
            $('#trcdTanggal').val('{{ date('Y-m-d') }}');
            $('#trcdNominal, #trcdNominal_display').val('');
            $('#trcdKeterangan').val('');
            $('#infoNoTrans, #infoPinjaman, #infoDibayar, #infoSisa').text('-');
            $('#trcdNominal_display').attr('placeholder', 'Memuat data...');
        }

        function fetchCicilanData(id) {
            $.get('/api/cicilan/' + id)
                .done(function(response) {
                    if (response.success) {
                        populateBayarForm(response.data);
                    } else {
                        alert('Gagal mengambil data cicilan.');
                    }
                })
                .fail(function() {
                    alert('Terjadi kesalahan saat mengambil data cicilan.');
                });
        }

        function populateBayarForm(d) {
            $('#trcdHeadId').val(d.trcId);
            $('#trcdNominal').val(d.trcPokokBayar);
            $('#trcdNominal_display').val(formatRupiah(d.trcPokokBayar));
            $('#trcdNominal_display').attr('placeholder', 'Masukkan nominal pembayaran');

            $('#infoNoTrans').text(d.trcNoTrans);
            $('#infoPinjaman').text('Rp ' + formatRupiah(d.trcNominalPokok));
            $('#infoDibayar').text('Rp ' + formatRupiah(d.totalDibayar));
            $('#infoSisa').text('Rp ' + formatRupiah(d.sisa));
        }

        // ============================================================
        // Utility
        // ============================================================

        function formatRupiah(angka) {
            return parseFloat(angka).toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function delDetail(id) {
            swalConfirm({
                title: 'Hapus pembayaran?',
                text: 'Data pembayaran yang dihapus tidak dapat dikembalikan.',
                icon: 'warning',
                confirmText: 'Ya, Hapus',
                confirmColor: '#e74a3b',
                cancelText: 'Batal',
            }).then(function(result) {
                if (!result.isConfirmed) return;

                $.get('/api/cicilan/delete/' + id)
                    .done(function(response) {
                        if (response.success) {
                            swalSuccess('Berhasil!', response.message);
                            setTimeout(function() { location.reload(); }, 1500);
                        } else {
                            swalError('Gagal!', response.message);
                        }
                    })
                    .fail(function() {
                        swalError('Gagal!', 'Terjadi kesalahan saat menghapus pembayaran.');
                    });
            });
        }
    </script>
@endsection
