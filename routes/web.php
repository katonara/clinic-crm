<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\TreatmentRoomController;
use App\Http\Controllers\Admin\PatientPackageController;
use App\Http\Controllers\Customer;
use App\Http\Controllers\Admin\LiveUpdateController;
use App\Http\Controllers\Customer\CustomerLiveController;
use App\Http\Controllers\PublicBookingController;

// Landing page
Route::get('/', function () {
    return view('public.landing');
})->name('home');

// Public booking
Route::get('/book', [PublicBookingController::class, 'index'])->name('public.booking');
Route::post('/book', [PublicBookingController::class, 'store'])->name('public.booking.store');

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// OTP verification
Route::middleware('auth')->group(function () {
    Route::get('/otp/verify', [OtpController::class, 'showVerifyForm'])->name('otp.verify');
    Route::post('/otp/verify', [OtpController::class, 'verify'])->name('otp.verify.submit');
    Route::post('/otp/resend', [OtpController::class, 'resend'])->name('otp.resend');
});

// Dashboard redirect based on role
Route::get('/dashboard', function () {
    if (auth()->user()->isCustomer()) {
        return redirect()->route('customer.dashboard');
    }
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified.email'])->name('dashboard.redirect');

// Admin / Staff / Doctor routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified.email', 'role:admin,staff,doctor'])->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('role:admin,staff')->group(function () {
        Route::resource('patients', Admin\PatientController::class);
    });

    Route::resource('services', Admin\ServiceController::class);

    Route::middleware('role:admin')->group(function () {
        Route::resource('staff', Admin\StaffController::class);
    });

    Route::resource('bookings', Admin\BookingController::class);
    Route::patch('/bookings/{booking}/status', [Admin\BookingController::class, 'updateStatus'])->name('bookings.update-status');
    Route::patch('/bookings/{booking}/payment', [Admin\BookingController::class, 'updatePayment'])->name('bookings.update-payment');

    Route::get('/calendar', [Admin\CalendarController::class, 'index'])->name('calendar');
    Route::get('/calendar/events', [Admin\CalendarController::class, 'events'])->name('calendar.events');

    Route::middleware('role:admin')->group(function () {
        Route::get('/schedules', [Admin\ScheduleController::class, 'index'])->name('schedules.index');
        Route::post('/schedules/working-hours', [Admin\ScheduleController::class, 'updateWorkingHours'])->name('schedules.working-hours');
        Route::post('/schedules/blocked-dates', [Admin\ScheduleController::class, 'storeBlockedDate'])->name('schedules.blocked-dates.store');
        Route::delete('/schedules/blocked-dates/{blockedDate}', [Admin\ScheduleController::class, 'destroyBlockedDate'])->name('schedules.blocked-dates.destroy');
    });

    Route::middleware('role:admin,staff')->group(function () {
        Route::resource('follow-ups', Admin\FollowUpController::class)->parameters(['follow-ups' => 'followUp']);
    });

    Route::middleware('role:admin')->group(function () {
        Route::resource('whatsapp-templates', Admin\WhatsappTemplateController::class)->parameters(['whatsapp-templates' => 'whatsappTemplate']);
    });

    // Doctor management
    Route::middleware('role:admin')->group(function () {
        Route::resource('doctors', DoctorController::class);
        Route::get('/doctors/{doctor}/slots', [DoctorController::class, 'slots'])->name('doctors.slots');
        Route::get('/doctors/{doctor}/slot-events', [DoctorController::class, 'slotEvents'])->name('doctors.slot-events');
        Route::post('/doctors/{doctor}/move-slot', [DoctorController::class, 'moveSlot'])->name('doctors.move-slot');
    });

    // Treatment room management
    Route::middleware('role:admin,staff,doctor')->group(function () {
        Route::get('/room-board', [TreatmentRoomController::class, 'board'])->name('treatment-rooms.board');
        Route::get('/room-board/data', [TreatmentRoomController::class, 'boardData'])->name('treatment-rooms.board-data');
    });
    Route::middleware('role:admin,staff')->group(function () {
        Route::resource('treatment-rooms', TreatmentRoomController::class)->parameters(['treatment-rooms' => 'treatmentRoom']);
        Route::post('/room-board/assign', [TreatmentRoomController::class, 'assignBooking'])->name('treatment-rooms.assign');
        Route::post('/room-board/move', [TreatmentRoomController::class, 'moveAssignment'])->name('treatment-rooms.move');
    });

    // Patient packages
    Route::middleware('role:admin,staff')->group(function () {
        Route::resource('patient-packages', PatientPackageController::class)->parameters(['patient-packages' => 'patientPackage']);
        Route::post('/patient-packages/{patientPackage}/deduct', [PatientPackageController::class, 'deductSession'])->name('patient-packages.deduct');
    });

    Route::middleware('role:admin')->group(function () {
        Route::get('/settings', [Admin\SettingController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [Admin\SettingController::class, 'update'])->name('settings.update');
    });

    // Live update JSON endpoints (all admin roles)
    Route::prefix('live')->name('live.')->group(function () {
        Route::get('/stats', [LiveUpdateController::class, 'stats'])->name('stats');
        Route::get('/upcoming-bookings', [LiveUpdateController::class, 'upcomingBookings'])->name('upcoming-bookings');
        Route::get('/notifications', [LiveUpdateController::class, 'notifications'])->name('notifications');
        Route::get('/room-status', [LiveUpdateController::class, 'roomStatus'])->name('room-status');
    });
});

// Customer routes
Route::prefix('customer')->name('customer.')->middleware(['auth', 'verified.email', 'role:customer'])->group(function () {
    Route::get('/dashboard', [Customer\CustomerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/bookings/create', [Customer\CustomerBookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [Customer\CustomerBookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/history', [Customer\CustomerBookingController::class, 'history'])->name('bookings.history');
    Route::patch('/bookings/{booking}/cancel', [Customer\CustomerBookingController::class, 'cancel'])->name('bookings.cancel');
    Route::patch('/bookings/{booking}/reschedule', [Customer\CustomerBookingController::class, 'requestReschedule'])->name('bookings.reschedule');
    Route::get('/profile', [Customer\CustomerProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [Customer\CustomerProfileController::class, 'update'])->name('profile.update');

    // Live update JSON endpoints
    Route::prefix('live')->name('live.')->group(function () {
        Route::get('/dashboard', [CustomerLiveController::class, 'dashboard'])->name('dashboard');
        Route::get('/notifications', [CustomerLiveController::class, 'notifications'])->name('notifications');
    });
});
