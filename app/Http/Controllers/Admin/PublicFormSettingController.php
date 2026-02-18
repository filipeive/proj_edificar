<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Setting;
use Illuminate\Http\Request;

class PublicFormSettingController extends Controller
{
    public function index()
    {
        $courses = Course::orderBy('name')->get();
        $preMaritalCourseId = Setting::get('pre_marital_course_id');
        $ministerialCourseIds = Setting::get('ministerial_public_courses', []);

        return view('admin.settings.public-forms', compact('courses', 'preMaritalCourseId', 'ministerialCourseIds'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pre_marital_course_id' => 'required|exists:courses,id',
            'ministerial_course_ids' => 'nullable|array',
            'ministerial_course_ids.*' => 'exists:courses,id',
        ]);

        Setting::set('pre_marital_course_id', $validated['pre_marital_course_id'], 'integer', 'public_forms');
        Setting::set('ministerial_public_courses', $validated['ministerial_course_ids'] ?? [], 'json', 'public_forms');

        return back()->with('success', 'Configurações de formulários públicos atualizadas com sucesso!');
    }
}
