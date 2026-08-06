@extends('layouts.app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Role</h1>
        <a href="{{ route('roles.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Role</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('roles.update', $role->rId) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="rNama">Nama Role <span class="text-danger">*</span></label>
                    <input type="text" name="rNama" id="rNama"
                           class="form-control @error('rNama') is-invalid @enderror"
                           value="{{ old('rNama', $role->rNama) }}" placeholder="Masukkan nama role" required>
                    @error('rNama')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update
                </button>
            </form>
        </div>
    </div>
@endsection
