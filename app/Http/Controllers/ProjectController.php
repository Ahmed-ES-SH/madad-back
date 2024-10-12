<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\ImagesUploadService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProjectController extends Controller
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
            $projects = Project::orderBy('created_at', 'desc')->paginate(10);
            return response()->json([
                'data' => $projects->items(),  // العناصر الحالية فقط
                'total' => $projects->total(), // إجمالي العناصر
                'per_page' => $projects->perPage(), // عدد العناصر في كل صفحة
                'current_page' => $projects->currentPage(), // الصفحة الحالية
                'last_page' => $projects->lastPage(), // آخر صفحة
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'Message' => $e->getMessage()
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
                "name"  => "required|string",
                "description" => "required|string",
                "images" => "required|array", // تأكد أن الصور عبارة عن مصفوفة
                "images.*" => "image|mimes:jpeg,png,jpg,gif|max:2048", // التحقق من نوع الصورة وحجمها
                "completion_date" => "required|date",
                "project_link" => "required|url",
                "client_name" => "required|string",
                "category" => "required|string",
                "video_link" => "nullable|url",
                "awards" => "nullable|string",
                "technologies_used"   => "nullable|string",
            ]);


            if ($validation->fails()) {
                return response()->json([
                    'errors' => $validation->errors()
                ]);
            }

            $project = new Project();

            $project->fill($request->only([
                'name',
                'description',
                'completion_date',
                'project_link',
                'client_name',
                'category',
                'video_link',
                'awards',
                'technologies_used',
            ]));

            $this->ImagesUploadService->uploadImages($request, $project, 'projects/');
            $project->save();
            return response()->json([
                'Message' => 'Project Add Successfully',
                'data' => $project
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'Message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            // العثور على المشروع
            $project = Project::findOrFail($id);

            return response()->json([
                'Message' => 'Project details retrieved successfully',
                'data' => $project
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'Message' => 'Project not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'Message' => $e->getMessage()
            ], 500);
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
                "name"  => "required|string",
                "description" => "required|string",
                "images" => "nullable|array", // الصور يمكن أن تكون اختيارية في التحديث
                "images.*" => "image|mimes:jpeg,png,jpg,gif|max:2048", // التحقق من نوع الصورة وحجمها
                "completion_date" => "required|date",
                "project_link" => "required|url",
                "client_name" => "required|string",
                "category" => "required|string",
                "video_link" => "nullable|url",
                "awards" => "nullable|string",
                "technologies_used"   => "nullable|string",
            ]);

            if ($validation->fails()) {
                return response()->json([
                    'errors' => $validation->errors()
                ], 422);
            }

            // العثور على المشروع
            $project = Project::findOrFail($id);

            // حذف الصور القديمة إذا تم رفع صور جديدة
            if ($request->hasFile('images')) {
                $oldImages = json_decode($project->images, true);
                if ($oldImages) {
                    foreach ($oldImages as $oldImage) {
                        Storage::disk('public')->delete($oldImage);
                    }
                }

                // رفع الصور الجديدة
                $this->ImagesUploadService->uploadImages($request, $project, 'projects/');
            }

            // تحديث باقي الحقول
            $project->fill($request->only([
                'name',
                'description',
                'completion_date',
                'project_link',
                'client_name',
                'category',
                'video_link',
                'awards',
                'technologies_used',
            ]));

            $project->save();

            return response()->json([
                'Message' => 'Project updated successfully',
                'data' => $project
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'Message' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            // العثور على المشروع
            $project = Project::findOrFail($id);

            // حذف الصور من التخزين
            $images = json_decode($project->images, true);
            if ($images) {
                foreach ($images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }

            // حذف المشروع
            $project->delete();

            return response()->json([
                'Message' => 'Project deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'Message' => $e->getMessage()
            ], 500);
        }
    }
}
