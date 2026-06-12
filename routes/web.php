<?php

$router->get('/', 'AuthController@userLogin');

$router->get('/user-login', 'AuthController@userLogin');
$router->post('/user-login', 'AuthController@processUserLogin');

$router->get('/admin-login', 'AuthController@adminLogin');
$router->post('/admin-login', 'AuthController@processAdminLogin');

$router->get('/admin-dashboard', 'DashboardController@admin');
$router->get('/agent-dashboard', 'DashboardController@agent');
$router->get('/user-dashboard', 'DashboardController@user');

$router->get('/logout', 'AuthController@logout');

$router->get('/mfa-setup', 'MfaController@setup');
$router->post('/mfa-setup', 'MfaController@verifySetup');

$router->get('/mfa-verify', 'MfaController@verifyPage');
$router->post('/mfa-verify', 'MfaController@verifyLogin');

$router->get('/mfa-recovery', 'MfaController@recoveryPage');
$router->post('/mfa-recovery', 'MfaController@sendRecoveryOtp');

$router->get('/mfa-recovery-verify', 'MfaController@recoveryVerifyPage');
$router->post('/mfa-recovery-verify', 'MfaController@verifyRecoveryOtp');

$router->get('/user-login-otp', 'AuthController@userOtpPage');
$router->post('/user-login-otp', 'AuthController@verifyUserOtp');

$router->get('/forgot-password', 'AuthController@forgotPasswordPage');
$router->post('/forgot-password', 'AuthController@sendForgotPasswordOtp');

$router->get('/forgot-password-verify', 'AuthController@forgotPasswordVerifyPage');
$router->post('/forgot-password-verify', 'AuthController@verifyForgotPasswordOtp');

$router->get('/reset-password', 'AuthController@resetPasswordPage');
$router->post('/reset-password', 'AuthController@resetPassword');

$router->get('/tickets', 'TicketController@index');
$router->get('/tickets/create', 'TicketController@create');
$router->post('/tickets/store', 'TicketController@store');

$router->get('/tickets/show/{id}', 'TicketController@show');

$router->post('/tickets/reply/{id}', 'TicketController@storeReply');

$router->get('/agent/tickets', 'AgentTicketController@index');
$router->get('/agent/tickets/show/{id}', 'AgentTicketController@show');
$router->post('/agent/tickets/reply/{id}', 'AgentTicketController@reply');
$router->post('/agent/tickets/status/{id}', 'AgentTicketController@updateStatus');

$router->get('/organization-users', 'OrganizationUserController@index');
$router->get('/organization-users/create', 'OrganizationUserController@create');
$router->post('/organization-users/create','OrganizationUserController@store');

$router->get('/admin/permissions', 'AdminPermissionController@index');
$router->get('/admin/permissions/edit/{id}', 'AdminPermissionController@edit');
$router->post('/admin/permissions/update/{id}', 'AdminPermissionController@update');

$router->get('/admin/users', 'AdminUserController@index');
$router->get('/admin/users/create', 'AdminUserController@create');
$router->post('/admin/users/create', 'AdminUserController@store');

$router->get('/admin/users/edit/{id}', 'AdminUserController@edit');
$router->post('/admin/users/update/{id}', 'AdminUserController@update');

$router->get('/admin/users/disable/{id}', 'AdminUserController@disable');

$router->get('/admin/organizations', 'AdminOrganizationController@index');
$router->get('/admin/organizations/create', 'AdminOrganizationController@create');
$router->post('/admin/organizations/create', 'AdminOrganizationController@store');

$router->get('/admin/organizations/edit/{id}', 'AdminOrganizationController@edit');
$router->post('/admin/organizations/update/{id}', 'AdminOrganizationController@update');

$router->get('/attachments/ticket/download/{id}', 'AttachmentController@downloadTicket');
$router->get('/attachments/reply/download/{id}', 'AttachmentController@downloadReply');

$router->get('/reports/tickets', 'ReportController@tickets');
$router->get('/reports/tickets/print', 'ReportController@printTickets');

$router->get('/reports/tickets/filter', 'ReportController@filterTickets');

$router->get('/reports/ticket-detail', 'ReportController@ticketDetail');

$router->get('/profile', 'ProfileController@index');

$router->get('/admin/activity-logs', 'ActivityLogController@index');

$router->get('/organizations', 'AdminOrganizationController@index');
$router->get('/organizations/create', 'AdminOrganizationController@create');
$router->post('/organizations/create', 'AdminOrganizationController@store');

$router->get('/organizations/edit/{id}', 'AdminOrganizationController@edit');
$router->post('/organizations/update/{id}', 'AdminOrganizationController@update');

$router->get('/agent/tickets/create', 'AgentTicketController@create');
$router->post('/agent/tickets/store', 'AgentTicketController@store');
