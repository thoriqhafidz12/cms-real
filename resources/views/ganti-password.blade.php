@extends('layouts.app')

@section('content')
<div class="row align-items-center justify-content-center">
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-key"></i> Ganti Password</h6>
            </div>
            <div class="card-body">
                @include('components.crud-form')
            </div>
        </div>
    </div>
</div>
@endsection