<?php

namespace App\Http\Controllers;

use App\Models\Potensi;
use Illuminate\Http\Request;

class PotensiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('potensi.index')->with([
            'potensi' => Potensi::paginate(10),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Potensi  $potensi
     * @return \Illuminate\Http\Response
     */
    public function show(Potensi $potensi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Potensi  $potensi
     * @return \Illuminate\Http\Response
     */
    public function edit(Potensi $potensi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Potensi  $potensi
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Potensi $potensi)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Potensi  $potensi
     * @return \Illuminate\Http\Response
     */
    public function destroy(Potensi $potensi)
    {
        //
    }
}
