<?php

namespace App\Http\Controllers;

use App\Models\CoupleEnrollment;
use App\Models\Course;
use App\Models\CourseClass;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CoupleEnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $query = CoupleEnrollment::with(['course', 'courseClass'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('husband_name', 'like', "%{$search}%")
                    ->orWhere('wife_name', 'like', "%{$search}%")
                    ->orWhere('contacts', 'like', "%{$search}%");
            });
        }

        $enrollments = $query->paginate(20)->withQueryString();
        $courses = Course::orderBy('name')->get();
        $classes = CourseClass::orderBy('name')->get();

        return view('couple_enrollments.index', compact('enrollments', 'courses', 'classes'));
    }

    public function assignClass(Request $request, CoupleEnrollment $coupleEnrollment)
    {
        $validated = $request->validate([
            'course_class_id' => 'required|exists:course_classes,id',
        ]);

        $courseClass = CourseClass::findOrFail($validated['course_class_id']);

        if ($courseClass->course_id !== $coupleEnrollment->course_id) {
            return back()->with('error', 'Esta turma não pertence ao curso da inscrição.');
        }

        $coupleEnrollment->update([
            'course_class_id' => $courseClass->id,
            'status' => 'approved',
        ]);

        return back()->with('success', 'Casal alocado à turma com sucesso!');
    }

    public function destroy(CoupleEnrollment $coupleEnrollment)
    {
        $coupleEnrollment->delete();
        return back()->with('success', 'Inscrição removida com sucesso!');
    }

    public function export(Request $request)
    {
        $query = CoupleEnrollment::with(['course', 'courseClass']);

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $enrollments = $query->get();

        $response = new StreamedResponse(function () use ($enrollments) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'ID',
                'Curso',
                'Marido',
                'Esposa',
                'Tipo Relação',
                'Endereço',
                'Contatos',
                'Célula/Zona',
                'Anos Juntos',
                'Líder',
                'Membro?',
                'Turma',
                'Status',
                'Data Inscrição'
            ]);

            foreach ($enrollments as $enrollment) {
                fputcsv($handle, [
                    $enrollment->id,
                    $enrollment->course->name,
                    $enrollment->husband_name,
                    $enrollment->wife_name,
                    $enrollment->relationship_type,
                    $enrollment->address,
                    $enrollment->contacts,
                    $enrollment->cell_zone,
                    $enrollment->years_together,
                    $enrollment->leader_name,
                    $enrollment->is_church_member ? 'Sim' : 'Não',
                    $enrollment->courseClass->name ?? 'Não alocado',
                    $enrollment->status,
                    $enrollment->created_at->format('d/m/Y H:i')
                ]);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="inscricoes_publicas_' . date('Ymd_His') . '.csv"');

        return $response;
    }
    public function updateStatus(Request $request, CoupleEnrollment $coupleEnrollment)
    {
        $validated = $request->validate([
            'status' => 'required|in:cursando,aprovado,reprovado,desistente,enrolled,completed,dropped,pending',
        ]);

        $coupleEnrollment->update($validated);

        return back()->with('success', 'Status da matrícula atualizado!');
    }
}
