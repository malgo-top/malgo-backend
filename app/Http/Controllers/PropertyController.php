<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Property;
use Illuminate\Support\Facades\Redis;

class PropertyController extends Controller
{
    public function getAllProperties(){

        return response()->json([
            'success' => true,
            'data' => Property::all()->map(function ($property) {
                $property->accepting_applications = $property->accepting_applications == 1;
                return $property;
            })
        ]);

    }

    public function switchPropertyApplications(Request $request){

        $property = Property::find($request->id);

        $property->accepting_applications = !$property->accepting_applications;
        $property->save();

        return response()->json([
            'success' => true,
            "data" => $property->accepting_applications
        ]);

    }

    public function checkIfPropertyApplicationOpen(Request $request){

        $property = Property::whereEncrypted("sku", $request->sku)->first();

        if (!$property) {
            return response()->json([
                'success' => false,
            ]);
        }

        return response()->json([
            'success' => $property->accepting_applications,
        ]);
    }

}
