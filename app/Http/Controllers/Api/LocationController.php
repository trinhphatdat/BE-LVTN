<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GhnService;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    private $ghnService;

    public function __construct(GhnService $ghnService)
    {
        $this->ghnService = $ghnService;
    }

    public function getProvinces()
    {
        $response = $this->ghnService->getProvinces();
        return response()->json($response->json());
    }

    public function getDistricts(Request $request)
    {
        $response = $this->ghnService->getDistricts($request->province_id);
        return response()->json($response->json());
    }

    public function getWards(Request $request)
    {
        $response = $this->ghnService->getWards($request->district_id);
        return response()->json($response->json());
    }

    /**
     * Tính phí vận chuyển
     */
    public function calculateShippingFee(Request $request)
    {
        $request->validate([
            'to_district_id' => 'required|integer',
            'to_ward_code' => 'required|string',
            'insurance_value' => 'nullable|numeric',
            'weight' => 'nullable|integer',
        ]);

        try {
            $response = $this->ghnService->calculateShippingFee([
                'to_district_id' => (int)$request->to_district_id,
                'to_ward_code' => (string)$request->to_ward_code,
                'insurance_value' => (int)($request->insurance_value ?? 0),
                'weight' => (int)($request->weight ?? 200),
                'service_type_id' => (int)($request->service_type_id ?? 2),
            ]);

            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể tính phí vận chuyển',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
