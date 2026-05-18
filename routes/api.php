<?php

use App\Http\Controllers\API\DashboardMenuController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\PagePrivilegeController;
use App\Http\Controllers\API\RolePrivilegeController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\UserPrivilegeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/register', [UserController::class, 'register']);

    Route::get('/check', [UserController::class, 'authenticateToken']);

    Route::middleware('checkAuth')->group(function () {
        Route::post('/logout', [UserController::class, 'logout']);
        Route::get('/me-role', [UserController::class, 'getMyRole']);
        Route::get('/profile', [UserController::class, 'getProfile']);
    });
});

Route::get('/my/sidebar-menus', [UserPrivilegeController::class, 'mySidebarMenus']);

Route::middleware('checkAuth')->prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::get('/all', [UserController::class, 'all']);
    Route::get('/{id}', [UserController::class, 'show']);

    Route::post('/', [UserController::class, 'store']);
    Route::match(['put', 'patch'], '/{id}', [UserController::class, 'update']);

    Route::delete('/{id}', [UserController::class, 'destroy']);
    Route::post('/{id}/restore', [UserController::class, 'restore']);
    Route::delete('/{id}/force', [UserController::class, 'forceDelete']);

    Route::patch('/{id}/password', [UserController::class, 'updatePassword']);
    Route::post('/{id}/image', [UserController::class, 'updateImage']);
    Route::post('/{uuid}/cv', [UserController::class, 'uploadCvByUuid']);
    Route::post('/import-csv', [UserController::class, 'importUsersCsv']);
});

Route::middleware('checkAuth')->group(function () {
    Route::get('/profile', [UserController::class, 'getProfile']);
    Route::post('/profile', [UserController::class, 'updateMyProfile']);
    Route::patch('/profile/password', [UserController::class, 'updateMyPassword']);

    Route::get('/user/{idOrUuid}', [UserPrivilegeController::class, 'show']);

    Route::prefix('dashboard-menus')->group(function () {
        Route::get('/', [DashboardMenuController::class, 'index']);
        Route::get('/tree', [DashboardMenuController::class, 'tree']);
        Route::get('/all-with-privileges', [DashboardMenuController::class, 'allWithPrivileges']);
        Route::get('/archived', [DashboardMenuController::class, 'archived']);
        Route::get('/bin', [DashboardMenuController::class, 'bin']);
        Route::post('/', [DashboardMenuController::class, 'store']);
        Route::post('/reorder', [DashboardMenuController::class, 'reorder']);
        Route::get('/{identifier}', [DashboardMenuController::class, 'show']);
        Route::match(['put', 'patch'], '/{identifier}', [DashboardMenuController::class, 'update']);
        Route::post('/{identifier}/archive', [DashboardMenuController::class, 'archive']);
        Route::post('/{identifier}/unarchive', [DashboardMenuController::class, 'unarchive']);
        Route::delete('/{identifier}', [DashboardMenuController::class, 'destroy']);
        Route::post('/{identifier}/restore', [DashboardMenuController::class, 'restore']);
        Route::delete('/{identifier}/force', [DashboardMenuController::class, 'forceDelete']);
    });

    Route::prefix('privileges')->group(function () {
        Route::get('/', [PagePrivilegeController::class, 'index']);
        Route::get('/index-of-api', [PagePrivilegeController::class, 'indexOfApi']);
        Route::get('/archived', [PagePrivilegeController::class, 'archived']);
        Route::get('/bin', [PagePrivilegeController::class, 'bin']);
        Route::post('/', [PagePrivilegeController::class, 'store']);
        Route::post('/reorder', [PagePrivilegeController::class, 'reorder']);
        Route::get('/{identifier}', [PagePrivilegeController::class, 'show']);
        Route::match(['put', 'patch'], '/{identifier}', [PagePrivilegeController::class, 'update']);
        Route::post('/{identifier}/archive', [PagePrivilegeController::class, 'archive']);
        Route::post('/{identifier}/unarchive', [PagePrivilegeController::class, 'unarchive']);
        Route::delete('/{identifier}', [PagePrivilegeController::class, 'destroy']);
        Route::post('/{identifier}/restore', [PagePrivilegeController::class, 'restore']);
        Route::delete('/{identifier}/force', [PagePrivilegeController::class, 'forceDelete']);
    });

    Route::prefix('user-privileges')->group(function () {
        Route::get('/list', [UserPrivilegeController::class, 'list']);
        Route::post('/sync', [UserPrivilegeController::class, 'sync']);
        Route::post('/assign', [UserPrivilegeController::class, 'assign']);
        Route::post('/unassign', [UserPrivilegeController::class, 'unassign']);
        Route::delete('/', [UserPrivilegeController::class, 'destroy']);
        Route::get('/my-modules', [UserPrivilegeController::class, 'myModules']);
        Route::get('/modules', [UserPrivilegeController::class, 'modulesForUser']);
        Route::get('/modules/{idOrUuid}', [UserPrivilegeController::class, 'modulesForUserByPath']);
        Route::get('/user/by-uuid', [UserPrivilegeController::class, 'byUuid']);
    });

    Route::prefix('role-privileges')->group(function () {
        Route::get('/list', [RolePrivilegeController::class, 'list']);
        Route::post('/sync', [RolePrivilegeController::class, 'sync']);
        Route::post('/assign', [RolePrivilegeController::class, 'assign']);
        Route::post('/unassign', [RolePrivilegeController::class, 'unassign']);
        Route::delete('/', [RolePrivilegeController::class, 'destroy']);
    });

    Route::prefix('notifications')->group(function () {
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::get('/drawer', [NotificationController::class, 'drawer']);
        Route::post('/read-all', [NotificationController::class, 'markAllRead']);
        Route::post('/{id}/read', [NotificationController::class, 'markRead']);
    });

    Route::get('/role/sidebar-menus', [RolePrivilegeController::class, 'sidebarMenusForRole']);
});
