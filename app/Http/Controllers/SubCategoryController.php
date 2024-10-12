<?php

namespace App\Http\Controllers;

use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Services\ImageUploadService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;

class SubCategoryController extends Controller
{
    protected $imageUploadService;

    public function __construct(ImageUploadService $imageuploadservice)
    {
        $this->imageUploadService = $imageuploadservice;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $subCategories = SubCategory::orderBy('created_at', 'desc')->paginate(10);
            return response()->json([
                'data' => $subCategories->items(),
                'total' => $subCategories->total(),
                'per_page' => $subCategories->perPage(),
                'current_page' => $subCategories->currentPage(),
                'last_page' => $subCategories->lastPage(),
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
                'name' => 'required|string|max:255|unique:sub_categories',
                'description' => 'nullable|string',
                'image' => 'nullable|image',
                'category_id' => 'required|exists:categories,id',
            ]);

            if ($validation->fails()) {
                return response()->json(['errors' => $validation->errors()], 422);
            }

            $subCategory = new SubCategory();
            $subCategory->fill($request->only(['name', 'description', 'category_id']));

            $this->imageUploadService->uploadImage($request, $subCategory, 'sub_categories/' . $subCategory->id);

            $subCategory->save();

            return response()->json(['message' => 'SubCategory created successfully', 'data' => $subCategory], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $subCategory = SubCategory::findeOrFail($id);
        return response()->json(['data' => $subCategory]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {

        $subCategory = SubCategory::findeOrFail($id);
        try {
            $validation = Validator::make($request->all(), [
                'name' => 'nullable|string|max:255|unique:sub_categories,name,' . $subCategory->id,
                'description' => 'nullable|string',
                'image' => 'nullable|image',
                'category_id' => 'required|exists:categories,id',
            ]);

            if ($validation->fails()) {
                return response()->json(['errors' => $validation->errors()], 422);
            }

            $subCategory->fill($request->only(['name', 'description', 'category_id']));

            // حذف الصورة القديمة إذا كانت موجودة
            if ($request->hasFile('image')) {
                if ($subCategory->image) {
                    Storage::disk('public')->delete($subCategory->image);
                }
                $this->imageUploadService->uploadImage($request, $subCategory, 'sub_categories/' . $subCategory->id);
            }

            $subCategory->save();

            return response()->json(['message' => 'SubCategory updated successfully', 'data' => $subCategory], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'SubCategory not found'], 404);
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
            $subCategory = SubCategory::findeOrFail($id);
            // حذف الصورة إذا كانت موجودة
            if ($subCategory->image) {
                Storage::disk('public')->delete($subCategory->image);
            }
            $subCategory->delete();
            return response()->json(['message' => 'SubCategory deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'SubCategory not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
