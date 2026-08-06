@extends('layouts.app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Menu</h1>
        <a href="{{ route('menus.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Tambah Menu</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('menus.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="mNama">Nama Menu <span class="text-danger">*</span></label>
                    <input type="text" name="mNama" id="mNama"
                           class="form-control @error('mNama') is-invalid @enderror"
                           value="{{ old('mNama') }}" placeholder="Masukkan nama menu" required>
                    @error('mNama')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </form>
        </div>
    </div>
@endsection
