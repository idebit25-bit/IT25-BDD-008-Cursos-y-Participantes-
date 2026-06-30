<?php

namespace App\Http\Controllers;

use App\Models\CourseActionLog;
use Illuminate\Http\Request;

class CourseActionLogController extends Controller
{
    public function index()
    {
        $logs = CourseActionLog::with(['curso', 'user'])->get();
        return view('course-action-logs.index', compact('logs'));
    }
    public function show($id)
    {
        // Buscar el registro por ID
        $log = CourseActionLog::findOrFail($id);

        // Pasar el log a la vista
        return view('course-action-logs.show', compact('log'));
    }
}
