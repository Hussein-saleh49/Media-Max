<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LoginnController;
use App\Http\Controllers\Auth\RegisterationController;   
use App\Http\Controllers\MedicationController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PharmacyController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DriverRatingController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentCardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InfoController;
use App\Http\Controllers\VoucherController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::post("/register",[RegisterationController::class,"register"]);
Route::post("/login",[LoginController::class,"__invoke"]);
Route::post('/logout', [LogoutController::class, 'logout'])->middleware('auth:sanctum');


// Route::get("auth/facebook",[SocialController::class,"redirectToFacebook"]);
// Route::get("auth/facebook/callback",[SocialController::class,"handleFacebookCallback"]);





Route::post('/send-otp', [PasswordResetController::class, 'SendOtp']);
Route::post('/resend-otp', [PasswordResetController::class, 'resendOtp']);
Route::post('/verify-otp', [PasswordResetController::class, 'verifyOtp']);
Route::post('/create-password', [PasswordResetController::class, 'createNewPassword']);


//home page
//get current address
Route::middleware('auth:sanctum')->get('/user/address', [AddressController::class, 'getAddress']);
//update address
Route::middleware('auth:sanctum')->post('/user/address', [AddressController::class, 'updateAddress']);
//search


Route::get('/search', [MedicationController::class, 'search']);
Route::get('/top-search', [MedicationController::class, 'topSearch']);

////
Route::post('/vouchers', [VoucherController::class, 'createVoucher']);
////
Route::post('/apply-voucher', [VoucherController::class, 'applyVoucher']);
////
Route::post('/place-order', [OrderController::class, 'placeOrder']);
////

//
Route::post('/confirm-payment', [OrderController::class, 'confirmPayment']);

Route::post('/info', [InfoController::class, 'store']); // ✅ إضافة بيانات جديدة
Route::get('/info/{type}', [InfoController::class, 'show']); // ✅ عرض "من نحن" أو "اتصل بنا"
Route::put('/info/{type}', [InfoController::class, 'update']); // ✅
//
Route::post('/contact', [ContactController::class, 'store']); // ✅ إرسال رسالة جديدة
Route::get('/contact', [ContactController::class, 'index']); // ✅ عرض جميع الرسائل (للمسؤول

//
Route::delete('/delete-account', [UserController::class, 'deleteAccount']);
//
Route::delete('/delete-address', [AddressController::class, 'deleteAddress']);



//add,skip and show appointment

Route::middleware('auth:sanctum')->group(function () {
    //
    Route::post('/appointments', [AppointmentController::class, 'store']);

    
    // Fetch today's appointments
    Route::get('/appointments', [AppointmentController::class, 'index']);

    // appointment is taken
    Route::post('/appointments/{id}/taken', [AppointmentController::class, 'markAsTaken']);
    // Skip an appointment

    Route::post('/appointments/{id}/skipped', [AppointmentController::class, 'markAsSkipped']);

    // add a new reminder
    
    Route::post('/reminders', [ReminderController::class, 'store']); 

    // get all remiders
    Route::get('/reminders', [ReminderController::class, 'index']);

    //
    Route::post('/reminders/{id}/snooze', [ReminderController::class, 'snooze']);

    //
    Route::post('/reminders/{id}/repeat', [ReminderController::class, 'repeatReminder']);

    //
    Route::get('/daily-progress', [ReminderController::class,"dailyprogress"]);

    //
    Route::get('/categories', [CategoryController::class, 'index']); // جلب جميع الفئات
    Route::post('/categories', [CategoryController::class, 'store']); // إنشاء فئة جديدة
    Route::get('/categories/{id}', [CategoryController::class, 'show']); // جلب فئة واحدة
    Route::put('/categories/{id}', [CategoryController::class, 'update']); // تحديث الفئة
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']); // حذف الفئة

    //

    Route::get('/products', [ProductController::class, 'index']); // جلب جميع المنتجات
    Route::post('/products', [ProductController::class, 'store']); // إضافة منتج جديد
    Route::get('/products/{id}', [ProductController::class, 'show']); // عرض منتج محدد
    Route::put('/products/{id}', [ProductController::class, 'update']); // تحديث المنتج
    Route::delete('/products/{id}', [ProductController::class, 'destroy']); // حذف المنتج
    //
    Route::post('/cart/add', [CartController::class, 'addToCart']);

    // 🛍️ جلب محتويات السلة
    Route::get('/cart', [CartController::class, 'getCart']);

    // 🔄 تحديث الكمية داخل السلة
    Route::put('/cart/update', [CartController::class, 'updateQuantity']);

    // ❌ حذف عنصر من السلة
    Route::delete('/cart/remove/{cartId}', [CartController::class, 'removeFromCart']);

    
   
    
    //
    Route::post('/order/place', [OrderController::class, 'placeOrder']); // تنفيذ الطلب
    //
    Route::get('/orders/active', [OrderController::class, 'activeOrders']);
    Route::get('/orders/past', [OrderController::class, 'pastOrders']);
    //
    Route::post("/user/update-profile",[UserController::class,"updateprofile"]);

    //
    Route::post('/change-password', [UserController::class, 'changePassword']);

    //
    Route::get('/payment-cards', [PaymentCardController::class, 'index']);
    Route::post('/payment-cards', [PaymentCardController::class, 'store']);
    Route::put('/payment-cards/{id}', [PaymentCardController::class, 'update']);
    Route::delete('/payment-cards/{id}', [PaymentCardController::class, 'destroy']);

    //
    Route::post('/addresses', [AddressController::class, 'store']); // إضافة عنوان جديد
    Route::put('/addresses/{address}', [AddressController::class, 'update']); // تحديث عنوان موجود
    Route::get('/addresses', [AddressController::class, 'getAddresses']); // جلب العناوين الخاصة بالمستخدم

    //
    Route::post('/rate-driver', [DriverRatingController::class, 'rateDriver']);
    
    
   
    
});








//nearby pharmacies
Route::get('/pharmacies/nearby', [PharmacyController::class, 'getNearbyPharmacies']);

//get all pharmacies
Route::get("/pharmacies", [PharmacyController::class, "getAllPharmacies"]);

//chatbot


Route::post('/chatbot', [ChatController::class, '__invoke']);

//










