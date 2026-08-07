@extends('layouts.app')

@section('content')
    <div class="row">
        {{-- Left: Table Listing --}}
        <div class="col-md-8">
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
                                    <th>Aksi</th>
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
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ count($grid) + 2 }}" class="text-center text-muted">
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

        {{-- Right: Form --}}
        <div class="col-md-4">
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
    </div>
@endsection
