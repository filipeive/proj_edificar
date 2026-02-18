<?php

namespace App\Http\Controllers;

use App\Models\CoupleEnrollment;
use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Zone;
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
                    ->orWhere('contacts', 'like', "%{$search}%")
                    ->orWhere('husband_phone', 'like', "%{$search}%")
                    ->orWhere('wife_phone', 'like', "%{$search}%");
            });
        }

        $enrollments = $query->paginate(20)->withQueryString();
        $courses = Course::orderBy('name')->get();
        $classes = CourseClass::orderBy('name')->get();

        return view('couple_enrollments.index', compact('enrollments', 'courses', 'classes'));
    }

    public function show(CoupleEnrollment $coupleEnrollment)
    {
        return view('couple_enrollments.show', compact('coupleEnrollment'));
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
                'Endereço Parceiro',
                'Endereço Parceira',
                'Tel. Parceiro',
                'Tel. Parceira',
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
                    $enrollment->wife_address ?? '',
                    $enrollment->husband_phone ?? '',
                    $enrollment->wife_phone ?? '',
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
    public function edit(CoupleEnrollment $coupleEnrollment)
    {
        $courses = Course::orderBy('name')->get();
        $zones = Zone::orderBy('name')->get();
        return view('couple_enrollments.edit', compact('coupleEnrollment', 'courses', 'zones'));
    }

    public function update(Request $request, CoupleEnrollment $coupleEnrollment)
    {
        $validated = $request->validate([
            'husband_name' => 'required|string|max:255',
            'wife_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'husband_phone' => 'nullable|string|max:30',
            'wife_phone' => 'nullable|string|max:30',
            'address' => 'required|string|max:255',
            'wife_address' => 'nullable|string|max:255',
            'contacts' => 'nullable|string|max:255',
            'relationship_type' => 'required|in:namoro,noivos,vivendo_maritalmente,casados',
            'years_together' => 'required|integer|min:0',
            'cell_zone' => 'nullable|string|max:255',
            'leader_name' => 'nullable|string|max:255',
            'is_church_member' => 'required|boolean',
            'has_pastoral_recommendation' => 'required|boolean',
            'observations' => 'nullable|string',
            'course_id' => 'required|exists:courses,id',
        ]);

        $coupleEnrollment->update($validated);

        return redirect()->route('couple-enrollments.index')->with('success', 'Inscrição atualizada com sucesso!');
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
