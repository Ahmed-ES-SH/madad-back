<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Services\ImagesUploadService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{

    protected $ImagesUploadService;

    public function __construct(ImagesUploadService $imagesUploadService)
    {
        $this->ImagesUploadService = $imagesUploadService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $services = Service::orderBy('created_at', 'desc')->paginate(12);
            return response()->json([
                'data' => $services->items(),
                'total' => $services->total(),
                'per_page' => $services->perPage(),
                'current_page' => $services->currentPage(),
                'last_page' => $services->lastPage(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'features' => 'required|array',
                'expected_benefit_percentage' => 'required|numeric',
                'starting_price' => 'required|numeric',
                'images' => 'required|array',
                'sub_category_id' => 'required|exists:sub_categories,id',
            ]);

            if ($validation->fails()) {
                return response()->json(['errors' => $validation->errors()], 422);
            }

            $service = new Service();
            $service->fill($request->only(['title', 'description', 'features', 'expected_benefit_percentage', 'starting_price', 'sub_category_id']));

            $this->ImagesUploadService->uploadImages($request, $service, 'services/'); // رفع الصور

            $service->save();

            return response()->json(['message' => 'Service created successfully', 'data' => $service], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $service = Service::findeOrFail($id);
        return response()->json(['data' => $service], 200);
    }




    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $validation = Validator::make($request->all(), [
                'title' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'features' => 'nullable|array',
                'expected_benefit_percentage' => 'nullable|numeric',
                'starting_price' => 'nullable|numeric',
                'images' => 'nullable|array',
            ]);

            if ($validation->fails()) {
                return response()->json(['errors' => $validation->errors()], 422);
            }

            $service = Service::findOrFail($id);

            $service->fill($request->only(['title', 'description', 'features', 'expected_benefit_percentage', 'starting_price']));

            if ($request->hasFile('images')) {
                // يمكنك إضافة كود لحذف الصور القديمة هنا إذا لزم الأمر
                $this->ImagesUploadService->uploadImages($request, $service, 'services/'); // رفع الصور
            }

            $service->save();

            return response()->json(['message' => 'Service updated successfully', 'data' => $service], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $service = Service::findOrFail($id);
            // حذف الصور القديمة إذا كانت موجودة
            if ($service->images) {
                foreach (json_decode($service->images) as $image) {
                    Storage::disk('public')->delete($image);
                }
            }

            $service->delete();
            return response()->json(['message' => 'Service deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
