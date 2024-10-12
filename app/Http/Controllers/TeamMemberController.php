<?php

namespace App\Http\Controllers;

use App\Models\TeamMember;
use Illuminate\Http\Request;
use App\Services\ImageUploadService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TeamMemberController extends Controller
{


    protected $imageUploadService;

    public function __construct(ImageUploadService $imageUploadService)
    {
        $this->imageUploadService = $imageUploadService; // حقن الـ Service
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $Members = TeamMember::orderBy('created_at', 'desc')->paginate(15);
            return response()->json([
                'data' => $Members->items(),  // العناصر الحالية فقط
                'total' => $Members->total(), // إجمالي العناصر
                'per_page' => $Members->perPage(), // عدد العناصر في كل صفحة
                'current_page' => $Members->currentPage(), // الصفحة الحالية
                'last_page' => $Members->lastPage(), // آخر صفحة
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $validation = Validator::make($request->all(), [
                'name' => 'required',
                'image' => 'nullable',
                'description' => 'nullable',
                'position' => 'required',
                'facebook' => 'nullable|string',
                'X_Account' => 'nullable|string',
                'instagram' => 'nullable|string',
            ]);

            if ($validation->fails()) {
                return response()->json([
                    'errors' => $validation->errors()
                ], 422);
            }

            $member = new TeamMember();

            $member->fill($request->only([
                'name',
                'description',
                'position',
                'facebook',
                'X_Account',
                'instagram',
            ]));

            $this->imageUploadService->uploadImage($request, $member, 'TeamMembers/' . $member->id);

            $member->save();

            return response()->json([
                'Message' => 'Success Add New Memeber ',
                'data' => $member
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $member = TeamMember::findOrFail($id); // استرجاع العضو بناءً على ID

            return response()->json([
                'data' => $member
            ], 200); // إرجاع البيانات مع حالة 200 عند النجاح
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Member not found.'
            ], 404); // إرجاع حالة 404 إذا لم يتم العثور على العضو
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500); // إرجاع حالة 500 في حالة حدوث خطأ
        }
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            // التحقق من صحة البيانات
            $validation = Validator::make($request->all(), [
                'name' => 'required|string',
                'image' => 'nullable|image',
                'description' => 'nullable|string',
                'position' => 'required|string',
                'facebook' => 'nullable|string',
                'X_Account' => 'nullable|string',
                'instagram' => 'nullable|string',
            ]);

            if ($validation->fails()) {
                return response()->json([
                    'errors' => $validation->errors()
                ], 422);
            }

            $member = TeamMember::findOrFail($id); // استرجاع العضو بناءً على ID

            // مسح الصورة القديمة إذا كانت موجودة
            if ($request->hasFile('image') && $member->image) {
                Storage::disk('public')->delete($member->image); // حذف الصورة القديمة من التخزين
            }

            // ملء الحقول من الطلب
            $member->fill($request->only([
                'name',
                'description',
                'position',
                'facebook',
                'X_Account',
                'instagram',
            ]));

            // رفع الصورة الجديدة فقط إذا كانت موجودة

            $this->imageUploadService->uploadImage($request, $member, 'TeamMembers/' . $member->id);


            $member->save(); // حفظ التغييرات في قاعدة البيانات

            return response()->json([
                'message' => 'Successfully updated member.',
                'data' => $member
            ], 200); // إرجاع الحالة 200 عند النجاح
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Member not found.'
            ], 404); // إرجاع حالة 404 إذا لم يتم العثور على العضو
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500); // إرجاع حالة 500 في حالة حدوث خطأ
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $member = TeamMember::findOrFail($id); // استرجاع العضو بناءً على ID

            // حذف الصورة القديمة إذا كانت موجودة
            if ($member->image) {
                Storage::disk('public')->delete($member->image); // حذف الصورة من التخزين
            }

            $member->delete(); // حذف العضو من قاعدة البيانات

            return response()->json([
                'message' => 'Member deleted successfully.'
            ], 200); // إرجاع رسالة نجاح مع حالة 200
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Member not found.'
            ], 404); // إرجاع حالة 404 إذا لم يتم العثور على العضو
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500); // إرجاع حالة 500 في حالة حدوث خطأ
        }
    }
}
