<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CategoryStatusController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\FindingController;
use App\Http\Controllers\InspectionController;
use App\Http\Controllers\InspectionTemplateController;
use App\Http\Controllers\OutletController;
use App\Http\Controllers\PolicyItemController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// --- Root ---
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// --- Auth ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// --- Authenticated App ---
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Inspections ──
    // IMPORTANT: specific routes must be before wildcard /inspections/{inspection}

    Route::get('/inspections', [InspectionController::class, 'index'])->name('inspections.index');

    // Create (admin/auditor only) — must be BEFORE /inspections/{inspection}
    Route::middleware('role:admin,auditor')->group(function () {
        Route::get('/inspections/create', [InspectionController::class, 'create'])->name('inspections.create');
        Route::post('/inspections', [InspectionController::class, 'store'])->name('inspections.store');
    });

    // Wildcard show — all authenticated
    Route::get('/inspections/{inspection}', [InspectionController::class, 'show'])->name('inspections.show');

    // Routes that require a bound {inspection} model (admin/auditor)
    Route::middleware('role:admin,auditor')->group(function () {
        Route::get('/inspections/{inspection}/edit', [InspectionController::class, 'edit'])->name('inspections.edit');
        Route::put('/inspections/{inspection}', [InspectionController::class, 'update'])->name('inspections.update');
        Route::delete('/inspections/{inspection}', [InspectionController::class, 'destroy'])->name('inspections.destroy');

        // Follow-up inspections
        Route::get('/inspections/{inspection}/follow-up', [InspectionController::class, 'createFollowUp'])->name('inspections.follow-up.create');
        Route::post('/inspections/{inspection}/follow-up', [InspectionController::class, 'storeFollowUp'])->name('inspections.follow-up.store');

        // Category status per policy per inspection
        Route::put('/inspections/{inspection}/categories/{policy}', [CategoryStatusController::class, 'update'])->name('category-status.update');

        // Findings — create & verify (auditor/admin only)
        Route::get('/inspections/{inspection}/findings/create', [FindingController::class, 'create'])->name('findings.create');
        Route::post('/inspections/{inspection}/findings', [FindingController::class, 'store'])->name('findings.store');
        Route::delete('/findings/{finding}', [FindingController::class, 'destroy'])->name('findings.destroy');
        Route::get('/findings/{finding}/verify', [FindingController::class, 'verify'])->name('findings.verify');
    });

    // Finding response — auditee fills corrective/preventive actions
    Route::get('/findings/{finding}/edit', [FindingController::class, 'edit'])->name('findings.edit');
    Route::put('/findings/{finding}', [FindingController::class, 'update'])->name('findings.update');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // ── Admin only ──
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {

        // Departments
        Route::get('departments', [DepartmentController::class, 'index'])->name('departments.index');
        Route::post('departments', [DepartmentController::class, 'store'])->name('departments.store');
        Route::put('departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');
        Route::delete('departments/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');

        // Outlets
        Route::get('outlets', [OutletController::class, 'index'])->name('outlets.index');
        Route::post('outlets', [OutletController::class, 'store'])->name('outlets.store');
        Route::put('outlets/{outlet}', [OutletController::class, 'update'])->name('outlets.update');
        Route::delete('outlets/{outlet}', [OutletController::class, 'destroy'])->name('outlets.destroy');

        // Inspection Templates
        Route::get('templates', [InspectionTemplateController::class, 'index'])->name('templates.index');
        Route::post('templates', [InspectionTemplateController::class, 'store'])->name('templates.store');
        Route::get('templates/{template}', [InspectionTemplateController::class, 'show'])->name('templates.show');
        Route::put('templates/{template}', [InspectionTemplateController::class, 'update'])->name('templates.update');
        Route::delete('templates/{template}', [InspectionTemplateController::class, 'destroy'])->name('templates.destroy');
        Route::post('templates/{template}/items', [InspectionTemplateController::class, 'storeItem'])->name('templates.items.store');
        Route::delete('template-items/{item}', [InspectionTemplateController::class, 'destroyItem'])->name('template-items.destroy');

        // Users
        Route::resource('users', UserController::class)->except(['show']);

        // Compliance Categories & checklist items
        Route::get('policies', [PolicyItemController::class, 'index'])->name('policies.index');
        Route::post('policies/{policy}/items', [PolicyItemController::class, 'store'])->name('policies.items.store');
        Route::put('policies/{policy}/items/{item}', [PolicyItemController::class, 'update'])->name('policies.items.update');
        Route::delete('policies/{policy}/items/{item}', [PolicyItemController::class, 'destroy'])->name('policies.items.destroy');
    });
});
