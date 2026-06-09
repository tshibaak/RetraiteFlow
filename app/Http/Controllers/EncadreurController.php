<?php

namespace App\Http\Controllers;

use App\Http\Requests\EncadreurSotreRequest;
use App\Models\Deposite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EncadreurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $participants = Deposite::paginate(10);
       
        return view('encadreur.index');
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
    public function store(EncadreurSotreRequest $request)
    {
        try {
            $validated = $request->validated();
            
            $validated['user_id'] = Auth::id();
            $validated['status'] = 'pending';
            
            $deposite = Deposite::create($validated);
            
            return redirect()->route('encadreur.index')
                ->with('success', 'Participant enregistré avec succès!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de l\'enregistrement: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
