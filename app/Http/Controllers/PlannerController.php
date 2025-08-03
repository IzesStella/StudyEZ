<?php

namespace App\Http\Controllers;

use App\Models\Planner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PlannerController extends Controller
{
    // Retorna (ou cria) a nota do dia para o usuário logado
    public function show(Request $request)
    {
        $date = $request->query('date');        // YYYY-MM-DD vindo da query string
        $user = Auth::user();

        $planner = Planner::firstOrCreate(
            ['user_id' => $user->id, 'date' => $date],
            ['note'    => '']
        );

        return response()->json([
            'date' => $planner->date,
            'note' => $planner->note,
        ]);
    }

    // Cria ou atualiza a nota
    public function store(Request $request)
{
    $data = $request->validate([
        'date' => 'required|date',
        'note' => 'nullable|string',
    ]);
    $user = Auth::user();

    // Usando Carbon para garantir que a data seja salva corretamente
    $date = Carbon::parse($data['date'])->format('Y-m-d');  // Formato de data sem hora

    $planner = Planner::updateOrCreate(
        ['user_id' => $user->id, 'date' => $date],
        ['note' => $data['note']]
    );

    return response()->json(['success' => true, 'note' => $planner->note]);
}
}