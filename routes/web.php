<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.auth.login')->name('login');
Route::redirect('/login', '/');
Route::redirect('/register', '/');
Route::redirect('/forgot-password', '/');
Route::redirect('/reset-password', '/');

Route::view('/dashboard', 'pages.pages.common.dashboard');
Route::view('/profile', 'pages.pages.user.profile');
Route::redirect('/user', '/users/manage');

Route::view('/users/manage', 'pages.pages.users.manageUsers');
Route::view('/user/manage', 'pages.pages.users.manageUsers');

Route::view('/dashboard-menu/manage', 'modules.dashboardMenu.manageDashboardMenu');
Route::view('/dashboard-menu/create', 'modules.dashboardMenu.createDashboardMenu');

Route::view('/page-privilege/manage', 'modules.privileges.managePagePrivileges');
Route::view('/page-privilege/create', 'modules.privileges.createPagePrivileges');

Route::view('/user-privileges/manage', 'modules.privileges.assignPrivileges');
Route::view('/role-privileges/manage', 'modules.privileges.assignRolePrivileges');

Route::get('/activity-logs', function () {
    return view('pages.pages.common.placeholder', [
        'pageTitle' => 'Activity Logs',
        'pageLead' => 'User activity logging is preserved in the system and this screen is ready for the next attendance-specific implementation.',
        'pageIcon' => 'fa-solid fa-clock-rotate-left',
    ]);
});

Route::get('/notifications', function () {
    return view('pages.pages.common.placeholder', [
        'pageTitle' => 'Notifications',
        'pageLead' => 'Notification delivery is active in the shared admin shell. A full notifications page can be added on top of this starter.',
        'pageIcon' => 'fa-solid fa-bell',
    ]);
});
