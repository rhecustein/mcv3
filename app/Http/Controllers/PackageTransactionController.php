<?php

namespace App\Http\Controllers;

use App\Models\PackageTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PackageTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = PackageTransaction::with(['company', 'outlet', 'package', 'creator']);

        if ($request->filled('search')) {
            $query->whereHas('company', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

        $transactions = $query->latest()->paginate(15);
        $totalTransactions = PackageTransaction::count();

        $totalThisMonth = PackageTransaction::whereMonth('created_at', now()->month)->sum('amount');
        $totalAllTime = PackageTransaction::sum('amount');

        return view('superadmin.package-transactions.index', compact(
            'transactions', 'totalTransactions', 'totalThisMonth', 'totalAllTime'
        ));
    }

    public function show(PackageTransaction $packageTransaction)
    {
        return view('superadmin.package-transactions.show', compact('packageTransaction'));
    }

    public function destroy(PackageTransaction $packageTransaction)
    {
        $packageTransaction->delete();
        return redirect()->route('package-transactions.index')->with('success', 'Transaksi berhasil dihapus.');
    }
}
