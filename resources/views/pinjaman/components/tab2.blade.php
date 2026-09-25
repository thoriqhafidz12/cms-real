<div class="row">
    {{-- Left: Table Listing (pengajuan Pending) --}}
    <div class="col-md-8 order-2 order-md-1">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Persetujuan Pengajuan</h6>
            </div>
            <div class="card-body">
                {{-- Search --}}
                <form method="GET" action="{{ route($route . '.index') }}" class="mb-3">
                    <div class="input-group" style="max-width: 300px;">
                        <input type="text" name="search_persetujuan" class="form-control bg-light border-0 small"
                            placeholder="Cari ..." value="{{ $searchPersetujuan ?? '' }}">
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
                                @foreach ($gridPersetujuan as $column)
                                    <th>{{ $column['label'] }}</th>
                                @endforeach
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($itemsPersetujuan as $item)
                                <tr
                                    class="{{ isset($dataPersetujuan) && $dataPersetujuan->{$primaryKey} == $item->{$primaryKey} ? 'table-warning' : '' }}">
                                    @foreach ($gridPersetujuan as $column)
                                        @if ($column['type'] === 'text')
                                            <td class="{{ $column['class'] ?? 'text-center' }}"
                                                data-label="{{ $column['label'] }}">
                                                {{ $item->{$column['field']} ?? '-' }}</td>
                                        @elseif ($column['type'] === 'icon')
                                            <td class="{{ $column['class'] ?? 'text-center' }}"
                                                data-label="{{ $column['label'] }}"><i
                                                    class="fas {{ $item->{$column['field']} }}"></i>
                                            </td>
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
                                    @if ($item->tpStatus == '0')
                                        <td class="text-center" data-label="Status">
                                            <badge class="btn-secondary btn-sm"><i class="fas fa-clock"></i></badge>
                                        </td>
                                    @elseif ($item->tpStatus == '2')
                                        <td class="text-center" data-label="Status">
                                            <badge class="btn-danger btn-sm"><i class="fas fa-times"></i></badge>
                                        </td>
                                    @elseif ($item->tpStatus == '3')
                                        <td class="text-center" data-label="Status">
                                            <badge class="btn-secondary btn-sm"><i class="fas fa-ban"></i></badge>
                                        </td>
                                    @else
                                        <td class="text-center" data-label="Status">
                                            <badge class="btn-success btn-sm"><i class="fas fa-check"></i></badge>
                                        </td>
                                    @endif
                                    <td class="text-center" data-label="Aksi">
                                        <a href="{{ route($route . '.index', ['setujui' => $item->{$primaryKey}]) }}"
                                            class="btn btn-warning btn-sm" title="Proses Persetujuan">
                                            <i class="fas fa-info"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ count($gridPersetujuan) + 2 }}" class="text-center text-muted">
                                        Tidak ada pengajuan yang menunggu persetujuan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <small class="text-muted">
                        Menampilkan {{ $itemsPersetujuan->firstItem() ?? 0 }} -
                        {{ $itemsPersetujuan->lastItem() ?? 0 }}
                        dari {{ $itemsPersetujuan->total() }} data
                    </small>
                    <div>
                        {{ $itemsPersetujuan->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Right: Form Persetujuan --}}
    <div class="col-md-4 order-1 order-md-2">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Form Persetujuan</h6>
            </div>
            <div class="card-body">
                @if (isset($dataPersetujuan) && $dataPersetujuan)
                    <div class="mb-3 p-3 bg-light border rounded">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-muted">Kode</td>
                                <td>: {{ $dataPersetujuan->tpKode }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Anggota</td>
                                <td>: {{ $dataPersetujuan->tpAnggotaNama }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Jumlah Pinjam</td>
                                <td>: Rp
                                    {{ number_format($dataPersetujuan->tpJumlahPinjam, 2, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Bunga</td>
                                <td>: {{ $dataPersetujuan->tpBunga }}%</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tenor</td>
                                <td>: {{ $dataPersetujuan->tpJumlahAngsuranBulan }} bulan</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Jaminan</td>
                                <td>: {{ $dataPersetujuan->tpJaminanNama }}</td>
                            </tr>
                        </table>
                    </div>

                    @include('components.crud-form', [
                        'form' => $formPersetujuan,
                        'route' => $route,
                        'editData' => $dataPersetujuan,
                        'primaryKey' => $primaryKey,
                        'action' => route($route . '.persetujuan', $dataPersetujuan->{$primaryKey}),
                        'submitLabel' => 'Simpan',
                    ])
                @else
                    <p class="text-muted mb-0">
                        Klik tombol <i class="fas fa-info text-warning"></i> pada tabel
                        untuk memproses persetujuan pengajuan.
                    </p>
                    <p class="text-muted mb-0">
                        <i class="fas fa-check text-success"></i> Disetujui
                    </p>
                    <p class="text-muted mb-0">
                        <i class="fas fa-times text-danger"></i> Ditolak
                    </p>
                    <p class="text-muted mb-0">
                        <i class="fas fa-ban text-secondary"></i> Dibatalkan
                    </p>
                    <p class="text-muted mb-0">
                        <i class="fas fa-clock text-secondary"></i> Pending
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>
