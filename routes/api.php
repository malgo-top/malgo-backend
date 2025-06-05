<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TenantApplicationController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PropertyController;
use Illuminate\Http\Request;


Route::post('/create/tenant/application', [TenantApplicationController::class, 'createTenantApplication']);
Route::get("/property/check/application/open", [PropertyController::class, 'checkIfPropertyApplicationOpen']);
Route::post('/tenant/application/request/doc/upload', [TenantApplicationController::class, 'uploadOtherDoc']);

//delete when production
Route::post('login', [AuthController::class, 'login']);
// Route::post('register', [AuthController::class, 'register']);


Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {

    Route::get('/tenant/applications', [TenantApplicationController::class, 'getTenantApplications']);
    Route::put('/tenant/application/deny', [TenantApplicationController::class, 'denyTenantApplication']);
    Route::put('/tenant/application/accept', [TenantApplicationController::class, 'acceptTenantApplication']);
    Route::put('/tenant/application/save', [TenantApplicationController::class, 'saveTenantApplication']);
    Route::put('/tenant/application/delete', [TenantApplicationController::class, 'deleteTenantApplication']);

    Route::put('/tenant/application/comment', [TenantApplicationController::class, 'updateComment']);

    Route::put('/tenant/application/request/doc', [TenantApplicationController::class, 'requestOtherDoc']);

    Route::post("/contract/create", [ContractController::class, 'createContract']);
    Route::get("/contract/all", [ContractController::class, 'getContracts']);
    Route::put("/contract/terminate", [ContractController::class, 'terminateContract']);

    Route::post("/bill/create/agua", [BillController::class, 'createWaterBill']);
    Route::post("/bill/create/gas", [BillController::class, 'createGasBill']);
    Route::post("/bill/create/luz", [BillController::class, 'createLuzBill']);

    Route::get("/property/all", [PropertyController::class, 'getAllProperties']);
    Route::put("/property/activate/applications", [PropertyController::class, 'switchPropertyApplications']);

});

Route::middleware(['auth:sanctum', 'role:tenant|admin'])->group(function () {

    Route::get("/bill/unpaid/", [BillController::class, 'getBillsUnpaid']);
    Route::post("/bill/pay/", [BillController::class, 'payBills']);

    Route::get("/payment/all/", [PaymentController::class, 'getPayments']);
    //hola
    Route::put("/payment/verify/", [PaymentController::class, 'verifyPayment']);

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);

});

