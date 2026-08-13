@extends('layouts.app')

@section('content')
    <div class="text-center">
        <div class="error mx-auto" data-text="403">403</div>
        <p class="lead text-gray-800 mb-4">Akses Ditolak</p>
        <p class="text-gray-500 mb-4">Anda tidak memiliki izin untuk mengakses halaman ini.</p>
        <a href="{{ route('dashboard') }}" class="btn btn-primary">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i>
            Kembali ke Dashboard
        </a>
    </div>
@endsection
