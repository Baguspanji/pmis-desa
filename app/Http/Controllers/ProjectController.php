<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function getProjects()
    {
        $search = request()->query('search');

        $query = Program::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('program_name', 'like', '%' . $search . '%');
            });
        }

        $tasks = $query->latest()->paginate(15);

        return $this->responsePaginate(
            data: $tasks,
            message: 'Berhasil mendapatkan data program'
        );
    }
}
