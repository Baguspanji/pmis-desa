<?php

use Livewire\Volt\Component;
use App\Models\User;
use App\Models\Program;
use App\Models\Task;
use Illuminate\Support\Facades\DB;

new class extends Component {
    public int $totalUsers = 0;
    public int $totalPrograms = 0;
    public int $totalTasks = 0;
    public array $chartData = [];

    public function mount(): void
    {
        $this->totalUsers = User::count();
        $this->totalPrograms = Program::count();
        $this->totalTasks = Task::count();
        $this->loadChartData();
    }

    public function loadChartData(): void
    {
        // Get data for the last 12 months
        $months = [];
        $programData = [];
        $taskData = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthName = $date->format('M');
            $year = $date->format('Y');
            $monthYear = $date->format('Y-m');

            $months[] = $monthName . ' ' . substr($year, 2);

            // Count programs created in this month
            $programCount = Program::whereYear('start_date', $date->year)
                ->whereMonth('start_date', $date->month)
                ->count();
            $programData[] = $programCount;

            // Count tasks created in this month
            $taskCount = Task::whereYear('start_date', $date->year)
                ->whereMonth('start_date', $date->month)
                ->count();
            $taskData[] = $taskCount;
        }

        $maxValue = max(array_merge($programData, $taskData));

        $this->chartData = [
            'labels' => $months,
            'programs' => $programData,
            'tasks' => $taskData,
            'maxValue' => $maxValue > 0 ? $maxValue : 10,
        ];
    }
}; ?>

<div>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl pt-12">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            {{-- Total Pengguna --}}
            <div
                class="relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-800">
                <div class="flex flex-col gap-2">
                    <div class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Total Pengguna</div>
                    <div class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">
                        {{ number_format($totalUsers) }}</div>
                </div>
                <div class="absolute right-4 top-4 rounded-full bg-orange-100 p-3 dark:bg-orange-900/30">
                    <svg class="h-6 w-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>

            {{-- Total Program --}}
            <div
                class="relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-800">
                <div class="flex flex-col gap-2">
                    <div class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Total Program</div>
                    <div class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">
                        {{ number_format($totalPrograms) }}</div>
                </div>
                <div class="absolute right-4 top-4 rounded-full bg-green-100 p-3 dark:bg-green-900/30">
                    <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
            </div>

            {{-- Total Tugas --}}
            <div
                class="relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-800">
                <div class="flex flex-col gap-2">
                    <div class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Total Tugas</div>
                    <div class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">
                        {{ number_format($totalTasks) }}</div>
                </div>
                <div class="absolute right-4 top-4 rounded-full bg-blue-100 p-3 dark:bg-blue-900/30">
                    <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Bar Chart: Program & Tugas by Month --}}
        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-800">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">Program & Tugas per Bulan
                    </h3>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">Data 12 bulan terakhir</p>
                </div>
            </div>

            {{-- ApexCharts Container --}}
            <div wire:ignore>
                <div id="programTaskChart"></div>
            </div>
        </div>
    </div>
</div>

@assets
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endassets

@script
<script>
    const chartData = @js($chartData);
    const isDarkMode = document.documentElement.classList.contains('dark');

    const options = {
        series: [{
            name: 'Program',
            data: chartData.programs
        }, {
            name: 'Tugas',
            data: chartData.tasks
        }],
        chart: {
            type: 'bar',
            height: 350,
            toolbar: {
                show: false
            },
            background: 'transparent',
            fontFamily: 'inherit',
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '60%',
                borderRadius: 6,
                dataLabels: {
                    position: 'top',
                }
            },
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            categories: chartData.labels,
            labels: {
                style: {
                    colors: isDarkMode ? '#9ca3af' : '#6b7280',
                    fontSize: '12px'
                }
            },
            axisBorder: {
                color: isDarkMode ? '#374151' : '#e5e7eb'
            },
            axisTicks: {
                color: isDarkMode ? '#374151' : '#e5e7eb'
            }
        },
        yaxis: {
            title: {
                text: 'Jumlah',
                style: {
                    color: isDarkMode ? '#9ca3af' : '#6b7280',
                    fontSize: '12px',
                    fontWeight: 500
                }
            },
            labels: {
                style: {
                    colors: isDarkMode ? '#9ca3af' : '#6b7280',
                    fontSize: '12px'
                }
            }
        },
        fill: {
            opacity: 1
        },
        tooltip: {
            theme: isDarkMode ? 'dark' : 'light',
            y: {
                formatter: function (val) {
                    return val + " item"
                }
            }
        },
        colors: ['#22c55e', '#3b82f6'],
        legend: {
            show: true,
            position: 'top',
            horizontalAlign: 'right',
            labels: {
                colors: isDarkMode ? '#d1d5db' : '#374151'
            },
            markers: {
                width: 12,
                height: 12,
                radius: 3
            }
        },
        grid: {
            borderColor: isDarkMode ? '#374151' : '#e5e7eb',
            strokeDashArray: 4,
        }
    };

    const chart = new ApexCharts(document.querySelector("#programTaskChart"), options);
    chart.render();

    // Listen for theme changes
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.attributeName === 'class') {
                const isDark = document.documentElement.classList.contains('dark');
                chart.updateOptions({
                    xaxis: {
                        labels: {
                            style: {
                                colors: isDark ? '#9ca3af' : '#6b7280'
                            }
                        },
                        axisBorder: {
                            color: isDark ? '#374151' : '#e5e7eb'
                        },
                        axisTicks: {
                            color: isDark ? '#374151' : '#e5e7eb'
                        }
                    },
                    yaxis: {
                        title: {
                            style: {
                                color: isDark ? '#9ca3af' : '#6b7280'
                            }
                        },
                        labels: {
                            style: {
                                colors: isDark ? '#9ca3af' : '#6b7280'
                            }
                        }
                    },
                    legend: {
                        labels: {
                            colors: isDark ? '#d1d5db' : '#374151'
                        }
                    },
                    grid: {
                        borderColor: isDark ? '#374151' : '#e5e7eb'
                    },
                    tooltip: {
                        theme: isDark ? 'dark' : 'light'
                    }
                });
            }
        });
    });

    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class']
    });
</script>
@endscript
