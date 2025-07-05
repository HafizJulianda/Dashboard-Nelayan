<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FaktaNelayan;
use App\Models\DimBerat;
use App\Models\DimWaktu;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $tahunDipilih = $request->tahun ?? date('Y');

        // Ambil semua data dengan relasi untuk tabel
        $data = FaktaNelayan::with(['dimBerat', 'dimWaktu'])->get();

        // Dropdown filter
        $berats = DimBerat::all();
        $waktus = DimWaktu::all();

        // Inisialisasi label dan data grafik (bulan 1-12)
        $chartLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $chartData = array_fill(0, 12, 0); // default 0 Ton tiap bulan

        // Ambil total berat per bulan (group by bulan dan tahun)
        $beratPerBulan = DB::table('fakta_nelayan')
            ->join('dim_waktu', 'fakta_nelayan.id_waktu', '=', 'dim_waktu.id')
            ->join('dim_berat', 'fakta_nelayan.id_berat', '=', 'dim_berat.id')
            ->where('dim_waktu.tahun', $tahunDipilih)
            ->select('dim_waktu.bulan', DB::raw('SUM(dim_berat.berat) as total_berat'))
            ->groupBy('dim_waktu.bulan')
            ->get();

        // Masukkan hasil ke array berdasarkan bulan (index 0-11)
        foreach ($beratPerBulan as $item) {
            $index = $item->bulan - 1; // karena bulan dimulai dari 1
            if ($index >= 0 && $index < 12) {
                $chartData[$index] = (float) $item->total_berat;
            }
        }

        return view('home', compact(
            'data',
            'berats',
            'waktus',
            'chartLabels',
            'chartData',
            'tahunDipilih'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|digits:4',
            'berat' => 'required|numeric|min:0',
        ]);

        // Simpan atau ambil waktu
        $dimWaktu = DimWaktu::firstOrCreate([
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
        ]);

        // Cek duplikat data
        $exists = FaktaNelayan::where('id_waktu', $dimWaktu->id)->exists();
        if ($exists) {
            return redirect()->route('home')->with('error', 'Data untuk bulan dan tahun tersebut sudah ada.');
        }

        // Simpan atau ambil kategori berat
        $dimBerat = DimBerat::firstOrCreate([
            'berat' => $request->berat,
        ]);

        // Simpan fakta
        FaktaNelayan::create([
            'id_waktu' => $dimWaktu->id,
            'id_berat' => $dimBerat->id,
        ]);

        return redirect()->route('home')->with('success', 'Data berhasil ditambahkan.');
    }
}
