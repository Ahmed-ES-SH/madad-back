<?php

namespace App\Http\Controllers;

use App\Models\CustomerReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CustomerReviewController extends Controller
{

    public function index()
    {
        try {
            $reviews = CustomerReview::orderBy('created_at', 'desc')->paginate(12);
            return response()->json([
                'data' => $reviews->items(),
                'total' => $reviews->total(),
                'per_page' => $reviews->perPage(),
                'current_page' => $reviews->currentPage(), // تصحيح اسم الدالة
                'last_page' => $reviews->lastPage(), // تصحيح اسم المتغير
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage() // تصحيح اسم المتغير
            ], 500); // إضافة كود الحالة 500
        }
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'review_text' => "required|string",
                'rating' => "required|numeric",
                'review_date' => "required|date",
                'company_name' => "nullable|string", // إضافة نوع بيانات
                'user_id' => "required|exists:users,id",
            ]);

            if ($validation->fails()) {
                return response()->json([
                    'errors' => $validation->errors()
                ], 422); // كود الحالة 422 للتأكد من فشل التحقق
            }

            $review = new CustomerReview();

            $review->fill($request->only([
                'review_text',
                'rating',
                'review_date',
                'company_name',
                'user_id',
            ]));

            $review->save(); // حفظ المراجعة في قاعدة البيانات

            return response()->json([
                'message' => 'Your Review Added Successfully',
                'data' => $review
            ], 201); // كود الحالة 201 للإشارة إلى أن العنصر تم إنشاؤه
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500); // كود الحالة 500 في حالة حدوث خطأ
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $review = CustomerReview::findOrFail($id); // البحث عن المراجعة بواسطة ID

            return response()->json([
                'data' => $review
            ], 200); // كود الحالة 200 للإشارة إلى النجاح
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Review not found'
            ], 404); // كود الحالة 404 في حالة عدم العثور على المراجعة
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500); // كود الحالة 500 في حالة حدوث خطأ
        }
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            // تحقق من وجود المراجعة
            $review = CustomerReview::findOrFail($id);

            $validation = Validator::make($request->all(), [
                'review_text' => "nullable|string",
                'rating' => "nullable|numeric",
                'review_date' => "nullable|date",
                'company_name' => "nullable|string",
                'user_id' => "nullable|exists:users,id", // قد لا يحتاج المستخدم إلى تعديل
            ]);

            if ($validation->fails()) {
                return response()->json([
                    'errors' => $validation->errors()
                ], 422); // كود الحالة 422 للتأكد من فشل التحقق
            }

            // تحديث المراجعة فقط للحقول التي تم تمريرها
            $review->fill($request->only([
                'review_text',
                'rating',
                'review_date',
                'company_name',
                'user_id',
            ]));

            $review->save(); // حفظ التحديثات في قاعدة البيانات

            return response()->json([
                'message' => 'Review updated successfully',
                'data' => $review
            ], 200); // كود الحالة 200 للإشارة إلى النجاح
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Review not found'
            ], 404); // كود الحالة 404 في حالة عدم العثور على المراجعة
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500); // كود الحالة 500 في حالة حدوث خطأ
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $review = CustomerReview::findOrFail($id); // البحث عن المراجعة بواسطة ID

            $review->delete(); // حذف المراجعة

            return response()->json([
                'message' => 'Review deleted successfully'
            ], 200); // كود الحالة 200 للإشارة إلى النجاح
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Review not found'
            ], 404); // كود الحالة 404 في حالة عدم العثور على المراجعة
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500); // كود الحالة 500 في حالة حدوث خطأ
        }
    }
}
