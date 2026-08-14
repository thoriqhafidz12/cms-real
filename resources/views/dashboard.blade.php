@extends('layouts.app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <div class="d-flex align-items-center">
            <span class="text-muted small mr-2">Tahun</span>
            <select id="filterTahun" class="form-control form-control-sm" style="width: auto;"
                onchange="window.location.href='?tahun=' + this.value">
                @foreach ($availableYears as $y)
                    <option value="{{ $y }}" {{ $y == $tahun ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- ── STAT CARDS ──────────────────────────────────────── --}}
    <div class="row">
        {{-- Penerimaan Bulan Ini --}}
        <div class="col-6 col-md-6 col-xl-3 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Penerimaan {{ date('M') }} {{ date('Y') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($terimaBulanIni, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-down fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pengeluaran Bulan Ini --}}
        <div class="col-6 col-md-6 col-xl-3 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Pengeluaran {{ date('M') }} {{ date('Y') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($keluarBulanIni, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-up fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Saldo Bulan Ini --}}
        <div class="col-6 col-md-6 col-xl-3 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Saldo {{ date('M') }} {{ date('Y') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($saldoBulanIni, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-balance-scale fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Saldo Sekarang (all-time) --}}
        <div class="col-6 col-md-6 col-xl-3 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Saldo</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($saldoSekarang, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-coins fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── WELCOME ─────────────────────────────────────────── --}}
    <div class="row">
        <div class="col-12 col-xl-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Selamat Datang</h6>
                </div>
                <div class="card-body">
                    <p>Halo, <strong>{{ Auth::user()->name }}</strong>! Anda login sebagai
                        <strong>{{ Auth::user()->roleRelation?->rNama ?? '-' }}</strong>.
                    </p>
                    <p class="mb-0">Email: {{ Auth::user()->email }}</p>
                </div>
            </div>
        </div>
        {{-- Ringkasan Tahunan --}}
        <div class="col-12 col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-calendar-alt mr-1"></i>Ringkasan Tahun {{ $tahun }}
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-success"><strong>Total Penerimaan</strong></td>
                            <td class="text-right">Rp {{ number_format(array_sum($chartTerima), 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="text-danger"><strong>Total Pengeluaran</strong></td>
                            <td class="text-right">Rp {{ number_format(array_sum($chartKeluar), 0, ',', '.') }}</td>
                        </tr>
                        <tr class="border-top">
                            <td><strong>Surplus / Defisit</strong></td>
                            <td
                                class="text-right font-weight-bold {{ array_sum($chartSaldo) >= 0 ? 'text-success' : 'text-danger' }}">
                                Rp {{ number_format(array_sum($chartSaldo), 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Saldo Sekarang</strong></td>
                            <td class="text-right font-weight-bold text-warning">
                                Rp {{ number_format($saldoSekarang, 0, ',', '.') }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ── INFO CARDS ───────────────────────────────────────── --}}
    {{-- <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Users</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUsers }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-users fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Roles</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalRoles }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-user-tag fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Menu</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalMenus }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-bars fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    {{-- ── CHARTS ──────────────────────────────────────────── --}}
    <div class="row">
        {{-- Bar Chart: Penerimaan vs Pengeluaran per Bulan --}}
        <div class="col-xl-6 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar mr-1"></i>Penerimaan vs Pengeluaran — Tahun {{ $tahun }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar" style="height: 340px;">
                        <canvas id="chartPenerimaanVsPengeluaran"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Line Chart: Saldo per Bulan --}}
        <div class="col-xl-6 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-line mr-1"></i>Tren Saldo Bulanan — Tahun {{ $tahun }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height: 340px;">
                        <canvas id="chartSaldo"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Chart.js -->
    <script src="{{ url('/') }}/assets/vendor/chart.js/Chart.min.js"></script>
    <script src="{{ url('/') }}/assets/js/demo/chart-bar-demo.js"></script>
    <script src="{{ url('/') }}/assets/js/demo/chart-area-demo.js"></script>
    <script src="{{ url('/') }}/assets/js/demo/chart-pie-demo.js"></script>
    <script>
        // ── Data dari server ────────────────────────────────────
        var chartLabels = @json($chartLabels);
        var chartTerima = @json($chartTerima);
        var chartKeluar = @json($chartKeluar);
        var chartSaldo = @json($chartSaldo);

        // ── BAR CHART: Penerimaan vs Pengeluaran ────────────────
        var ctxBar = document.getElementById('chartPenerimaanVsPengeluaran');
        if (ctxBar) {
            new Chart(ctxBar.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Penerimaan',
                        backgroundColor: '#1cc88a',
                        hoverBackgroundColor: '#17a673',
                        borderColor: '#1cc88a',
                        data: chartTerima
                    }, {
                        label: 'Pengeluaran',
                        backgroundColor: '#e74a3b',
                        hoverBackgroundColor: '#be2617',
                        borderColor: '#e74a3b',
                        data: chartKeluar
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            left: 10,
                            right: 25,
                            top: 25,
                            bottom: 0
                        }
                    },
                    scales: {
                        xAxes: [{
                            gridLines: {
                                display: false,
                                drawBorder: false
                            },
                            maxBarThickness: 25
                        }],
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                padding: 10,
                                callback: function(v) {
                                    return 'Rp ' + number_format(v);
                                }
                            },
                            gridLines: {
                                color: 'rgb(234, 236, 244)',
                                zeroLineColor: 'rgb(234, 236, 244)',
                                drawBorder: false,
                                borderDash: [2],
                                zeroLineBorderDash: [2]
                            }
                        }]
                    },
                    legend: {
                        display: false
                    },
                    tooltips: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgb(255,255,255)',
                        titleFontColor: '#6e707e',
                        titleFontSize: 14,
                        titleMarginBottom: 10,
                        bodyFontColor: '#858796',
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        caretPadding: 10,
                        callbacks: {
                            label: function(item, chart) {
                                return chart.datasets[item.datasetIndex].label + ': Rp ' + number_format(item
                                    .yLabel);
                            }
                        }
                    }
                }
            });
        }

        // ── LINE CHART: Tren Saldo Bulanan ──────────────────────
        var ctxLine = document.getElementById('chartSaldo');
        if (ctxLine) {
            new Chart(ctxLine.getContext('2d'), {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Saldo',
                        lineTension: 0.3,
                        backgroundColor: 'rgba(28, 200, 138, 0.05)',
                        borderColor: '#1cc88a',
                        pointRadius: 3,
                        pointBackgroundColor: '#1cc88a',
                        pointBorderColor: '#1cc88a',
                        pointHoverRadius: 3,
                        pointHoverBackgroundColor: '#1cc88a',
                        pointHoverBorderColor: '#1cc88a',
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        data: chartSaldo
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            left: 10,
                            right: 25,
                            top: 25,
                            bottom: 0
                        }
                    },
                    scales: {
                        xAxes: [{
                            gridLines: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                maxTicksLimit: 12
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                padding: 10,
                                callback: function(v) {
                                    return 'Rp ' + number_format(v);
                                }
                            },
                            gridLines: {
                                color: 'rgb(234, 236, 244)',
                                zeroLineColor: 'rgb(234, 236, 244)',
                                drawBorder: false,
                                borderDash: [2],
                                zeroLineBorderDash: [2]
                            }
                        }]
                    },
                    legend: {
                        display: false
                    },
                    tooltips: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgb(255,255,255)',
                        titleFontColor: '#6e707e',
                        titleFontSize: 14,
                        titleMarginBottom: 10,
                        bodyFontColor: '#858796',
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        caretPadding: 10,
                        callbacks: {
                            label: function(item, chart) {
                                return chart.datasets[item.datasetIndex].label + ': Rp ' + number_format(item
                                    .yLabel);
                            }
                        }
                    }
                }
            });
        }
    </script>
@endpush
