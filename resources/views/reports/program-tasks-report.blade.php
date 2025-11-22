<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Program - {{ $program->program_name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.5;
        }

        .container {
            padding: 20px;
        }

        .header {
            text-align: center;
            padding-bottom: 15px;
            border-bottom: 2px solid #333;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .header p {
            font-size: 10px;
            color: #666;
        }

        .section {
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd;
        }

        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            width: 30%;
            padding: 5px 10px 5px 0;
            font-weight: bold;
            vertical-align: top;
        }

        .info-value {
            display: table-cell;
            padding: 5px 0;
            vertical-align: top;
        }

        .stats-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .stats-row {
            display: table-row;
        }

        .stats-cell {
            display: table-cell;
            width: 25%;
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }

        .stats-value {
            font-size: 16px;
            font-weight: bold;
            display: block;
            margin-bottom: 3px;
        }

        .stats-label {
            font-size: 9px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th {
            background-color: #f5f5f5;
            padding: 8px;
            text-align: left;
            font-size: 10px;
            border: 1px solid #ddd;
        }

        table td {
            padding: 6px 8px;
            border: 1px solid #ddd;
            font-size: 10px;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }

        .status-not_started {
            background-color: #e5e5e5;
            color: #666;
        }

        .status-in_progress {
            background-color: #e3f2fd;
            color: #1976d2;
        }

        .status-completed {
            background-color: #e8f5e9;
            color: #388e3c;
        }

        .status-on_hold {
            background-color: #fff3e0;
            color: #f57c00;
        }

        .status-cancelled {
            background-color: #ffebee;
            color: #d32f2f;
        }

        .priority-high {
            color: #d32f2f;
            font-weight: bold;
        }

        .priority-medium {
            color: #f57c00;
        }

        .priority-low {
            color: #388e3c;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 9px;
            color: #666;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Laporan Tugas Program</h1>
            <p>{{ $program->program_name }}</p>
            <p>Dicetak pada: {{ $generatedDate }}</p>
        </div>

        <!-- Informasi Program -->
        <div class="section">
            <div class="section-title">INFORMASI PROGRAM</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Nama Program</div>
                    <div class="info-value">{{ $program->program_name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Deskripsi</div>
                    <div class="info-value">{{ $program->description ?: '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Tanggal Mulai</div>
                    <div class="info-value">{{ $program->start_date ? $program->start_date->format('d M Y') : '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Tanggal Selesai</div>
                    <div class="info-value">{{ $program->end_date ? $program->end_date->format('d M Y') : '-' }}</div>
                </div>
            </div>
        </div>

        <!-- Statistik Program -->
        <div class="section">
            <div class="section-title">STATISTIK PROGRAM</div>
            <div class="stats-grid">
                <div class="stats-row">
                    <div class="stats-cell">
                        <span class="stats-value">{{ $totalTasks }}</span>
                        <span class="stats-label">Total Tugas</span>
                    </div>
                    <div class="stats-cell">
                        <span class="stats-value">{{ $completedTasks }}</span>
                        <span class="stats-label">Tugas Selesai</span>
                    </div>
                    <div class="stats-cell">
                        <span class="stats-value">{{ $totalTasks > 0 ? number_format(($completedTasks / $totalTasks) * 100, 0) : 0 }}%</span>
                        <span class="stats-label">Persentase Selesai</span>
                    </div>
                    <div class="stats-cell">
                        <span class="stats-value">{{ $totalTasks - $completedTasks }}</span>
                        <span class="stats-label">Tugas Berjalan</span>
                    </div>
                </div>
                <div class="stats-row">
                    <div class="stats-cell" colspan="2">
                        <span class="stats-value">Rp {{ number_format($totalBudget, 0, ',', '.') }}</span>
                        <span class="stats-label">Total Estimasi Anggaran</span>
                    </div>
                    <div class="stats-cell" colspan="2">
                        <span class="stats-value">Rp {{ number_format($totalRealization, 0, ',', '.') }}</span>
                        <span class="stats-label">Total Realisasi Anggaran</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Task -->
        <div class="section page-break">
            <div class="section-title">DAFTAR TUGAS</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 25%;">Nama Task</th>
                        <th style="width: 15%;">Penanggung Jawab</th>
                        <th style="width: 12%;">Status</th>
                        <th style="width: 8%;">Prioritas</th>
                        <th style="width: 10%;">Mulai</th>
                        <th style="width: 10%;">Selesai</th>
                        <th style="width: 15%;">Anggaran</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $statusLabels = [
                            'not_started' => 'Belum Dimulai',
                            'in_progress' => 'Sedang Berjalan',
                            'completed' => 'Selesai',
                            'on_hold' => 'Ditunda',
                            'cancelled' => 'Dibatalkan'
                        ];
                    @endphp
                    @foreach($tasks as $index => $task)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $task->task_name }}</td>
                        <td>{{ $task->assignedUser->name ?? '-' }}</td>
                        <td>
                            <span class="status-badge status-{{ $task->status }}">
                                {{ $statusLabels[$task->status] ?? $task->status }}
                            </span>
                        </td>
                        <td>
                            <span class="priority-{{ $task->priority }}">
                                {{ ucfirst($task->priority ?? '-') }}
                            </span>
                        </td>
                        <td>{{ $task->start_date ? $task->start_date->format('d/m/Y') : '-' }}</td>
                        <td>{{ $task->end_date ? $task->end_date->format('d/m/Y') : '-' }}</td>
                        <td style="text-align: right;">Rp {{ number_format($task->estimated_budget ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Detail Task per Task -->
        @foreach($tasks as $taskIndex => $task)
        <div class="section page-break">
            <div class="section-title">DETAIL TUGAS {{ $taskIndex + 1 }}: {{ strtoupper($task->task_name) }}</div>

            <!-- Info Task -->
            <div style="margin-bottom: 15px;">
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Deskripsi</div>
                        <div class="info-value">{{ $task->task_description ?: '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Penanggung Jawab</div>
                        <div class="info-value">{{ $task->assignedUser->name ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Status</div>
                        <div class="info-value">
                            <span class="status-badge status-{{ $task->status }}">
                                {{ $statusLabels[$task->status] ?? $task->status }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Target Task -->
            @if($task->targets->count() > 0)
            <div style="margin-bottom: 15px;">
                <strong style="font-size: 11px;">Target:</strong>
                <table style="margin-top: 5px;">
                    <thead>
                        <tr>
                            <th style="width: 40%;">Nama Target</th>
                            <th style="width: 15%;">Target</th>
                            <th style="width: 15%;">Tercapai</th>
                            <th style="width: 15%;">Satuan</th>
                            <th style="width: 15%;">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($task->targets as $target)
                        <tr>
                            <td>{{ $target->target_name }}</td>
                            <td style="text-align: right;">{{ number_format($target->target_value, 0, ',', '.') }}</td>
                            <td style="text-align: right;">{{ number_format($target->achieved_value ?? 0, 0, ',', '.') }}</td>
                            <td>{{ $target->target_unit ?? '-' }}</td>
                            <td>{{ $target->target_date ? $target->target_date->format('d/m/Y') : '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            <!-- Realisasi Anggaran -->
            @if($task->budgetRealizations->count() > 0)
            <div style="margin-bottom: 15px;">
                <strong style="font-size: 11px;">Realisasi Anggaran:</strong>
                <table style="margin-top: 5px;">
                    <thead>
                        <tr>
                            <th style="width: 15%;">Tanggal</th>
                            <th style="width: 50%;">Deskripsi</th>
                            <th style="width: 20%;">Kategori</th>
                            <th style="width: 15%;">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $taskTotalBudget = 0; @endphp
                        @foreach($task->budgetRealizations as $budget)
                        <tr>
                            <td>{{ $budget->transaction_date ? $budget->transaction_date->format('d/m/Y') : '-' }}</td>
                            <td>{{ $budget->description }}</td>
                            <td>{{ $budget->category ?? '-' }}</td>
                            <td style="text-align: right;">Rp {{ number_format($budget->amount, 0, ',', '.') }}</td>
                        </tr>
                        @php $taskTotalBudget += $budget->amount; @endphp
                        @endforeach
                        <tr style="font-weight: bold; background-color: #f5f5f5;">
                            <td colspan="3" style="text-align: right;">TOTAL</td>
                            <td style="text-align: right;">Rp {{ number_format($taskTotalBudget, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @endif

            <!-- Logbook -->
            @if($task->logbooks->count() > 0)
            <div>
                <strong style="font-size: 11px;">Logbook ({{ $task->logbooks->count() }} aktivitas):</strong>
                <table style="margin-top: 5px;">
                    <thead>
                        <tr>
                            <th style="width: 15%;">Tanggal</th>
                            <th style="width: 50%;">Aktivitas</th>
                            <th style="width: 15%;">Progress</th>
                            <th style="width: 20%;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($task->logbooks->take(5) as $logbook)
                        <tr>
                            <td>{{ $logbook->activity_date ? $logbook->activity_date->format('d/m/Y') : '-' }}</td>
                            <td>{{ Str::limit($logbook->title, 60) }}</td>
                            <td style="text-align: center;">{{ $logbook->progress_value ?? '-' }}%</td>
                            <td>{{ $logbook->verified_at ? 'Verified' : 'Pending' }}</td>
                        </tr>
                        @endforeach
                        @if($task->logbooks->count() > 5)
                        <tr>
                            <td colspan="4" style="text-align: center; font-style: italic; color: #666;">
                                ... dan {{ $task->logbooks->count() - 5 }} logbook lainnya
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @endif
        </div>
        @endforeach

        <!-- Footer -->
        <div class="footer">
            <p>Laporan ini dibuat secara otomatis oleh sistem PMIS Desa</p>
            <p>Tanggal Cetak: {{ $generatedDate }}</p>
        </div>
    </div>
</body>
</html>
