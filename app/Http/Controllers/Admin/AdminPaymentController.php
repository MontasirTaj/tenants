<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminPaymentController extends Controller
{
    public function index(Request $request)
    {
        $plan = $request->query('plan');

        $query = Payment::query()->orderByDesc('id');
        if ($plan) {
            $query->where('plan', $plan);
        }
        $payments = $query->limit(500)->get();

        $totalsByPlan = Payment::select('plan', DB::raw('SUM(amount_total) as total'))
            ->groupBy('plan')
            ->orderBy('plan')
            ->get();

        return view('admin.payments.index', [
            'payments' => $payments,
            'totalsByPlan' => $totalsByPlan,
            'currentPlan' => $plan,
        ]);
    }
}
