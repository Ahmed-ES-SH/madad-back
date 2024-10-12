<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class UsersController extends Controller
{


    protected $imageUploadService;

    public function __construct(ImageUploadService $imageUploadService)
    {
        $this->imageUploadService = $imageUploadService; // حقن الـ Service
    }


    public function index()
    {
        try {
            // تصحيح orderBy و paginate
            $users = User::orderBy('created_at', 'desc')->paginate(18);

            return response()->json([
                'data' => $users->items(),  // العناصر الحالية فقط
                'total' => $users->total(), // إجمالي العناصر
                'per_page' => $users->perPage(), // عدد العناصر في كل صفحة
                'current_page' => $users->currentPage(), // الصفحة الحالية
                'last_page' => $users->lastPage(), // آخر صفحة
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }




    public function update(Request $request, $id)
    {
        try {
            // التحقق من وجود المستخدم
            $user = User::findOrFail($id);

            // التحقق من صحة البيانات
            $validation = Validator::make($request->all(), [
                'name' => 'sometimes|required|unique:users,name,' . $user->id, // استخدام sometimes لتجنب الخطأ إذا لم يتم تقديم الاسم
                'email' => 'sometimes|required|email|unique:users,email,' . $user->id, // استخدام sometimes
                'password' => 'sometimes|nullable|min:6|max:52', // السماح بكلمة مرور فارغة
                'phone_number' => 'nullable',
                'image' => 'nullable|file|image|max:2048', // تحديد الحد الأقصى لحجم الصورة
            ]);

            if ($validation->fails()) {
                return response()->json([
                    'message' => $validation->errors()
                ], 422);
            }

            $user->fill($request->only(['name', 'email', 'phone_number']));
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            // رفع الصورة إذا تم إرسالها
            $this->imageUploadService->uploadImage($request, $user, 'users/' . $user->id);

            // حفظ التحديثات في قاعدة البيانات
            $user->save();

            // استجابة النجاح
            return response()->json([
                'message' => 'User updated successfully',
                'user' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function show($id)
    {
        try {
            // البحث عن المستخدم بناءً على المعرف
            $user = User::findOrFail($id);

            // استجابة النجاح
            return response()->json([
                'data' => $user
            ], 200); // يمكنك إضافة كود الحالة 200 بشكل صريح

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // إذا لم يتم العثور على المستخدم، ارجع خطأ 404
            return response()->json([
                'message' => 'User not found'
            ], 404);
        } catch (\Exception $e) {
            // إذا حدث أي خطأ آخر، ارجع خطأ 500
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            // البحث عن المستخدم بناءً على المعرف
            $user = User::findOrFail($id);

            // حذف الصورة القديمة إذا كانت موجودة
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }

            // حذف المستخدم
            $user->delete();

            // استجابة النجاح
            return response()->json([
                'message' => 'User deleted successfully'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // إذا لم يتم العثور على المستخدم، ارجع خطأ 404
            return response()->json([
                'message' => 'User not found'
            ], 404);
        } catch (\Exception $e) {
            // إذا حدث أي خطأ آخر، ارجع خطأ 500
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
