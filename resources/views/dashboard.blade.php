@extends('layouts.app')

@push('styles')
    <style>
        :root {
            /* Palet seri (slot tetap, tervalidasi) + tint untuk badge */
            --series-1: #2a78d6;        /* biru — Anggota / Penerimaan / Laki-laki */
            --series-2: #eb6834;        /* oranye — Pengeluaran */
            --series-3: #e87ba4;        /* magenta — Perempuan */
            --neutral: #c3c2b7;         /* abu netral — Lainnya / tidak diisi */
            --series-1-tint: rgba(42, 120, 214, 0.12);
            --series-1-strong: #1c5cab;
            --series-2-tint: rgba(235, 104, 52, 0.14);
            --series-2-strong: #a34b16;
            --ink-secondary: #52514e;
            --ink-muted: #898781;
            --gridline: #e1e0d9;
        }

        .viz-container {
            position: relative;
            height: 300px;
        }

        .viz-empty {
            height: 300px;
        }

        .avatar-initials {
            width: 40px;
            height: 40px;
            flex: 0 0 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: 700;
            color: #fff;
            background: var(--series-1);
        }

        .badge-tipe-penerimaan {
            background: var(--series-1-tint);
            color: var(--series-1-strong);
        }

        .badge-tipe-pengeluaran {
            background: var(--series-2-tint);
            color: var(--series-2-strong);
        }
    </style>
@endpush

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Dashboard</h1>
            <p class="mb-0 small text-gray-500">Selamat datang, <strong>{{ Auth::user()->name }}</strong> — {{ $hariIni }}</p>
        </div>
        <div class="d-none d-sm-block small text-gray-500">
            Periode laporan: <span class="font-weight-bold text-gray-800">Tahun {{ $tahunIni }}</span>
        </div>
    </div>

    {{-- KPI Baris 1 --}}
    <div class="row">
        {{-- Total Anggota --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Anggota</div>
                            <div class="h5 mb-1 font-weight-bold text-gray-800">{{ $totalAnggotaText }}</div>
                            <div class="small text-gray-500">
                                <i class="fas fa-user-plus fa-fw"></i> {{ $anggotaBaruBulanIni }} baru bulan ini
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pendapatan Tahun Ini --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Pendapatan</div>
                            <div class="h5 mb-1 font-weight-bold text-gray-800">{{ $pendapatanText }}</div>
                            <div class="small text-gray-500">Tahun berjalan</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Biaya Tahun Ini --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Biaya</div>
                            <div class="h5 mb-1 font-weight-bold text-gray-800">{{ $biayaText }}</div>
                            <div class="small text-gray-500">Tahun berjalan</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-check-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Laba Bersih --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-{{ $labaPositif ? 'success' : 'danger' }} shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-{{ $labaPositif ? 'success' : 'danger' }} text-uppercase mb-1">Laba Bersih</div>
                            <div class="h5 mb-1 font-weight-bold text-gray-800">{{ $labaText }}</div>
                            <div class="small text-gray-500">Tahun berjalan</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Baris 2: Pertumbuhan Anggota + Komposisi Jenis Kelamin --}}
    <div class="row">
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card shadow mb-4 h-100">
                <div class="card-header py-3 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Pertumbuhan Anggota</h6>
                    <span class="small text-gray-500">12 bulan terakhir</span>
                </div>
                <div class="card-body">
                    @if ($hasTrendAnggota)
                        <div class="viz-container">
                            <canvas id="chartPertumbuhanAnggota"></canvas>
                        </div>
                    @else
                        <div class="viz-empty d-flex flex-column align-items-center justify-content-center text-center text-gray-400">
                            <i class="fas fa-chart-area fa-2x mb-2"></i>
                            <div class="small">Belum ada data anggota untuk ditampilkan</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card shadow mb-4 h-100">
                <div class="card-header py-3 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Komposisi Jenis Kelamin</h6>
                    <span class="small text-gray-500">Total: {{ $totalAnggotaText }}</span>
                </div>
                <div class="card-body">
                    @if ($hasDataGender)
                        <div class="viz-container">
                            <canvas id="chartGender"></canvas>
                        </div>
                    @else
                        <div class="viz-empty d-flex flex-column align-items-center justify-content-center text-center text-gray-400">
                            <i class="fas fa-venus-mars fa-2x mb-2"></i>
                            <div class="small">Belum ada data anggota untuk ditampilkan</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Baris 3: Penerimaan & Pengeluaran + Anggota Terbaru --}}
    <div class="row">
        <div class="col-xl-7 col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Penerimaan &amp; Pengeluaran</h6>
                    <span class="small text-gray-500">12 bulan terakhir</span>
                </div>
                <div class="card-body">
                    @if ($hasDataKeuangan)
                        <div class="viz-container">
                            <canvas id="chartKasBulanan"></canvas>
                        </div>
                    @else
                        <div class="viz-empty d-flex flex-column align-items-center justify-content-center text-center text-gray-400">
                            <i class="fas fa-chart-bar fa-2x mb-2"></i>
                            <div class="small">Belum ada transaksi penerimaan/pengeluaran</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-5 col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Anggota Terbaru</h6>
                    <a href="{{ route('ms-anggota.index') }}" class="small text-primary">Lihat semua <i class="fas fa-arrow-right fa-sm"></i></a>
                </div>
                <div class="card-body py-2">
                    @forelse ($anggotaTerbaru as $anggota)
                        <div class="d-flex align-items-center py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="avatar-initials">{{ $anggota['inisial'] }}</div>
                            <div class="flex-grow-1 min-width-0 pl-3">
                                <div class="font-weight-bold text-gray-800 text-truncate">{{ $anggota['nama'] }}</div>
                                <div class="small text-gray-500">{{ $anggota['kode'] }}</div>
                            </div>
                            <div class="small text-gray-500 text-nowrap">{{ $anggota['tanggal'] }}</div>
                        </div>
                    @empty
                        <div class="text-center text-gray-400 py-4">
                            <i class="fas fa-user-slash fa-2x mb-2"></i>
                            <div class="small">Belum ada anggota terdaftar</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Baris 4: Aktivitas Terbaru --}}
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Aktivitas Terbaru</h6>
                    <span class="small text-gray-500">Penerimaan &amp; pengeluaran terakhir</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr class="small text-gray-500 text-uppercase">
                                    <th class="border-0 pl-4">Tanggal</th>
                                    <th class="border-0">Jenis</th>
                                    <th class="border-0">Deskripsi</th>
                                    <th class="border-0 text-right pr-4">Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($aktivitas as $item)
                                    <tr>
                                        <td class="text-nowrap small pl-4">{{ $item['tanggal'] }}</td>
                                        <td>
                                            <span class="badge badge-tipe-{{ $item['tipe'] === 'Penerimaan' ? 'penerimaan' : 'pengeluaran' }}">
                                                <i class="fas fa-{{ $item['tipe'] === 'Penerimaan' ? 'arrow-down' : 'arrow-up' }} fa-xs mr-1"></i>{{ $item['tipe'] }}
                                            </span>
                                        </td>
                                        <td class="small text-truncate" style="max-width: 380px;">
                                            {{ $item['deskripsi'] }}
                                            <span class="text-gray-400">· {{ $item['no'] }}</span>
                                        </td>
                                        <td class="text-nowrap text-right small font-weight-bold text-gray-800 pr-4">
                                            {{ $item['tipe'] === 'Penerimaan' ? '+' : '−' }}Rp {{ number_format($item['nilai'], 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-gray-400 py-4">
                                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                            <span class="small">Belum ada transaksi</span>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor/chart.js/Chart.bundle.min.js') }}"></script>
    <script>
        $(function () {
            var css = getComputedStyle(document.documentElement);
            var series1 = css.getPropertyValue('--series-1').trim() || '#2a78d6';
            var series2 = css.getPropertyValue('--series-2').trim() || '#eb6834';
            var series3 = css.getPropertyValue('--series-3').trim() || '#e87ba4';
            var neutral = css.getPropertyValue('--neutral').trim() || '#c3c2b7';
            var inkSecondary = css.getPropertyValue('--ink-secondary').trim() || '#52514e';
            var inkMuted = css.getPropertyValue('--ink-muted').trim() || '#898781';
            var gridline = css.getPropertyValue('--gridline').trim() || '#e1e0d9';

            Chart.defaults.global.defaultFontFamily = "'Nunito', -apple-system, 'Segoe UI', sans-serif";
            Chart.defaults.global.defaultFontColor = inkMuted;
            Chart.defaults.global.defaultFontSize = 11;

            var hexToRgba = function (hex, alpha) {
                var r = parseInt(hex.slice(1, 3), 16);
                var g = parseInt(hex.slice(3, 5), 16);
                var b = parseInt(hex.slice(5, 7), 16);
                return 'rgba(' + r + ',' + g + ',' + b + ',' + alpha + ')';
            };

            var fmtAngka = function (v) {
                return Number(v).toLocaleString('id-ID', { maximumFractionDigits: 0 });
            };

            var fmtRupiah = function (v) {
                return 'Rp ' + fmtAngka(v);
            };

            var fmtRupiahSingkat = function (v) {
                if (Math.abs(v) >= 1e9) return 'Rp ' + (v / 1e9).toLocaleString('id-ID', { maximumFractionDigits: 1 }) + ' M';
                if (Math.abs(v) >= 1e6) return 'Rp ' + (v / 1e6).toLocaleString('id-ID', { maximumFractionDigits: 1 }) + ' jt';
                if (Math.abs(v) >= 1e3) return 'Rp ' + Math.round(v / 1e3) + ' rb';
                return 'Rp ' + fmtAngka(v);
            };

            var tooltipDasar = {
                backgroundColor: 'rgba(11, 11, 11, 0.92)',
                titleFontSize: 12,
                bodyFontSize: 13,
                xPadding: 12,
                yPadding: 10,
                cornerRadius: 4,
                displayColors: false
            };

            var skalaX = {
                gridLines: { display: false },
                ticks: { fontColor: inkMuted }
            };

            var skalaY = {
                gridLines: { color: gridline, drawBorder: false, zeroLineColor: gridline },
                ticks: {
                    fontColor: inkMuted,
                    beginAtZero: true,
                    maxTicksLimit: 6,
                    callback: function (v) {
                        if (Math.round(v) === v) return fmtAngka(v);
                        return Number(v).toLocaleString('id-ID', { maximumFractionDigits: 1 });
                    }
                }
            };

            @if ($hasTrendAnggota)
                // Satu seri — judul kartu sudah menamai serinya, tanpa kotak legenda.
                new Chart(document.getElementById('chartPertumbuhanAnggota'), {
                    type: 'line',
                    data: {
                        labels: @json($bulanLabels),
                        datasets: [{
                            label: 'Total Anggota',
                            data: @json($pertumbuhanAnggota),
                            fill: true,
                            lineTension: 0.2,
                            borderColor: series1,
                            borderWidth: 2,
                            backgroundColor: hexToRgba(series1, 0.10),
                            pointBackgroundColor: series1,
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 8
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        legend: { display: false },
                        layout: { padding: { top: 8, right: 8, bottom: 4, left: 4 } },
                        tooltips: Object.assign({}, tooltipDasar, {
                            callbacks: {
                                label: function (item) {
                                    return ' Total: ' + fmtAngka(item.value) + ' anggota';
                                }
                            }
                        }),
                        scales: { xAxes: [skalaX], yAxes: [skalaY] }
                    }
                });
            @endif

            @if ($hasDataKeuangan)
                // Dua seri — legenda wajib hadir; identitas juga ditandai teks di tooltip.
                new Chart(document.getElementById('chartKasBulanan'), {
                    type: 'bar',
                    data: {
                        labels: @json($bulanLabels),
                        datasets: [
                            {
                                label: 'Penerimaan',
                                data: @json($terimaBulanan),
                                backgroundColor: series1,
                                hoverBackgroundColor: series1,
                                borderColor: '#ffffff',
                                borderWidth: 2
                            },
                            {
                                label: 'Pengeluaran',
                                data: @json($keluarBulanan),
                                backgroundColor: series2,
                                hoverBackgroundColor: series2,
                                borderColor: '#ffffff',
                                borderWidth: 2
                            }
                        ]
                    },
                    options: {
                        maintainAspectRatio: false,
                        legend: {
                            position: 'top',
                            labels: { usePointStyle: true, boxWidth: 8, fontColor: inkSecondary, padding: 16 }
                        },
                        layout: { padding: { top: 4, right: 8, bottom: 4, left: 4 } },
                        tooltips: Object.assign({}, tooltipDasar, {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function (item, data) {
                                    return ' ' + data.datasets[item.datasetIndex].label + ': ' + fmtRupiah(item.value);
                                }
                            }
                        }),
                        scales: {
                            xAxes: [skalaX],
                            yAxes: [Object.assign({}, skalaY, {
                                ticks: {
                                    fontColor: inkMuted,
                                    beginAtZero: true,
                                    maxTicksLimit: 6,
                                    callback: function (v) { return fmtRupiahSingkat(v); }
                                }
                            })]
                        }
                    }
                });
            @endif

            @if ($hasDataGender)
                // Legenda sekaligus label langsung (jumlah + persen) — relief untuk segmen kontras rendah.
                new Chart(document.getElementById('chartGender'), {
                    type: 'doughnut',
                    data: {
                        labels: @json($genderData['labels']),
                        datasets: [{
                            data: @json($genderData['values']),
                            backgroundColor: [series1, series3, neutral],
                            hoverBackgroundColor: [series1, series3, neutral],
                            borderColor: '#ffffff',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        cutoutPercentage: 62,
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                boxWidth: 8,
                                fontColor: inkSecondary,
                                padding: 12,
                                generateLabels: function (chart) {
                                    var data = chart.data;
                                    var nilai = data.datasets[0].data;
                                    var total = nilai.reduce(function (a, b) { return a + b; }, 0);

                                    return data.labels.map(function (label, i) {
                                        var persen = total ? Math.round(nilai[i] / total * 100) : 0;
                                        return {
                                            text: label + ' — ' + fmtAngka(nilai[i]) + ' (' + persen + '%)',
                                            fillStyle: data.datasets[0].backgroundColor[i],
                                            strokeStyle: '#ffffff',
                                            lineWidth: 1,
                                            hidden: false,
                                            index: i
                                        };
                                    });
                                }
                            }
                        },
                        tooltips: Object.assign({}, tooltipDasar, {
                            callbacks: {
                                label: function (item, data) {
                                    var nilai = data.datasets[0].data;
                                    var total = nilai.reduce(function (a, b) { return a + b; }, 0);
                                    var persen = total ? Math.round(nilai[item.index] / total * 100) : 0;
                                    return ' ' + data.labels[item.index] + ': ' + fmtAngka(nilai[item.index]) + ' anggota (' + persen + '%)';
                                }
                            }
                        })
                    }
                });
            @endif
        });
    </script>
@endpush
