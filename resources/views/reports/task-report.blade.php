<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Tugas - {{ $task->task_name }}</title>
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

        .no-data {
            text-align: center;
            padding: 20px;
            color: #999;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Laporan Detail Tugas</h1>
            <p>{{ $task->program->program_name ?? 'N/A' }}</p>
            <p>Dicetak pada: {{ $generatedDate }}</p>
        </div>

        <!-- Informasi Umum Task -->
        <div class="section">
            <div class="section-title">INFORMASI UMUM</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Nama Tugas</div>
                    <div class="info-value">{{ $task->task_name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Deskripsi</div>
                    <div class="info-value">{{ $task->task_description ?: '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Program</div>
                    <div class="info-value">{{ $task->program->program_name ?? '-' }}</div>
                </div>
                @if ($task->parentTask)
                    <div class="info-row">
                        <div class="info-label">Parent Tugas</div>
                        <div class="info-value">{{ $task->parentTask->task_name ?? '-' }}</div>
                    </div>
                @endif
                <div class="info-row">
                    <div class="info-label">Penanggung Jawab</div>
                    <div class="info-value">{{ $task->assignedUser->name ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Status</div>
                    <div class="info-value">
                        @php
                            $statusLabels = [
                                'not_started' => 'Belum Dimulai',
                                'in_progress' => 'Sedang Berjalan',
                                'completed' => 'Selesai',
                                'on_hold' => 'Ditunda',
                                'cancelled' => 'Dibatalkan'
                            ];
                        @endphp
                        <span class="status-badge status-{{ $task->status }}">
                            {{ $statusLabels[$task->status] ?? $task->status }}
                        </span>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Prioritas</div>
                    <div class="info-value">
                        <span class="priority-{{ $task->priority }}">
                            {{ ucfirst($task->priority ?? '-') }}
                        </span>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Tanggal Mulai</div>
                    <div class="info-value">{{ $task->start_date ? $task->start_date->format('d M Y') : '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Tanggal Selesai</div>
                    <div class="info-value">{{ $task->end_date ? $task->end_date->format('d M Y') : '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Estimasi Anggaran</div>
                    <div class="info-value">Rp {{ number_format($task->estimated_budget ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <!-- Statistik -->
        <div class="section">
            <div class="section-title">STATISTIK</div>
            <div class="stats-grid">
                <div class="stats-row">
                    <div class="stats-cell">
                        <span class="stats-value">{{ $totalTargets }}</span>
                        <span class="stats-label">Total Target</span>
                    </div>
                    <div class="stats-cell">
                        <span class="stats-value">{{ $achievedTargets }}</span>
                        <span class="stats-label">Target Tercapai</span>
                    </div>
                    <div class="stats-cell">
                        <span class="stats-value">{{ number_format($progressPercentage, 0) }}%</span>
                        <span class="stats-label">Progress</span>
                    </div>
                    <div class="stats-cell">
                        <span class="stats-value">{{ $totalLogbooks }}</span>
                        <span class="stats-label">Total Logbook</span>
                    </div>
                </div>
                <div class="stats-row">
                    <div class="stats-cell">
                        <span class="stats-value">Rp {{ number_format($totalBudget, 0, ',', '.') }}</span>
                        <span class="stats-label">Total Realisasi Anggaran</span>
                    </div>
                    <div class="stats-cell">
                        <span class="stats-value">{{ $verifiedLogbooks }}</span>
                        <span class="stats-label">Logbook Terverifikasi</span>
                    </div>
                    <div class="stats-cell">
                        <span class="stats-value">{{ $task->attachments->count() }}</span>
                        <span class="stats-label">Total Lampiran</span>
                    </div>
                    <div class="stats-cell">
                        <span class="stats-value">{{ $task->subTasks->count() }}</span>
                        <span class="stats-label">Sub Tugas</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sub Tasks -->
        @if($task->subTasks->count() > 0)
        <div class="section">
            <div class="section-title">SUB TUGAS</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 35%;">Nama Tugas</th>
                        <th style="width: 20%;">Penanggung Jawab</th>
                        <th style="width: 15%;">Status</th>
                        <th style="width: 12%;">Mulai</th>
                        <th style="width: 13%;">Selesai</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($task->subTasks as $index => $subTask)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $subTask->task_name }}</td>
                        <td>{{ $subTask->assignedUser->name ?? '-' }}</td>
                        <td>
                            <span class="status-badge status-{{ $subTask->status }}">
                                {{ $statusLabels[$subTask->status] ?? $subTask->status }}
                            </span>
                        </td>
                        <td>{{ $subTask->start_date ? $subTask->start_date->format('d/m/Y') : '-' }}</td>
                        <td>{{ $subTask->end_date ? $subTask->end_date->format('d/m/Y') : '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Targets -->
        @if($task->targets->count() > 0)
        <div class="section page-break">
            <div class="section-title">TARGET TUGAS</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 30%;">Nama Target</th>
                        <th style="width: 15%;">Target</th>
                        <th style="width: 15%;">Tercapai</th>
                        <th style="width: 10%;">Satuan</th>
                        <th style="width: 15%;">Tanggal Target</th>
                        <th style="width: 10%;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($task->targets as $index => $target)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $target->target_name }}</td>
                        <td style="text-align: right;">{{ number_format($target->target_value, 0, ',', '.') }}</td>
                        <td style="text-align: right;">{{ number_format($target->achieved_value ?? 0, 0, ',', '.') }}</td>
                        <td>{{ $target->target_unit ?? '-' }}</td>
                        <td>{{ $target->target_date ? $target->target_date->format('d/m/Y') : '-' }}</td>
                        <td style="text-align: center;">
                            @if($target->achieved_value >= $target->target_value)
                                ✓
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Budget Realizations -->
        @if($task->budgetRealizations->count() > 0)
        <div class="section">
            <div class="section-title">REALISASI ANGGARAN</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 15%;">Tanggal</th>
                        <th style="width: 15%;">Kategori</th>
                        <th style="width: 35%;">Deskripsi</th>
                        <th style="width: 15%;">Jumlah</th>
                        <th style="width: 15%;">Dibuat Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($task->budgetRealizations as $index => $budget)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $budget->transaction_date ? $budget->transaction_date->format('d/m/Y') : '-' }}</td>
                        <td>{{ $budget->category ?? '-' }}</td>
                        <td>{{ $budget->description }}</td>
                        <td style="text-align: right;">Rp {{ number_format($budget->amount, 0, ',', '.') }}</td>
                        <td>{{ $budget->creator->name ?? '-' }}</td>
                    </tr>
                    @endforeach
                    <tr style="font-weight: bold; background-color: #f5f5f5;">
                        <td colspan="4" style="text-align: right;">TOTAL</td>
                        <td style="text-align: right;">Rp {{ number_format($totalBudget, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif

        <!-- Logbooks -->
        @if($task->logbooks->count() > 0)
        <div class="section page-break">
            <div class="section-title">LOGBOOK AKTIVITAS</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 12%;">Tanggal</th>
                        <th style="width: 20%;">Judul</th>
                        <th style="width: 30%;">Deskripsi</th>
                        <th style="width: 10%;">Progress</th>
                        <th style="width: 13%;">Dibuat Oleh</th>
                        <th style="width: 10%;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($task->logbooks as $index => $logbook)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $logbook->activity_date ? $logbook->activity_date->format('d/m/Y') : '-' }}</td>
                        <td>{{ $logbook->title }}</td>
                        <td>{{ Str::limit($logbook->description, 100) }}</td>
                        <td style="text-align: center;">{{ $logbook->progress_value ?? '-' }}%</td>
                        <td>{{ $logbook->creator->name ?? '-' }}</td>
                        <td style="text-align: center;">
                            @if($logbook->verified_at)
                                ✓ Verified
                            @else
                                Pending
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Attachments -->
        @if($task->attachments->count() > 0)
        <div class="section">
            <div class="section-title">LAMPIRAN</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 40%;">Nama File</th>
                        <th style="width: 15%;">Tipe</th>
                        <th style="width: 15%;">Ukuran</th>
                        <th style="width: 25%;">Diunggah Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($task->attachments as $index => $attachment)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $attachment->file_name }}</td>
                        <td>{{ $attachment->file_type ?? '-' }}</td>
                        <td>{{ number_format($attachment->file_size / 1024, 2) }} KB</td>
                        <td>{{ $attachment->uploader->name ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>Laporan ini dibuat secara otomatis oleh sistem PMIS Desa</p>
            <p>Tanggal Cetak: {{ $generatedDate }}</p>
        </div>
    </div>
</body>
</html>
