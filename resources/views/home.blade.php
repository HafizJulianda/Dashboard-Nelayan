<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Nelayan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #f8fafc;
        }
        .card {
            border-radius: 1rem;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        th {
            background-color: #f1f5f9;
        }
        .form-select, .form-control {
            border-radius: .5rem;
        }
    </style>
</head>
<body>
<div class="container py-5">
    <h2 class="mb-4 text-center fw-bold text-primary">📊 Data Nelayan Bengkalis</h2>

    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @elseif(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    {{-- Tombol Tambah Data --}}
    <div class="text-end mb-3">
        <button class="btn btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-circle me-1"></i> Tambah Data
        </button>
    </div>

    {{-- Tabel --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Bulan</th>
                            <th>Tahun</th>
                            <th>Berat Ikan (Ton)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $i => $row)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $row->dimWaktu->bulan }}</td>
                                <td>{{ $row->dimWaktu->tahun }}</td>
                                <td>{{ $row->dimBerat->berat ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Filter Tahun --}}
    <form method="GET" action="{{ route('home') }}" class="row g-2 justify-content-center mb-4">
        <div class="col-md-3">
            <select name="tahun" class="form-select shadow-sm" onchange="this.form.submit()">
                @foreach($waktus->pluck('tahun')->unique() as $thn)
                    <option value="{{ $thn }}" {{ request('tahun', date('Y')) == $thn ? 'selected' : '' }}>
                        Tahun {{ $thn }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    {{-- Grafik --}}
    <div class="card">
        <div class="card-body">
            <h5 class="text-center mb-3 text-secondary">📈 Grafik Total Berat Ikan per Bulan (Tahun {{ $tahunDipilih }})</h5>
            <canvas id="barChart"></canvas>
        </div>
    </div>
</div>

{{-- Modal Tambah Data --}}
<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('fakta.store') }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahLabel">Tambah Data Fakta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="bulan" class="form-label">Bulan (1 - 12)</label>
                    <input type="number" name="bulan" id="bulan" class="form-control" min="1" max="12" required>
                </div>
                <div class="mb-3">
                    <label for="tahun" class="form-label">Tahun</label>
                    <input type="number" name="tahun" id="tahun" class="form-control" placeholder="Contoh: 2025" required>
                </div>
                <div class="mb-3">
                    <label for="berat" class="form-label">Berat Ikan (Ton)</label>
                    <input type="number" name="berat" id="berat" class="form-control" placeholder="Contoh: 2.5" step="0.1" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Chart.js --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('barChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            datasets: [{
                label: 'Total Berat (Ton)',
                data: {!! json_encode($chartData) !!},
                backgroundColor: 'rgba(13, 110, 253, 0.6)',
                borderColor: 'rgba(13, 110, 253, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Ton' }
                },
                x: {
                    title: { display: true, text: 'Bulan' }
                }
            }
        }
    });
});
</script>

{{-- Bootstrap JS + Icons --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</body>
</html>
