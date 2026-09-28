<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">

    <title>Rapport national PNAE-RCA</title>

    <style>
        @page {
            margin: 25px;
        }

        body {
            color: #18243a;
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
        }

        h1,
        h2 {
            color: #073b88;
        }

        h1 {
            margin: 0;
            font-size: 22px;
        }

        .subtitle {
            margin: 4px 0 20px;
            color: #68758a;
        }

        .kpi-table,
        .report-table {
            width: 100%;
            border-collapse: collapse;
        }

        .kpi-table td {
            width: 25%;
            padding: 12px;
            border: 1px solid #d6deea;
        }

        .kpi-table span,
        .kpi-table strong {
            display: block;
        }

        .kpi-table span {
            color: #68758a;
            font-size: 8px;
        }

        .kpi-table strong {
            margin-top: 5px;
            color: #073b88;
            font-size: 17px;
        }

        .section {
            margin-top: 22px;
        }

        .report-table th,
        .report-table td {
            padding: 7px;
            border: 1px solid #d6deea;
        }

        .report-table th {
            color: #ffffff;
            background: #073b88;
        }

        .alert {
            margin-top: 5px;
            padding: 8px;
            border-left: 4px solid #ffcd00;
            background: #fff7d9;
        }

        .footer {
            position: fixed;
            right: 0;
            bottom: -10px;
            left: 0;
            color: #7d8797;
            text-align: center;
        }
    </style>
</head>

<body>

<h1>Rapport exécutif national PNAE-RCA</h1>

<div class="subtitle">
    Situation au {{ now()->format('d/m/Y à H:i') }}
</div>

<table class="kpi-table">
    <tr>
        <td>
            <span>Citoyens</span>
            <strong>{{ $dashboard['summary']['citizens'] }}</strong>
        </td>

        <td>
            <span>Agents</span>
            <strong>{{ $dashboard['summary']['agents'] }}</strong>
        </td>

        <td>
            <span>Dossiers</span>
            <strong>{{ $dashboard['summary']['applications'] }}</strong>
        </td>

        <td>
            <span>Score national</span>
            <strong>
                {{ $dashboard['nationalScore']['score'] }} %
            </strong>
        </td>
    </tr>

    <tr>
        <td>
            <span>Paiements confirmés</span>
            <strong>{{ $dashboard['paymentSummary']['paye'] }}</strong>
        </td>

        <td>
            <span>Recettes nationales</span>
            <strong>
                {{
                    number_format(
                        $dashboard['summary']['revenue_total'],
                        0,
                        ',',
                        ' '
                    )
                }}
                FCFA
            </strong>
        </td>

        <td>
            <span>Documents officiels</span>
            <strong>
                {{ $dashboard['summary']['official_documents'] }}
            </strong>
        </td>

        <td>
            <span>Temps moyen</span>
            <strong>
                {{ $dashboard['averageProcessing']['global_days'] }}
                jour(s)
            </strong>
        </td>
    </tr>
</table>

<div class="section">
    <h2>Centre d’alertes</h2>

    @foreach($dashboard['alerts'] as $alert)
        <div class="alert">
            <strong>
                {{ $alert['count'] }} — {{ $alert['title'] }}
            </strong>

            <div>{{ $alert['description'] }}</div>
        </div>
    @endforeach
</div>

<div class="section">
    <h2>Performance des ministères</h2>

    <table class="report-table">
        <thead>
            <tr>
                <th>Ministère</th>
                <th>Démarches</th>
                <th>Dossiers</th>
                <th>Validés</th>
                <th>Traitement</th>
                <th>Rejetés</th>
                <th>Taux</th>
            </tr>
        </thead>

        <tbody>
            @foreach($dashboard['ministryPerformance'] as $ministry)
                <tr>
                    <td>{{ $ministry->name }}</td>
                    <td>{{ $ministry->procedures_count }}</td>
                    <td>{{ $ministry->total_applications }}</td>
                    <td>{{ $ministry->validated_applications }}</td>
                    <td>{{ $ministry->processing_applications }}</td>
                    <td>{{ $ministry->rejected_applications }}</td>
                    <td>{{ $ministry->validation_rate }} %</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="section">
    <h2>Top des démarches</h2>

    <table class="report-table">
        <thead>
            <tr>
                <th>Rang</th>
                <th>Démarche</th>
                <th>Ministère</th>
                <th>Dossiers</th>
            </tr>
        </thead>

        <tbody>
            @foreach($dashboard['topProcedures'] as $procedure)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $procedure->title }}</td>
                    <td>{{ $procedure->ministry->name ?? '-' }}</td>
                    <td>{{ $procedure->applications_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="footer">
    PNAE-RCA — Rapport généré automatiquement
</div>

</body>
</html>