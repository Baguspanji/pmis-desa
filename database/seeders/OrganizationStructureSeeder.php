<?php

namespace Database\Seeders;

use App\Models\OrganizationStructure;
use Illuminate\Database\Seeder;

class OrganizationStructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pemerintah Desa Structure
        $kepala = OrganizationStructure::create([
            'name' => 'Anam Syaifudin',
            'position' => 'Kepala Desa',
            'organization_type' => 'Pemerintah Desa',
            'level' => 'head',
            'order' => 1,
            'description' => 'Kepala Desa (Village Head)',
        ]);

        // Sekretaris Desa
        OrganizationStructure::create([
            'name' => 'Nama Perangkat',
            'position' => 'Sekretaris Desa',
            'organization_type' => 'Pemerintah Desa',
            'level' => 'vice',
            'parent_id' => $kepala->id,
            'order' => 1,
            'description' => 'Sekretaris Desa (Village Secretary)',
        ]);

        // Bendahara Desa
        OrganizationStructure::create([
            'name' => 'Nama Perangkat',
            'position' => 'Bendahara Desa',
            'organization_type' => 'Pemerintah Desa',
            'level' => 'vice',
            'parent_id' => $kepala->id,
            'order' => 2,
            'description' => 'Bendahara Desa (Village Treasurer)',
        ]);

        // Staff Members
        for ($i = 1; $i <= 4; $i++) {
            OrganizationStructure::create([
                'name' => 'Nama Staff '.$i,
                'position' => 'Staff Desa',
                'organization_type' => 'Pemerintah Desa',
                'level' => 'staff',
                'parent_id' => $kepala->id,
                'order' => $i + 2,
                'description' => 'Staff Desa (Village Staff)',
            ]);
        }

        // Badan Permasyarakatan Desa Structure
        $ketua = OrganizationStructure::create([
            'name' => 'Mark Tom',
            'position' => 'Ketua',
            'organization_type' => 'Badan Permasyarakatan Desa',
            'level' => 'head',
            'order' => 1,
            'description' => 'Ketua (Chairman)',
        ]);

        // Vice Leaders
        $viceLeaders = [
            ['name' => 'Tom Hiddles', 'order' => 1],
            ['name' => 'Frankie James', 'order' => 2],
            ['name' => 'Ella Linda', 'order' => 3],
        ];

        foreach ($viceLeaders as $vice) {
            OrganizationStructure::create([
                'name' => $vice['name'],
                'position' => 'Wakil Ketua',
                'organization_type' => 'Badan Permasyarakatan Desa',
                'level' => 'vice',
                'parent_id' => $ketua->id,
                'order' => $vice['order'],
                'description' => 'Wakil Ketua (Vice Chairman)',
            ]);
        }

        // Members
        for ($i = 1; $i <= 4; $i++) {
            OrganizationStructure::create([
                'name' => 'Member '.$i,
                'position' => 'Anggota',
                'organization_type' => 'Badan Permasyarakatan Desa',
                'level' => 'member',
                'parent_id' => $ketua->id,
                'order' => $i,
                'description' => 'Anggota (Member)',
            ]);
        }
    }
}
