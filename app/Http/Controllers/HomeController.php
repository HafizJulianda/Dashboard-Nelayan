<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FaktaNelayan;
use App\Models\DimBerat;
use App\Models\DimWaktu;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $tahunDipilih = $request->tahun ?? date('Y');

        // Ambil semua data dengan relasi
        $data = FaktaNelayan::with(['dimBerat', 'dimWaktu'])->get();

        // Dropdown filter
        $berats = DimBerat::all();
        $waktus = DimWaktu::all();

        // Inisialisasi array bulan 1 - 12
        $chartLabels = range(1, 12); // 1 sampai 12
        $chartData = array_fill(0, 12, 0); // semua 0

        // Ambil data yang sesuai tahun dipilih
        $fakta = FaktaNelayan::with('dimWaktu')
            ->whereHas('dimWaktu', function($q) use ($tahunDipilih) {
                $q->where('tahun', $tahunDipilih);
            })
            ->get();

        foreach ($fakta as $item) {
            $bulan = $item->dimWaktu->bulan;
            if ($bulan >= 1 && $bulan <= 12) {
                $chartData[$bulan - 1]++;
            }
        }

        return view('home', compact('data', 'berats', 'waktus', 'chartLabels', 'chartData', 'tahunDipilih'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|digits:4',
            'berat' => 'required|numeric|min:0',
        ]);

        $dimWaktu = DimWaktu::firstOrCreate([
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
        ]);

        $exists = FaktaNelayan::where('id_waktu', $dimWaktu->id)->exists();
        if ($exists) {
            return redirect()->route('home')->with('error', 'Data untuk bulan dan tahun tersebut sudah ada.');
        }

        $dimBerat = DimBerat::firstOrCreate([
            'berat' => $request->berat,
        ]);

        FaktaNelayan::create([
            'id_waktu' => $dimWaktu->id,
            'id_berat' => $dimBerat->id,
        ]);

        return redirect()->route('home')->with('success', 'Data berhasil ditambahkan.');
    }
}
