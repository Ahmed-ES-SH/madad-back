<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImagesUploadService
{
    public function uploadImages(Request $request, $user, $folder)
    {
        if ($request->hasFile('images')) {
            $imagePaths = []; // مصفوفة لتخزين مسارات الصور
            foreach ($request->file('images') as $image) {
                // تخزين الصورة داخل المجلد المحدد
                $imagePath = $image->store($folder, 'public');
                $imagePaths[] = $imagePath; // إضافة المسار إلى المصفوفة
            }
            // حفظ مسارات الصور في قاعدة البيانات
            $user->images = json_encode($imagePaths); // افترض أن لديك حقل images في جدول المستخدم
            $user->save();
        }
    }
}
