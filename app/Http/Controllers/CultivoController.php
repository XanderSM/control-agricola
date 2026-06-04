<?php

namespace App\Http\Controllers;

use App\Models\Cultivo;
use App\Models\Parcela;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CultivoController extends Controller
{
    public function index()
    {
        // Traemos los cultivos con su parcela asociada
        $cultivos = Cultivo::with('parcela')->get();
        $parcelas = Parcela::all();
        
        return Inertia::render('Cultivos/Index', [
            'cultivos' => $cultivos,
            'parcelas' => $parcelas
        ]);
    }

    public function store(Request $request)
    {
        // Rechazar explícitamente valores numéricos negativos
        if ($request->input('parcela_id') < 0) {
            return back()->withErrors(['parcela_id' => 'No se permiten valores negativos.']);
        }

        $validated = $request->validate([
            'parcela_id' => 'required|integer|min:1',
            'producto' => 'required|string|max:255',
            'fecha_siembra' => 'required|date',
            'imagen' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('cultivos_img', 'public');
            $validated['imagen'] = $path;
        }

        Cultivo::create($validated);

        return redirect()->back();
    }

    public function update(Request $request, Cultivo $cultivo)
    {
        // Rechazo estricto de números negativos en la actualización
        if ($request->input('parcela_id') < 0) {
            return back()->withErrors(['parcela_id' => 'No se permiten valores negativos.']);
        }

        $validated = $request->validate([
            'parcela_id' => 'required|integer|min:1',
            'producto' => 'required|string|max:255',
            'fecha_siembra' => 'required|date',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // nullable porque puede no querer cambiar la foto
        ]);

        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('cultivos_img', 'public');
            $validated['imagen'] = $path;
        }

        $cultivo->update($validated);

        return redirect()->back();
    }

    public function destroy(Cultivo $cultivo)
    {
        $cultivo->delete();
        return redirect()->back();
    }
}
