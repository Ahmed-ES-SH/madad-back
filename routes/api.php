<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyInformationController;
use App\Http\Controllers\CustomerReviewController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TeamMemberController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\QuestionAnswerController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BlogPostController;
use App\Http\Controllers\SubCategoryController;
use Illuminate\Support\Facades\Route;


//////////////////
//Auth Routes ////
//////////////////

Route::post('/register', [AuthController::class, 'register']);
Route::middleware('auth:sanctum')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::get('/currentuser', 'currentUser'); // إضافة دالة لاسترجاع المستخدم الحالي
        Route::post('/logout', 'logout');
        Route::post('/login', 'login');
    });
});

////////////////////
//users Routes /////
////////////////////

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(UsersController::class)->group(function () {
        Route::get('/users/{id}', 'show');
        Route::get('/users', 'index');
        Route::put('/user/{id}', 'update');
        Route::delete('/user/{id}', 'destroy');
    });
});


///////////////////////////////////////////
//CompanyInformationController Routes /////
///////////////////////////////////////////

Route::controller(CompanyInformationController::class)->group(function () {
    Route::get('/all-details', 'index');
    Route::post('/store-details', 'store');
    Route::put('/update-details/{id}', 'update');
    Route::get('/show-details/{id}', 'show');
    Route::delete('/details/{id}', 'destroy');
});

///////////////////////////////////////////
//TeamMembers Routes /////
///////////////////////////////////////////

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(TeamMemberController::class)->group(function () {
        Route::get('/team-members', 'index'); // عرض جميع الأعضاء
        Route::post('/team-member', 'store'); // إضافة عضو جديد
        Route::get('/team-member/{id}', 'show'); // عرض عضو محدد
        Route::put('/team-member/{id}', 'update'); // تحديث معلومات عضو محدد
        Route::delete('/team-member/{id}', 'destroy'); // حذف عضو محدد
    });
});


///////////////////////
//Projects Routes /////
///////////////////////

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(ProjectController::class)->group(function () {
        Route::get('/projects', 'index');
        Route::post('/store-project', 'store');
        Route::get('/project/{id}', 'show');
        Route::put('/project/{id}', 'update');
        Route::delete('/project/{id}', 'destroy');
    });
});


/////////////////////////////
//CustomerRviews Routes /////
/////////////////////////////

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(CustomerReviewController::class)->group(function () {
        Route::get('/customer-reviews', 'index'); // للحصول على جميع المراجعات
        Route::post('/customer-reviews', 'store'); // لإضافة مراجعة جديدة
        Route::get('/customer-reviews/{id}', 'show'); // لعرض مراجعة معينة
        Route::put('/customer-reviews/{id}', 'update'); // لتحديث مراجعة معينة
        Route::delete('/customer-reviews/{id}', 'destroy'); // لحذف مراجعة معينة
    });
});

//////////////////////////////
//QuestionAnswers Routes /////
//////////////////////////////


Route::middleware('auth:sanctum')->group(function () {
    Route::controller(QuestionAnswerController::class)->group(function () {
        Route::get('/questions', 'index'); // عرض جميع الأسئلة والأجوبة
        Route::get('/approved-questions',  'approvedQuestions');
        Route::post('/questions', 'store'); // إضافة سؤال وجواب جديد
        Route::get('/questions/{id}', 'show'); // عرض سؤال وجواب محدد
        Route::put('/questions/{id}', 'update'); // تحديث سؤال وجواب محدد
        Route::delete('/questions/{id}', 'destroy'); // حذف سؤال وجواب محدد
    });
});

//////////////////////////////
//categories Routes //////////
//////////////////////////////


Route::middleware('auth:sanctum')->group(function () {
    Route::controller(CategoryController::class)->group(function () {
        Route::get('/categories',  'index');
        Route::post('/category-add',  'store');
        Route::put('/categories/{id}',  'update');
        Route::get('/categories/{id}',  'show');
        Route::delete('/categories/{id}',  'destroy');
    });
});

//////////////////////////////
//subcategories Routes ///////
//////////////////////////////

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(SubCategoryController::class)->group(function () {
        Route::get('/subcategories', 'index'); // عرض جميع الفئات الفرعية
        Route::post('/subcategory-add', 'store'); // إضافة فئة فرعية جديدة
        Route::get('/subcategories/{id}', 'show'); // عرض فئة فرعية معينة
        Route::put('/subcategories/{id}', 'update'); // تحديث فئة فرعية معينة
        Route::delete('/subcategories/{id}', 'destroy'); // حذف فئة فرعية معينة
    });
});

//////////////////////////////
// Services Routes ///////////
//////////////////////////////

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(ServiceController::class)->group(function () {
        Route::get('/services', 'index'); // عرض جميع الخدمات
        Route::post('/service-add', 'store'); // إضافة خدمة جديدة
        Route::get('/services/{id}', 'show'); // عرض خدمة معينة
        Route::put('/services/{id}', 'update'); // تحديث خدمة معينة
        Route::delete('/services/{id}', 'destroy'); // حذف خدمة معينة
    });
});

//////////////////////////////
// BlogPosts Routes //////////
//////////////////////////////

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(BlogPostController::class)->group(function () {
        Route::get('/blog-posts', 'index'); // قائمة المقالات
        Route::post('/blog-post-add', 'store'); // إنشاء مقالة جديدة
        Route::get('/blog-posts/{id}', 'show'); // عرض مقالة محددة
        Route::put('/blog-posts/{id}', 'update'); // تحديث مقالة محددة
        Route::delete('/blog-posts/{id}', 'destroy'); // حذف مقالة محددة
    });
});
//////////////////////////////
// BlogPosts Routes //////////
//////////////////////////////

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(CommentController::class)->group(function () {
        Route::get('/blog-posts/{postId}/comments', 'index');
        Route::post('/blog-posts/{postId}/comments', 'store'); // إضافة تعليق
        Route::get('/comments/{id}', 'show'); // عرض تعليق
        Route::put('/comments/{id}', 'update'); // تحديث تعليق
        Route::delete('/comments/{id}', 'destroy');
    });
});
