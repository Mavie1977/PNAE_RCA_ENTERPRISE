<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditService;
use App\Services\NationalDashboardService;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NationalReportController extends Controller
{
    public function pdf(
        NationalDashboardService $dashboardService
    ) {
        $dashboard = $dashboardService->getDashboardData();

        AuditService::log(
            'Export du rapport national PDF',
            'Rapport national',
            null,
            [
                'period' => now()->format('Y-m'),
            ]
        );

        $pdf = Pdf::loadView(
            'pdf.national-report',
            compact('dashboard')
        )
            ->setPaper('a4', 'landscape')
            ->setOption('defaultFont', 'DejaVu Sans');

        return $pdf->download(
            'Rapport_PNAE_'
            . now()->format('Y_m_d_His')
            . '.pdf'
        );
    }

    public function excel(
        NationalDashboardService $dashboardService
    ): StreamedResponse {
        $dashboard = $dashboardService->getDashboardData();

        $spreadsheet = new Spreadsheet();

        $summarySheet = $spreadsheet->getActiveSheet();
        $summarySheet->setTitle('Synthèse nationale');

        $summarySheet->fromArray([
            ['PNAE-RCA — Rapport national'],
            ['Généré le', now()->format('d/m/Y H:i')],
            [],
            ['Indicateur', 'Valeur'],
            ['Citoyens', $dashboard['summary']['citizens']],
            ['Agents publics', $dashboard['summary']['agents']],
            ['Ministères', $dashboard['summary']['ministries']],
            ['Démarches', $dashboard['summary']['procedures']],
            ['Dossiers', $dashboard['summary']['applications']],
            ['Paiements confirmés', $dashboard['paymentSummary']['paye']],
            ['Recettes FCFA', $dashboard['summary']['revenue_total']],
            ['Documents officiels', $dashboard['summary']['official_documents']],
            ['Score national', $dashboard['nationalScore']['score'] . ' %'],
            ['Temps moyen en jours', $dashboard['averageProcessing']['global_days']],
        ]);

        $summarySheet
            ->getStyle('A1:B1')
            ->getFont()
            ->setBold(true)
            ->setSize(16);

        $summarySheet
            ->getStyle('A4:B4')
            ->getFont()
            ->setBold(true)
            ->getColor()
            ->setARGB('FFFFFFFF');

        $summarySheet
            ->getStyle('A4:B4')
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FF073B88');

        $summarySheet->getColumnDimension('A')->setWidth(34);
        $summarySheet->getColumnDimension('B')->setWidth(22);

        $ministrySheet = $spreadsheet->createSheet();
        $ministrySheet->setTitle('Ministères');

        $ministrySheet->fromArray([
            [
                'Ministère',
                'Démarches',
                'Dossiers',
                'Validés',
                'En traitement',
                'Rejetés',
                'Validation %',
            ],
        ]);

        $row = 2;

        foreach ($dashboard['ministryPerformance'] as $ministry) {
            $ministrySheet->fromArray([
                [
                    $ministry->name,
                    $ministry->procedures_count,
                    $ministry->total_applications,
                    $ministry->validated_applications,
                    $ministry->processing_applications,
                    $ministry->rejected_applications,
                    $ministry->validation_rate,
                ],
            ], null, 'A' . $row);

            $row++;
        }

        $ministrySheet
            ->getStyle('A1:G1')
            ->getFont()
            ->setBold(true)
            ->getColor()
            ->setARGB('FFFFFFFF');

        $ministrySheet
            ->getStyle('A1:G1')
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FF073B88');

        foreach (range('A', 'G') as $column) {
            $ministrySheet
                ->getColumnDimension($column)
                ->setAutoSize(true);
        }

        $procedureSheet = $spreadsheet->createSheet();
        $procedureSheet->setTitle('Top démarches');

        $procedureSheet->fromArray([
            ['Démarche', 'Ministère', 'Nombre de dossiers'],
        ]);

        $row = 2;

        foreach ($dashboard['topProcedures'] as $procedure) {
            $procedureSheet->fromArray([
                [
                    $procedure->title,
                    $procedure->ministry->name ?? '-',
                    $procedure->applications_count,
                ],
            ], null, 'A' . $row);

            $row++;
        }

        $procedureSheet
            ->getStyle('A1:C1')
            ->getFont()
            ->setBold(true);

        foreach (range('A', 'C') as $column) {
            $procedureSheet
                ->getColumnDimension($column)
                ->setAutoSize(true);
        }

        AuditService::log(
            'Export du rapport national Excel',
            'Rapport national',
            null,
            [
                'period' => now()->format('Y-m'),
            ]
        );

        $filename = 'Rapport_PNAE_'
            . now()->format('Y_m_d_His')
            . '.xlsx';

        return response()->streamDownload(
            function () use ($spreadsheet): void {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            },
            $filename,
            [
                'Content-Type' =>
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]
        );
    }
}