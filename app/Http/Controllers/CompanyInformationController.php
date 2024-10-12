<?php

namespace App\Http\Controllers;

use App\Models\CompanyInformation;
use Illuminate\Http\Request;
use App\Services\ImageUploadService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CompanyInformationController extends Controller
{

    protected $imageUploadService;

    public function __construct(ImageUploadService $imageUploadService)
    {
        $this->imageUploadService = $imageUploadService; // حقن الـ Service
    }


    public function index()
    {
        try {

            $all_details = CompanyInformation::orderBy('created_at', 'desc')->paginate(8);

            return response()->json([
                'data' => $all_details->items(),  // العناصر الحالية فقط
                'total' => $all_details->total(), // إجمالي العناصر
                'per_page' => $all_details->perPage(), // عدد العناصر في كل صفحة
                'current_page' => $all_details->currentPage(), // الصفحة الحالية
                'last_page' => $all_details->lastPage(), // آخر صفحة
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }



    public function store(Request $request)
    {
        try {
            // التحقق من صحة البيانات
            $validation = Validator::make($request->all(), [
                'name' => 'nullable|string',
                'vision' => 'nullable|string',
                'goals' => 'nullable|string',
                'values' => 'nullable|string',
                'address' => 'nullable|string',
                'vision_image' => 'nullable|image',
                'goals_image' => 'nullable|image',
                'values_image' => 'nullable|image',
            ]);


            if ($validation->fails()) {
                return response()->json([
                    'errors' => $validation->errors()
                ]);
            }

            $CompanyInformation = new CompanyInformation();

            $CompanyInformation->fill($request->only(['name', 'vision', 'address',  'goals', 'values']));

            // استخدام خدمة رفع الصور
            $this->imageUploadService->uploadImage($request, $CompanyInformation, 'company/vision');
            $this->imageUploadService->uploadImage($request, $CompanyInformation, 'company/goals');
            $this->imageUploadService->uploadImage($request, $CompanyInformation, 'company/values');

            $CompanyInformation->save();

            return response()->json(['message' => 'Company info saved successfully', 'data' => $CompanyInformation], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }


    public function show($id)
    {
        try {
            $details = CompanyInformation::findOrFail($id);

            return response()->json([
                'data' => $details
            ], 200); // كود الحالة 200 (نجاح)
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Company information not found'
            ], 404); // كود الحالة 404 (غير موجود)
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500); // كود الحالة 500 (خطأ داخلي)
        }
    }


    public function update(Request $request, $id)
    {
        try {
            // التحقق من صحة البيانات
            $validation = Validator::make($request->all(), [
                'name' => 'sometimes|string',
                'vision' => 'sometimes|string',
                'goals' => 'sometimes|string',
                'values' => 'sometimes|string',
                'address' => 'sometimes|string',
                'vision_image' => 'sometimes|image',
                'goals_image' => 'sometimes|image',
                'values_image' => 'sometimes|image',
            ]);

            if ($validation->fails()) {
                return response()->json([
                    'errors' => $validation->errors()
                ], 422);
            }

            // البحث عن معلومات الشركة
            $companyInformation = CompanyInformation::findOrFail($id);

            // ملء الحقول باستخدام fill
            $companyInformation->fill($request->only(['name', 'vision', 'address',  'goals', 'values']));


            // مسح الصور القديمة إذا كانت موجودة
            $imageFields = [
                'vision_image',
                'goals_image',
                'values_image'
            ];

            foreach ($imageFields as $field) {
                if ($request->hasFile($field) && $companyInformation->{$field}) {
                    Storage::disk('public')->delete($companyInformation->{$field});
                }
            }

            // استخدام خدمة رفع الصور
            $this->imageUploadService->uploadImage($request, $companyInformation, 'company/vision_image');
            $this->imageUploadService->uploadImage($request, $companyInformation, 'company/goals_image');
            $this->imageUploadService->uploadImage($request, $companyInformation, 'company/values_image');

            // حفظ التحديثات
            $companyInformation->save();

            return response()->json(['message' => 'Company info updated successfully', 'data' => $companyInformation], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }


    public function destroy($id)
    {
        try {
            // البحث عن معلومات الشركة بناءً على المعرف
            $companyInformation = CompanyInformation::findOrFail($id);

            // قائمة الحقول التي تحتوي على الصور
            $imageFields = ['vision_image', 'goals_image', 'values_image'];

            // حذف الصور القديمة إذا كانت موجودة
            foreach ($imageFields as $field) {
                if ($companyInformation->$field) {
                    Storage::disk('public')->delete($companyInformation->$field);
                }
            }

            // حذف معلومات الشركة
            $companyInformation->delete();

            return response()->json([
                'message' => 'Company information deleted successfully'
            ], 200); // كود الحالة 200 للإشارة إلى النجاح
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500); // كود الحالة 500 للأخطاء العامة
        }
    }
}
