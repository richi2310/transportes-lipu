<?php

namespace App\Http\Controllers;

use App\Models\BitacoraDiesel;
use App\Models\BitacoraAforo;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelController extends Controller
{
    public function exportarDiesel(Request $request) {
        $query = BitacoraDiesel::with(['unidad', 'despachador'])
                    ->orderBy('fecha_hora', 'desc');

        if ($request->has('fecha_inicio')) {
            $query->whereDate('fecha_hora', '>=', $request->fecha_inicio);
        }
        if ($request->has('fecha_fin')) {
            $query->whereDate('fecha_hora', '<=', $request->fecha_fin);
        }

        $registros = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Reporte Diesel');

        // Encabezados
        $sheet->setCellValue('A1', 'Fecha/Hora');
        $sheet->setCellValue('B1', 'Unidad');
        $sheet->setCellValue('C1', 'Km Anterior');
        $sheet->setCellValue('D1', 'Km Actual');
        $sheet->setCellValue('E1', 'Litros Cargados');
        $sheet->setCellValue('F1', 'Rendimiento (km/lt)');
        $sheet->setCellValue('G1', 'Despachador');

        // Estilo encabezados
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);

        // Datos
        $fila = 2;
        foreach ($registros as $r) {
            $sheet->setCellValue('A' . $fila, $r->fecha_hora);
            $sheet->setCellValue('B' . $fila, $r->unidad?->numero_unidad);
            $sheet->setCellValue('C' . $fila, $r->km_anterior);
            $sheet->setCellValue('D' . $fila, $r->km_actual);
            $sheet->setCellValue('E' . $fila, $r->litros_cargados);
            $sheet->setCellValue('F' . $fila, $r->rendimiento_km_litro);
            $sheet->setCellValue('G' . $fila, $r->despachador?->name);
            $fila++;
        }

        // Autosize columnas
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer   = new Xlsx($spreadsheet);
        $filename = 'reporte_diesel_' . now()->format('Ymd_His') . '.xlsx';
        $path     = storage_path('app/' . $filename);
        $writer->save($path);

        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }

    public function exportarAforo(Request $request) {
        $inicio = $request->get('inicio', now()->startOfWeek()->toDateString());
        $fin    = $request->get('fin',    now()->endOfWeek()->toDateString());

        $registros = BitacoraAforo::with(['unidad', 'ruta', 'colaborador', 'operador'])
            ->whereBetween('fecha_hora', [$inicio . ' 00:00:00', $fin . ' 23:59:59'])
            ->orderBy('fecha_hora', 'desc')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Reporte Aforo');

        $sheet->setCellValue('A1', 'Fecha/Hora');
        $sheet->setCellValue('B1', 'Colaborador');
        $sheet->setCellValue('C1', 'Hotel');
        $sheet->setCellValue('D1', 'Ruta');
        $sheet->setCellValue('E1', 'Unidad');
        $sheet->setCellValue('F1', 'Operador');

        $sheet->getStyle('A1:F1')->getFont()->setBold(true);

        $fila = 2;
        foreach ($registros as $r) {
            $sheet->setCellValue('A' . $fila, $r->fecha_hora);
            $sheet->setCellValue('B' . $fila, $r->colaborador?->nombre . ' ' . $r->colaborador?->apellidos);
            $sheet->setCellValue('C' . $fila, $r->colaborador?->empresa_hotel);
            $sheet->setCellValue('D' . $fila, $r->ruta?->nombre_ruta);
            $sheet->setCellValue('E' . $fila, $r->unidad?->numero_unidad);
            $sheet->setCellValue('F' . $fila, $r->operador?->name);
            $fila++;
        }

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer   = new Xlsx($spreadsheet);
        $filename = 'reporte_aforo_' . now()->format('Ymd_His') . '.xlsx';
        $path     = storage_path('app/' . $filename);
        $writer->save($path);

        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }
}
