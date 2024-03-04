<?php

namespace App\Http\Controllers;

use App\Models\Penduduk;
use App\Models\Sosial;
use Illuminate\Http\Request;
use App\Exports\pendudukExport;
use App\Imports\pendudukImport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\StorePendudukRequest;
use App\Http\Requests\UpdatePendudukRequest;
use Exception;

class PendudukController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                $columns = array(
                    0 => 'id',
                    1 => 'nama',
                    2 => 'tempat_lahir',
                    3 => 'tanggal_lahir',
                    4 => 'jenis_kelamin',
                    5 => 'golongan_darah',
                    6 => 'agama',
                    7 => 'pekerjaan',
                    8 => 'alamat',
                    9 => 'rt',
                    10 => 'keterangan',
                );

                $totalData = Penduduk::count();

                $totalFiltered = $totalData;

                $limit = $request->input('length', 10);
                $start = $request->input('start', 0);
                $order = $columns[$request->input('order.0.column', 'id')];
                $dir = $request->input('order.0.dir', 'asc');

                if (empty($request->input('search.value'))) {
                        $query = Penduduk::query()
                            ->when(request()->filled('golDarah'), function ($q) {
                                $q->where('golongan_darah', request('golDarah'));
                            })
                            ->when(request()->filled('jenisKelamin'), function ($q) {
                                $q->where('jenis_kelamin', request('jenisKelamin'));
                            })
                            ->when(request()->filled('rt'), function ($q) {
                                $q->where('rt', request('rt'));
                            });

                        $totalFiltered = $query->count();

                        $orderColumnIndex = $request->input('order.0.column');
                        $orderColumnName = $columns[$orderColumnIndex] ?? null;
                        $orderDir = $request->input('order.0.dir');

                        if ($orderColumnName) {
                            $query->orderBy($orderColumnName, $orderDir);
                        }

                        $penduduks = $query->offset($start)
                            ->limit($limit)
                            ->orderBy($order, $dir)
                            // ->latest()
                            ->get();
                } else {
                    $search = $request->input('search.value');

                    $penduduks =  Penduduk::where('nama', 'LIKE', "%{$search}%")
                        ->orWhere('nama', 'LIKE', "%{$search}%")
                        ->orWhere('tempat_lahir', 'LIKE', "%{$search}%")
                        ->orWhere('tanggal_lahir', 'LIKE', "%{$search}%")
                        ->orWhere('jenis_kelamin', 'LIKE', "%{$search}%")
                        ->orWhere('golongan_darah', 'LIKE', "%{$search}%")
                        ->orWhere('agama', 'LIKE', "%{$search}%")
                        ->orWhere('pekerjaan', 'LIKE', "%{$search}%")
                        ->orWhere('alamat', 'LIKE', "%{$search}%")
                        ->orWhere('rt', 'LIKE', "%u{$search}%")
                        ->orWhere('keterangan', 'LIKE', "%{$search}%")
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy('id', 'asc')
                        ->get();

                    $totalFiltered = Penduduk::where('id', 'LIKE', "%{$search}%")
                        ->orWhere('nama', 'LIKE', "%{$search}%")
                        ->orWhere('tempat_lahir', 'LIKE', "%{$search}%")
                        ->orWhere('tanggal_lahir', 'LIKE', "%{$search}%")
                        ->orWhere('jenis_kelamin', 'LIKE', "%{$search}%")
                        ->orWhere('golongan_darah', 'LIKE', "%{$search}%")
                        ->orWhere('agama', 'LIKE', "%{$search}%")
                        ->orWhere('pekerjaan', 'LIKE', "%{$search}%")
                        ->orWhere('alamat', 'LIKE', "%{$search}%")
                        ->orWhere('rt', 'LIKE', "%{$search}%")
                        ->orWhere('keterangan', 'LIKE', "%{$search}%")
                        ->count();
                }

                $penduduks = $penduduks->map(function ($penduduk) {
                    $penduduk->action = (string) view('admin.penduduk.action', [
                        'item' => $penduduk
                    ]);
                    $penduduk->tanggal_lahir = date('d-m-Y', strtotime($penduduk->tanggal_lahir));
                    return $penduduk;
                });
            } catch (Exception $e) {
                return $e;
            }

            $json_data = array(
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data"            => $penduduks
            );

            return json_encode($json_data);
        }

        $gol_darah = Penduduk::pluck('golongan_darah')->filter(function ($value) {
            return $value != '-';
        })->unique()->sort();

        $jenis_kelamin = Penduduk::pluck('jenis_kelamin')->unique();

        $rt = Penduduk::pluck('rt')->unique()->sort();

        return view('admin.penduduk.index', compact('gol_darah', 'jenis_kelamin', 'rt'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.penduduk.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePendudukRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePendudukRequest $request)
    {
        Penduduk::create($request->validated());

        return redirect()->route('penduduk.index')
            ->with('success', 'Penduduk berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Penduduk  $penduduk
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $no_kk = $request->input('no_kk');

        $result = DB::select(
            '
            SELECT *, DATE_FORMAT(tanggal_lahir, "%d-%m-%Y") AS tanggal_lahir,
            CASE
                WHEN jenis_kelamin = "L" THEN "Laki-laki"
                WHEN jenis_kelamin = "P" THEN "Perempuan"
                ELSE jenis_kelamin
            END AS jenis_kelamin,
            CASE
            WHEN status_keluarga = "1" THEN "Kepala Keluarga"
                ELSE "-"
            END AS status_keluarga
            FROM penduduk WHERE no_kk = ?
            ORDER BY YEAR(tanggal_lahir)
            ',
            [$no_kk]
        );

        return response()->json($result);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Penduduk  $penduduk
     * @return \Illuminate\Http\Response
     */
    public function edit(string $id)
    {
        $penduduk = Penduduk::find($id);

        return view('admin.penduduk.edit', compact('penduduk'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePendudukRequest  $request
     * @param  \App\Models\Penduduk  $penduduk
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePendudukRequest $request, string $id)
    {
        Penduduk::find($id)->update($request->validated());

        return redirect()->route('penduduk.index')
            ->with('success', 'Penduduk berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Penduduk  $penduduk
     * @return \Illuminate\Http\Response
     */
    public function destroy(Penduduk $penduduk)
    {
        $penduduk->delete();

        return redirect()->route('penduduk.index')
            ->with('success', 'Penduduk berhasil dihapus');
    }

    public function import()
    {
        Excel::import(new pendudukImport, request()->file('file'));

        return redirect()->route('penduduk.index')
            ->with('success', 'Penduduk berhasil diimport');
    }

    public function export()
    {
        return Excel::download(new pendudukExport, 'penduduk.xlsx');
    }
}
