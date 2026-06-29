<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CallController;
use App\Http\Controllers\CafeController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\GymOwnerController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\MemberQrController;
use App\Http\Controllers\MemberSearchController;
use App\Http\Controllers\MpesaController;
use App\Http\Controllers\PaymentApprovalController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TrainerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('site.home');
})->name('site.home');

Route::view('/about', 'site.about')->name('site.about');
Route::view('/services', 'site.services')->name('site.services');
Route::view('/memberships', 'site.memberships')->name('site.memberships');
Route::view('/trainers', 'site.trainers')->name('site.trainers');
Route::view('/contact', 'site.contact')->name('site.contact');
Route::post('/contact', function (Request $request) {
    $request->validate([
        'name' => ['required', 'string', 'max:120'],
        'email' => ['required', 'email', 'max:255'],
        'topic' => ['required', 'string', 'max:80'],
        'message' => ['required', 'string', 'max:1000'],
    ]);

    return back()->with('status', 'Message received. Fitzone will contact you soon.');
})->name('site.contact.store');

Route::get('/member-card/{token}', [MemberQrController::class, 'show'])->name('members.qr.show');
Route::post('/mpesa/callback', [MpesaController::class, 'callback'])->name('mpesa.callback');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::post('/login/google', [AuthController::class, 'loginWithGoogle'])->name('login.google');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', function () {
        return match (auth()->user()->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'trainer' => redirect()->route('trainer.dashboard'),
            'gym_owner' => redirect()->route('gym-owner.dashboard'),
            'cafe' => redirect()->route('cafe.dashboard'),
            default => redirect()->route('client.dashboard'),
        };
    })->name('dashboard');
    Route::get('/pay', [MpesaController::class, 'pay'])->name('mpesa.pay');
    Route::post('/stkpush', [MpesaController::class, 'stkPush'])->name('mpesa.stkpush');
    Route::post('/payments/submit', [PaymentApprovalController::class, 'store'])->name('payments.submit');
    Route::post('/payments/{payment}/approve', [PaymentApprovalController::class, 'approve'])->name('payments.approve');
    Route::post('/payments/{payment}/reject', [PaymentApprovalController::class, 'reject'])->name('payments.reject');
    Route::get('/members/{member}/qr.svg', [MemberQrController::class, 'svg'])->name('members.qr.svg');
    Route::get('/member-search', [MemberSearchController::class, 'index'])->name('member-search.index');
    Route::post('/member-search/{member}/chat', [MemberSearchController::class, 'startChat'])->name('member-search.chat');
    Route::get('/member-chats/{chat}', [MemberSearchController::class, 'showChat'])->name('member-chats.show');
    Route::post('/member-chats/{chat}/messages', [MemberSearchController::class, 'sendMessage'])->name('member-chats.messages');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/photo', [ProfileController::class, 'destroyPhoto'])->name('profile.photo.destroy');

    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/payments', [PaymentApprovalController::class, 'adminIndex'])->name('payments');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/orders', [InventoryController::class, 'orders'])->name('orders');
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');
        Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');
        Route::post('/inventory/{product}/restock', [InventoryController::class, 'restock'])->name('inventory.restock');
        Route::get('/trainers', [AdminController::class, 'trainers'])->name('trainers');
        Route::post('/trainers', [AdminController::class, 'storeTrainer'])->name('trainers.store');
        Route::post('/trainers/{trainer}/messages', [AdminController::class, 'messageTrainer'])->name('trainers.messages');
        Route::delete('/trainers/{trainer}', [AdminController::class, 'destroyTrainer'])->name('trainers.destroy');
        Route::get('/cafe-staff', [AdminController::class, 'cafeStaff'])->name('cafe-staff');
        Route::post('/cafe-staff', [AdminController::class, 'storeCafeStaff'])->name('cafe-staff.store');
        Route::delete('/cafe-staff/{staff}', [AdminController::class, 'destroyCafeStaff'])->name('cafe-staff.destroy');
        Route::get('/members/{member}', [AdminController::class, 'member'])->name('members.show');
        Route::get('/chats', [AdminController::class, 'chats'])->name('chats');
        Route::post('/chats/{chat}/messages', [AdminController::class, 'sendMessage'])->name('chats.messages');
    });                                                         

    Route::prefix('trainer')->name('trainer.')->middleware('role:trainer')->group(function () {
        Route::get('/dashboard', [TrainerController::class, 'dashboard'])->name('dashboard');
        Route::get('/payments', [PaymentApprovalController::class, 'trainerIndex'])->name('payments');
        Route::get('/attendance', [AttendanceController::class, 'scan'])->name('attendance');
        Route::post('/attendance', [AttendanceController::class, 'mark'])->name('attendance.mark');
        Route::post('/attendance/paperwork', [AttendanceController::class, 'importPaperwork'])->name('attendance.paperwork');
        Route::get('/chat', [TrainerController::class, 'chat'])->name('chat');
        Route::post('/chat/messages', [TrainerController::class, 'sendMessage'])->name('chat.messages');
        Route::post('/calls/{call}/accept', [CallController::class, 'accept'])->name('calls.accept');
        Route::post('/calls/{call}/decline', [CallController::class, 'decline'])->name('calls.decline');
    });

    Route::get('/calls/{call}', [CallController::class, 'show'])->name('calls.show');
    Route::post('/calls/{call}/end', [CallController::class, 'end'])->name('calls.end');
    Route::match(['get', 'post'], '/calls/{call}/signal', [CallController::class, 'signal'])->name('calls.signal');

    Route::prefix('client')->name('client.')->middleware('auth')->group(function () {
        Route::get('/packages', [ClientController::class, 'packages'])->name('packages');
        Route::get('/trainers', [ClientController::class, 'trainers'])->name('trainers');
        Route::get('/checkout', [ClientController::class, 'checkout'])->name('checkout');
        Route::post('/activate', [ClientController::class, 'activate'])->name('activate');
        Route::post('/bookings', [ClientController::class, 'storeBooking'])->name('bookings.store');
        Route::post('/reviews', [ClientController::class, 'storeReview'])->name('reviews.store');
        Route::post('/complaints', [ClientController::class, 'storeComplaint'])->name('complaints.store');

        Route::post('/recommend-trainers', [RecommendationController::class, 'recommend'])->name('recommend.trainers');

        Route::middleware('role:member')->group(function () {
            // Location selection after registration
            Route::get('/location/select', [\App\Http\Controllers\LocationController::class, 'showSelectionForm'])->name('location.select');
            Route::post('/location/nearby', [\App\Http\Controllers\LocationController::class, 'findNearbyTrainers'])->name('location.nearby');

            Route::get('/dashboard', [ClientController::class, 'dashboard'])->name('dashboard');
            Route::get('/onboarding', [ClientController::class, 'onboarding'])->name('onboarding');
            Route::get('/activation', [ClientController::class, 'activation'])->name('activation');
            Route::get('/today', [ClientController::class, 'today'])->name('today');
            Route::get('/diet', [ClientController::class, 'diet'])->name('diet');
            Route::get('/workout-plan', [ClientController::class, 'workout'])->name('workout');
            Route::get('/attendance', [ClientController::class, 'attendance'])->name('attendance');
            Route::get('/payments', [ClientController::class, 'payments'])->name('payments');
            Route::get('/wallet', [CafeController::class, 'wallet'])->name('wallet');
            Route::post('/wallet/deposit', [CafeController::class, 'deposit'])->name('wallet.deposit');
            Route::get('/cafe', [CafeController::class, 'shop'])->name('cafe');
            Route::post('/cafe/orders', [CafeController::class, 'order'])->name('cafe.orders');
            Route::get('/chat', [ClientController::class, 'chat'])->name('chat');
            Route::post('/chat/messages', [ClientController::class, 'sendMessage'])->name('chat.messages');
            Route::post('/calls/request', [CallController::class, 'store'])->name('calls.store');
            Route::post('/trainer/switch', [ClientController::class, 'switchTrainer'])->name('trainer.switch');
            Route::get('/gyms', [ClientController::class, 'gyms'])->name('gyms');
            Route::get('/members/{package:slug?}', [ClientController::class, 'members'])->name('members');
            Route::post('/members/{member}/chat', [ClientController::class, 'startMemberChat'])->name('members.chat');
        });
    });

    Route::prefix('gym-owner')->name('gym-owner.')->middleware('role:gym_owner')->group(function () {
        Route::get('/dashboard', [GymOwnerController::class, 'dashboard'])->name('dashboard');
    });

    Route::prefix('cafe')->name('cafe.')->middleware('role:cafe,admin')->group(function () {
        Route::get('/dashboard', [CafeController::class, 'dashboard'])->name('dashboard');
        Route::post('/orders/{order}', [CafeController::class, 'updateOrder'])->name('orders.update');
    });
});
