<?php

namespace App\Http\Controllers;

use App\Models\UtangPiutang;
use App\Http\Requests\StoreUtangPiutangRequest;
use App\Http\Requests\UpdateUtangPiutangRequest;

class UtangPiutangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function indexUtang()
    {
        $Utang = UtangPiutang::paginate(10);

        return view('utang-piutang.index', compact('Utang'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUtangPiutangRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(UtangPiutang $utangPiutang)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UtangPiutang $utangPiutang)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUtangPiutangRequest $request, UtangPiutang $utangPiutang)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UtangPiutang $utangPiutang)
    {
        //
    }
}
