<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;

class PatientSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = trim($request->get('q'));

        if (!$query || strlen($query) < 1) {
            return response()->json([]);
        }

        $patients = Patient::with('user')
            ->whereHas('user', function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%');
            })
            ->limit(10)
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->user->name,
                    'dob' => $p->birth_date,
                    'gender' => $p->gender,
                    'phone_number' => $p->phone,
                    'address' => $p->address,
                ];
            });

        return response()->json($patients);
    }
}
