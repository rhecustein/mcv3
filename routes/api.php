<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Http\Controllers\Api\PatientSearchController;

Route::get('/patients/search', function (Request $request) {
    $query = $request->q;
    return Patient::select('id', 'name', 'gender', 'phone as phone_number', 'birth_date as dob', 'address')
        ->where('name', 'like', "%{$query}%")
        ->limit(10)
        ->get();
});

