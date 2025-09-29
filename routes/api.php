<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterationController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DriverRatingController;
use App\Http\Controllers\InfoController;
use App\Http\Controllers\MedicationController;
use App\Http\Controllers\PharmacyController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderDeliveryController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PaymentCardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
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

Route::post("/register", [RegisterationController::class, "register"]);
Route::post("/login", [LoginController::class, "__invoke"]);
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

Route::get('/medications/search/{pharmacyId}', [MedicationController::class, 'searchInPharmacy']);

Route::get('/top-search', [MedicationController::class, 'topSearch']);

//add medicine
Route::middleware('auth:sanctum')->post('/medications', [MedicationController::class, 'store']);


////
Route::post('/vouchers', [VoucherController::class, 'createVoucher']);
////
Route::post('/apply-voucher', [VoucherController::class, 'applyVoucher']);
////
Route::post('/place-order', [OrderController::class, 'placeOrder']);
////

//
Route::post('/confirm-payment', [OrderController::class, 'confirmPayment']);

Route::post('/info', [InfoController::class, 'store']);        
Route::get('/info/{type}', [InfoController::class, 'show']);   
Route::put('/info/{type}', [InfoController::class, 'update']); 
//
Route::post('/contact', [ContactController::class, 'store']); 
Route::get('/contact', [ContactController::class, 'index']);  

//
Route::delete('/delete-account', [UserController::class, 'deleteAccount']);
//
Route::delete('/delete-address', [AddressController::class, 'deleteAddress']);

//add,skip and show appointment

Route::middleware('auth:sanctum')->group(function () {


   
   
   Route::put('/cart/update', [CartController::class, 'updateQuantity']);
   
   // ❌ حذف عنصر من السلة
   Route::delete('/cart/remove/{cartId}', [CartController::class, 'removeFromCart']);
   
   
   Route::post('/order/place', [OrderController::class, 'placeOrder']); 
   
   Route::get('/orders/active', [OrderController::class, 'activeOrders']);
   Route::get('/orders/past', [OrderController::class, 'pastOrders']);
   //
   Route::post("/user/update-profile", [UserController::class, "updateprofile"]);
   
   //
   
   //
   Route::get('/payment-cards', [PaymentCardController::class, 'index']);
   Route::post('/payment-cards', [PaymentCardController::class, 'store']);
   Route::put('/payment-cards/{id}', [PaymentCardController::class, 'update']);
   Route::delete('/payment-cards/{id}', [PaymentCardController::class, 'destroy']);
   
   //
   Route::post('/addresses', [AddressController::class, 'store']);          
   Route::put('/addresses/{address}', [AddressController::class, 'update']); 
   Route::get('/addresses', [AddressController::class, 'getAddresses']);     
   
   //
   Route::post('/rate-driver', [DriverRatingController::class, 'rateDriver']);
   
   //
   Route::post('/medications/{id}', [MedicationController::class, 'update']);
   
   Route::post('/cart/add', [CartController::class, 'addToCart']);
   Route::get('/cart', [CartController::class, 'getCart']);
});

Route::post('/confirm-delivery', [OrderDeliveryController::class, 'confirmDelivery']);

Route::get('/pharmacies', [MedicationController::class, 'getAllPharmacies']);    

Route::post('/pharmacies', [PharmacyController::class, 'store']);

//
Route::post('/pharmacies/{id}', [PharmacyController::class, 'update']);
Route::delete('/medications/{id}', [MedicationController::class, 'destroy']);
Route::post('/contact/send', [ContactController::class, 'send']);

Route::post('/assign-shared-image', [MedicationController::class, 'assignSharedImageToMedications']);


// Route::post('/confirm-delivery', [OrderDeliveryController::class, 'confirmDelivery'])->middleware('auth:sanctum');

Route::post('/change-password', [UserController::class, 'changePassword']);
Route::get('/pharmacies/{id}/medications', [PharmacyController::class, 'getMedicationsByPharmacy']);

Route::delete('/medications/{id}', [MedicationController::class, 'destroy']);


