<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{


    public function index($postId)
    {
        try {
            // جلب جميع التعليقات المرتبطة بالمقال المحدد مع الترقيم
            $comments = Comment::where('post_id', $postId)->paginate(15);

            return response()->json([
                'data' => $comments->items(),
                'total' => $comments->total(),
                'last_Page' => $comments->lastPage(), // تصحيح هنا
                'per_Page' => $comments->perPage(),
                'current_Page' => $comments->currentPage(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error retrieving comments.'], 500);
        }
    }



    /**
     * Store a newly created comment in storage.
     */
    public function store(Request $request, $postId)
    {
        try {
            $validation = Validator::make($request->all(), [
                'content' => 'required|string|max:500',
                'author_id' => 'required|exists:users,id',
            ]);

            if ($validation->fails()) {
                return response()->json(['errors' => $validation->errors()], 422);
            }

            // إنشاء تعليق جديد وملء البيانات
            $comment = new Comment();
            $comment->fill($request->only(['content', 'author_id']));
            $comment->post_id = $postId; // تعيين معرف المقال
            $comment->likes = 0; // يمكنك تغيير ذلك حسب الحاجة
            $comment->save();

            return response()->json([
                'data' => $comment,
                'message' => 'Comment added successfully.'
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified comment.
     */
    public function show($id)
    {
        try {
            $comment = Comment::findOrFail($id);
            return response()->json($comment, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Comment not found.'], 404);
        }
    }

    /**
     * Update the specified comment in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $validation = Validator::make($request->all(), [
                'content' => 'required|string|max:500',
                'likes' => 'integer|min:0',
            ]);

            if ($validation->fails()) {
                return response()->json(['errors' => $validation->errors()], 422);
            }

            $comment = Comment::findOrFail($id);
            $comment->fill($request->only(['content', 'likes'])); // ملء البيانات المحدثة
            $comment->save();

            return response()->json([
                'data' => $comment,
                'message' => 'Comment updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Comment not found.'], 404);
        }
    }

    /**
     * Remove the specified comment from storage.
     */
    public function destroy($id)
    {
        try {
            $comment = Comment::findOrFail($id);
            $comment->delete();

            return response()->json(['message' => 'Comment deleted successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Comment not found.'], 404);
        }
    }
}
