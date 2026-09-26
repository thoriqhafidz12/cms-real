@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">

                <div class="card-body">
                    <ul class="nav nav-tabs mb-4">
                        <li class="nav-item"><a href="#basic-tab1" class="nav-link active" data-toggle="tab"><i class="fas fa-file-alt"></i> Pengajuan</a></li>
                        <li class="nav-item"><a href="#basic-tab2" class="nav-link" data-toggle="tab"><i class="fas fa-handshake"></i> Persetujuan  @if($countPersetujuan > 0) <span class="badge badge-pill badge-danger">{{ $countPersetujuan }}</span> @endif</a></li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="basic-tab1">
                            @include('pinjaman.components.tab1')
                        </div>

                        <div class="tab-pane fade" id="basic-tab2">
                            @include('pinjaman.components.tab2')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            var storageKey = 'pengajuan-active-tab';

            // Restore tab aktif setelah reload (form submit, edit, setujui, pagination, redirect)
            var savedTab = localStorage.getItem(storageKey);
            if (savedTab) {
                var $tab = $('.nav-tabs a[data-toggle="tab"][href="' + savedTab + '"]');
                if ($tab.length) {
                    $tab.tab('show');
                }
            }

            // Simpan tab aktif setiap kali pindah tab
            $('.nav-tabs a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                localStorage.setItem(storageKey, $(e.target).attr('href'));
            });
        });
    </script>
@endpush