<?php

namespace App\Http\Controllers;

use App\Services\ReporteService;
use App\Services\ReporteGeneratorService;
use App\Models\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReporteChatController extends Controller
{
    public function __construct(
        private ReporteService          $reporteService,
        private ReporteGeneratorService $generatorService
    ) {}

    public function proyectos()
    {
        return response()->json(
            Proyecto::where('user_id', Auth::id())
                ->withCount('documentos')
                ->get(['id', 'nombre', 'documentos_count'])
        );
    }

    public function generar(Request $request)
    {
        $request->validate([
            'instrucciones' => 'required|string|max:1000',
            'formato'       => 'required|in:pdf,docx',
            'proyecto_id'   => 'nullable|exists:proyectos,id',
        ]);

        if ($request->proyecto_id) {
            Proyecto::where('id', $request->proyecto_id)
                ->where('user_id', Auth::id())
                ->firstOrFail();
        }

        @mkdir(storage_path('app/temp'), 0755, true);

        $contenido = $this->reporteService->generarContenido(
            Auth::id(),
            $request->proyecto_id,
            $request->instrucciones
        );

        $proyectoNombre = $request->proyecto_id
            ? Proyecto::find($request->proyecto_id)->nombre
            : 'Tutti i Progetti';

        $empresa = \App\Models\Empresa::where('user_id', Auth::id())->first();
        $titulo  = $empresa?->nombre ?? 'Docum24 Report';
        $filename = 'report_' . now()->format('Ymd_His');

        if ($request->formato === 'pdf') {
            $path = $this->generatorService->generarPDF($contenido, $titulo);
            return response()->download($path, $filename . '.pdf', [
                'Content-Type' => 'application/pdf',
            ])->deleteFileAfterSend(true);
        }

        $path = $this->generatorService->generarDocx($contenido, $titulo);
        return response()->download($path, $filename . '.docx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ])->deleteFileAfterSend(true);
    }
}
