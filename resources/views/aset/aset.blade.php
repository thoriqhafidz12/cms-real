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
                        <table class="table table-bordered table-mobile-card" width="100%" cellspacing="0">
                            <thead>
                                <tr class="text-center">
                                    {{-- <th>No</th> --}}
                                    @foreach ($grid as $column)
                                        <th>{{ $column['label'] }}</th>
                                    @endforeach
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
                                                <td class="{{ $column['class'] ?? 'text-center' }}"
                                                    data-label="{{ $column['label'] }}">
                                                    {{ $item->{$column['field']} ?? '-' }}</td>
                                            @elseif ($column['type'] === 'icon')
                                                <td class="{{ $column['class'] ?? 'text-center' }}"
                                                    data-label="{{ $column['label'] }}"><i
                                                        class="fas {{ $item->{$column['field']} }}"></i></td>
                                            @elseif ($column['type'] === 'badge')
                                                <td class="{{ $column['class'] ?? 'text-center' }}"
                                                    data-label="{{ $column['label'] }}"><span
                                                        class="badge badge-primary">{{ $item->{$column['field']} }}</span>
                                                </td>
                                            @elseif ($column['type'] === 'date')
                                                <td class="{{ $column['class'] ?? 'text-center' }}"
                                                    data-label="{{ $column['label'] }}">
                                                    {{ $item->{$column['field']} ? \Carbon\Carbon::parse($item->{$column['field']})->format('d/m/Y') : '-' }}
                                                </td>
                                            @elseif ($column['type'] === 'datetime')
                                                <td class="{{ $column['class'] ?? 'text-center' }}"
                                                    data-label="{{ $column['label'] }}">
                                                    {{ $item->{$column['field']} ? \Carbon\Carbon::parse($item->{$column['field']})->format('d/m/Y H:i:s') : '-' }}
                                                </td>
                                            @elseif ($column['type'] === 'angka')
                                                <td class="{{ $column['class'] ?? 'text-center' }}"
                                                    data-label="{{ $column['label'] }}">
                                                    @php
                                                        $value = $item->{$column['field']} ?? 0;
                                                        echo number_format($value, 2, ',', '.');
                                                    @endphp
                                                </td>
                                            @elseif ($column['type'] === 'rupiah')
                                                <td class="{{ $column['class'] ?? 'text-center' }}"
                                                    data-label="{{ $column['label'] }}">
                                                    @php
                                                        $value = $item->{$column['field']} ?? 0;
                                                        echo 'Rp ' . number_format($value, 2, ',', '.');
                                                    @endphp
                                                </td>
                                            @else
                                                <td class="{{ $column['class'] ?? 'text-center' }}"
                                                    data-label="{{ $column['label'] }}">
                                                    {{ $item->{$column['field']} ?? '-' }}</td>
                                            @endif
                                            </td>
                                        @endforeach
                                        <td class="text-center" data-label="Aksi">
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
                                        <td class="text-center" data-label="Detail">
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

    <script>
        // ============================================================
        // Detail Data — collapse table
        // ============================================================

        function loadDetail(id) {
            var $content = $('#detailContent' + id);

            // Cegah fetch ulang
            if ($content.data('loaded')) return;

            // Loading
            $content.html('<div class="text-center text-muted py-2">' +
                '<i class="fas fa-spinner fa-pulse"></i> Memuat...</div>');

            $.get('{{ url($routeDetail) }}/' + id)
                .done(function(response) {
                    $content.html(buildDetailTable(response.detail));
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
                '<th class="text-center">Penyusutan Pertahun</th><th class="text-center">Penyusutan Tahun Berjalan</th><th class="text-center">Sisa Nilai Penyusutan</th><th class="text-center">Sudah Dipakai</th><th class="text-center">Sisa Layak Pakai</th><th class="text-center">Uang Perbulan Dikeluarkan</th>' +
                '</tr></thead><tbody>';

            if (data.length === 0) {
                html += '<tr><td colspan="7" class="text-center text-muted">Belum ada data.</td></tr>';
            } else {
                html += '<tr>' +
                    '<td class="text-right">Rp ' + formatRupiah(data.penyusutanPertahun) + '</td>' +
                    '<td class="text-right">Rp ' + formatRupiah(data.penyusutanTahunBerjalan) + '</td>' +
                    '<td class="text-right">Rp ' + formatRupiah(data.sisaNilaiPenyusutan) + '</td>' +
                    '<td class="text-center"> ' + data.sudahDipakai + ' Tahun</td>' +
                    '<td class="text-center"> ' + data.sisaLayakPakai + ' Tahun</td>' +
                    '<td class="text-right">Rp ' + formatRupiah(data.uangPerbulan) + '</td>' +
                    '</tr>';
            }
            html += '</tbody></table></div>';
            return html;
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
    </script>
@endsection
