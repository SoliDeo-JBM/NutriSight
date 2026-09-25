<?php

namespace App\Http\Controllers;

use App\Models\PhilippineBarangay;
use App\Models\PhilippineMunicipality;
use App\Models\PhilippineProvince;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PhilippineLocationController extends Controller
{
    public function provinces(): JsonResponse
    {
        return response()->json(PhilippineProvince::query()->orderBy('name')->get(['code', 'name']));
    }

    public function municipalities(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'province_code' => ['required', 'string', 'exists:ph_provinces,code'],
        ]);

        return response()->json(
            PhilippineMunicipality::query()
                ->where('province_code', $validated['province_code'])
                ->orderBy('name')
                ->get(['code', 'name'])
        );
    }

    public function barangays(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'municipality_code' => ['required', 'string', 'exists:ph_municipalities,code'],
        ]);

        $municipalityCode = $validated['municipality_code'];
        $barangayCodePrefix = substr($municipalityCode, 0, -3);

        return response()->json(
            PhilippineBarangay::query()
                ->where(function ($query) use ($municipalityCode, $barangayCodePrefix) {
                    $query->where('municipality_code', $municipalityCode)
                        ->orWhere('code', 'like', $barangayCodePrefix . '%');
                })
                ->orderBy('name')
                ->get(['code', 'name'])
        );
    }
}