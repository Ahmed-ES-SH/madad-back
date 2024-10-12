<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageUploadService
{
    public function uploadImage(Request $request, $user, $folder)
    {
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة إذا كانت موجودة
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }

            // تخزين الصورة داخل المجلد المحدد
            $imagePath = $request->file('image')->store($folder, 'public');
            $user->image = $imagePath; // حفظ مسار الصورة في قاعدة البيانات
            $user->save();
        }
    }
}
