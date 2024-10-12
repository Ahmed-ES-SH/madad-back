<?php

namespace App\Http\Controllers;

use App\Models\Question_Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class QuestionAnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $Q_As = Question_Answer::orderBy('created_at', 'desc')->paginate(12);
            return response()->json([
                'data' => $Q_As->items(),
                'total' => $Q_As->total(),
                'per_page' => $Q_As->perPage(), // التصحيح هنا
                'last_page' => $Q_As->lastPage(), // التصحيح هنا
                'current_page' => $Q_As->currentPage(), // التصحيح هنا
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage() // تصحيح اسم المفتاح
            ], 500); // إضافة كود الحالة 500
        }
    }


    public function approvedQuestions()
    {
        try {
            $approvedQuestions = Question_Answer::where('approved', true)
                ->orderBy('created_at', 'desc')
                ->paginate(12);

            return response()->json([
                'data' => $approvedQuestions->items(),
                'total' => $approvedQuestions->total(),
                'per_page' => $approvedQuestions->perPage(),
                'current_page' => $approvedQuestions->currentPage(),
                'last_page' => $approvedQuestions->lastPage(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }




    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // التحقق من صحة البيانات
            $validation = Validator::make($request->all(), [
                'question' => "required|string",
                'answer' => "required|string",
                'user_id' => "nullable|exists:users,id",
                'is_visible' => "nullable|boolean", // تحديد النوع كـ boolean
            ]);

            if ($validation->fails()) {
                return response()->json([
                    'errors' => $validation->errors()
                ], 422); // إضافة كود الحالة 422 لبيانات غير صالحة
            }

            $Q_A = new Question_Answer();

            // ملء البيانات
            $Q_A->fill($request->only([
                'question',
                'answer',
                'user_id',
                'is_visible',
            ]));

            $Q_A->save(); // حفظ السؤال والجواب في قاعدة البيانات

            return response()->json([
                'message' => 'Question and answer added successfully', // تصحيح اسم المفتاح
                'data' => $Q_A
            ], 201); // إضافة كود الحالة 201 للإشارة إلى إنشاء ناجح
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage() // تصحيح اسم المفتاح
            ], 500); // إضافة كود الحالة 500
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            // البحث عن السجل باستخدام المعرف
            $Q_A = Question_Answer::findOrFail($id);

            return response()->json([
                'data' => $Q_A
            ], 200); // كود الحالة 200 للإشارة إلى النجاح
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Question and answer not found'
            ], 404); // كود الحالة 404 إذا لم يتم العثور على السجل
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500); // إضافة كود الحالة 500 للأخطاء العامة
        }
    }




    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            // البحث عن السجل باستخدام المعرف
            $Q_A = Question_Answer::findOrFail($id);

            // التحقق من صحة البيانات
            $validation = Validator::make($request->all(), [
                'question' => "nullable|string",
                'answer' => "nullable|string",
                'user_id' => "nullable|exists:users,id",
                'is_visible' => "nullable|boolean", // تحديد النوع كـ boolean
            ]);

            if ($validation->fails()) {
                return response()->json([
                    'errors' => $validation->errors()
                ], 422); // إضافة كود الحالة 422 لبيانات غير صالحة
            }

            // ملء البيانات الجديدة
            $Q_A->fill($request->only([
                'question',
                'answer',
                'user_id',
                'is_visible',
            ]));

            $Q_A->save(); // حفظ التغييرات في قاعدة البيانات

            return response()->json([
                'message' => 'Question and answer updated successfully',
                'data' => $Q_A
            ], 200); // إضافة كود الحالة 200 للإشارة إلى تحديث ناجح
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Question and answer not found'
            ], 404); // كود الحالة 404 إذا لم يتم العثور على السجل
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500); // إضافة كود الحالة 500 للأخطاء العامة
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            // البحث عن السجل باستخدام المعرف
            $Q_A = Question_Answer::findOrFail($id);

            $Q_A->delete(); // حذف السجل

            return response()->json([
                'message' => 'Question and answer deleted successfully'
            ], 200); // كود الحالة 200 للإشارة إلى النجاح
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Question and answer not found'
            ], 404); // كود الحالة 404 إذا لم يتم العثور على السجل
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500); // إضافة كود الحالة 500 للأخطاء العامة
        }
    }
}
