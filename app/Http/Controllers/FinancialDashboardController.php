<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Models\OfferingType;
use App\Models\ServiceOffering;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancialDashboardController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));

        // 1. Contributions (Dízimos/Ofertas de Membros)
        $contributionsByType = Contribution::whereMonth('contribution_date', $month)
            ->whereYear('contribution_date', $year)
            ->where('status', 'verificada')
            ->select('offering_type_id', DB::raw('SUM(amount) as total'))
            ->groupBy('offering_type_id')
            ->with('offeringType')
            ->get();

        // 2. Service Offerings (Ofertas de Cultos)
        $serviceOfferingsByType = ServiceOffering::whereHas('service', function ($q) use ($month, $year) {
            $q->whereMonth('date', $month)->whereYear('date', $year);
        })
            ->select('offering_type_id', DB::raw('SUM(amount) as total'))
            ->groupBy('offering_type_id')
            ->with('offeringType')
            ->get();

        // 3. Consolidate
        $totals = [];
        $offeringTypes = OfferingType::where('is_active', true)->orderBy('order')->get();

        foreach ($offeringTypes as $type) {
            $contributionTotal = $contributionsByType->where('offering_type_id', $type->id)->first()->total ?? 0;
            $serviceTotal = $serviceOfferingsByType->where('offering_type_id', $type->id)->first()->total ?? 0;

            if ($contributionTotal > 0 || $serviceTotal > 0) {
                $totals[] = [
                    'type' => $type->name,
                    'contributions' => $contributionTotal,
                    'services' => $serviceTotal,
                    'total' => $contributionTotal + $serviceTotal
                ];
            }
        }

        $grandTotal = collect($totals)->sum('total');

        return view('financial_dashboard.index', compact('totals', 'grandTotal', 'month', 'year'));
    }
}
