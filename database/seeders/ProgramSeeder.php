<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\Task;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed programs, projects, tasks, etc. here
        Program::create([
            'program_name' => 'Realisasi Pembuatan dan Penempatan Sarana Air Bersih',
            'program_description' => 'Sebuah program yang berfokus pada penyediaan sarana air bersih untuk masyarakat desa.',
            'location' => 'Desa Sukamaju',
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31',
            'pic_user_id' => null,
            'total_budget' => 500000000.00,
            'status' => 'planned',
            'created_by' => 2,
        ]);

        Task::create([
            'task_name' => 'Survey Lokasi Pemasangan Sarana Air Bersih',
            'task_description' => 'Melakukan survey di beberapa lokasi potensial untuk pemasangan sarana air bersih.',
            'program_id' => 1,
            'parent_task_id' => null,
            'assigned_user_id' => 4,
            'status' => 'not_started',
            'progress_type' => 'percentage',
            'priority' => 'high',
            'start_date' => '2024-01-05',
            'end_date' => '2024-01-20',
            'estimated_budget' => 10000000.00,
        ]);

        Task::create([
            'task_name' => 'Pengadaan Peralatan dan Bahan',
            'task_description' => 'Membeli semua peralatan dan bahan yang diperlukan untuk pembuatan sarana air bersih.',
            'program_id' => 1,
            'parent_task_id' => null,
            'assigned_user_id' => 5,
            'status' => 'not_started',
            'progress_type' => 'percentage',
            'priority' => 'medium',
            'start_date' => '2024-01-21',
            'end_date' => '2024-02-10',
            'estimated_budget' => 200000000.00,
        ]);

        Task::create([
            'task_name' => 'Pembuatan dan Pemasangan Sarana Air Bersih',
            'task_description' => 'Melaksanakan pembuatan dan pemasangan sarana air bersih di lokasi yang telah disurvey.',
            'program_id' => 1,
            'parent_task_id' => null,
            'assigned_user_id' => 6,
            'status' => 'not_started',
            'progress_type' => 'percentage',
            'priority' => 'high',
            'start_date' => '2024-02-11',
            'end_date' => '2024-04-30',
            'estimated_budget' => 250000000.00,
        ]);

        Task::create([
            'task_name' => 'Sosialisasi dan Pelatihan Penggunaan Sarana Air Bersih',
            'task_description' => 'Mengadakan sosialisasi dan pelatihan kepada masyarakat tentang penggunaan dan pemeliharaan sarana air bersih.',
            'program_id' => 1,
            'parent_task_id' => null,
            'assigned_user_id' => 7,
            'status' => 'not_started',
            'progress_type' => 'percentage',
            'priority' => 'low',
            'start_date' => '2024-05-01',
            'end_date' => '2024-05-31',
            'estimated_budget' => 5000000.00,
        ]);

        Program::create([
            'program_name' => 'Sensus Penduduk dan Pemetaan Wilayah Desa',
            'program_description' => 'Program untuk melakukan sensus penduduk dan pemetaan wilayah guna mendukung perencanaan pembangunan desa.',
            'location' => 'Desa Mekar Jaya',
            'start_date' => '2024-02-01',
            'end_date' => '2024-11-30',
            'pic_user_id' => 4,
            'total_budget' => 750000000.00,
            'status' => 'planned',
            'created_by' => 2,
        ]);
    }
}
