<?php
declare(strict_types=1);

// AUTHENTICATION

Route::group(['middleware' => ['auth:api'], 'namespace' => 'Auth', 'prefix' => 'auth'], function () {

    Route::get('me', 'AuthController@signIn');
    Route::post('logout', 'AuthController@logoutApi');
});

// CREATE_COMPANY
Route::group(['prefix' => 'create-company'], function () {

    // Create Company
    Route::post('create-mail', 'CompanyController@createCompanyEmail');
    Route::post('check-confirm-email', 'CompanyController@checkConfirmEmail');
    Route::post('check-confirm-key', 'CompanyController@checkConfirmKey');
    Route::post('create', 'CompanyController@createCompany');

    // Sign In
    Route::post('sign-in/company-name', 'CompanyController@checkCompanyName');
    Route::post('sign-in/user-data', 'UserController@checkUserData');
    Route::post('sign-in/auto-login', 'UserController@autoLogin');

    // Reset Password
    Route::post('reset-password', 'UserController@resetPassword');
    Route::post('reset-password/check-key', 'UserController@resetPasswordCheckKey');
    Route::put('reset-password/new-password', 'UserController@resetPasswordNewPassword');

    // User Invite Check Token
    Route::post('invited-user-register-sign-in', 'UserController@invitationUserCreate');
    Route::post('check-invite-token', 'UserController@checkInviteToken');
});

// For allUser
Route::group(['prefix' => 'panel', 'middleware' => ['auth:api', 'check.data']], function () {

    // Menu  && Content
    Route::get('get-menu', 'CompanyController@getMenu');

    /**
     * @see App\Services\Traits\PositioningTrait
     */
    Route::put('structured-group-menu', 'PositioningController@reStructuredGroupByMenu');
    Route::put('structured-document-menu', 'PositioningController@reStructuredDocumentByMenu');

    Route::post('get-content', 'DocumentController@getContent');
});

// For Leader Only
Route::group(
    ['prefix' => 'panel-leader', 'middleware' => ['auth:api', 'check.data', 'is.leader', 'scopes']], function () {

    // Send User Invite Email
    Route::post('get-user-invite', 'UserController@inviteUser');

    // Get groups and documents for user invite
    Route::get('get-groups', 'GroupController@getGroups');
    Route::get('get-documents', 'DocumentController@getDocuments');

    // Add Document
    Route::post('add-document', 'DocumentController@addDocument');

    // Edit Document
    Route::put('edit-document', 'DocumentController@editDocument');

    // Delete Document
    Route::delete('delete-document/{document_slug_name}', 'DocumentController@deleteDocument');

    // Add Group
    Route::post('add-group', 'GroupController@addGroup');
    Route::post('add-documents-in-group', 'DocumentController@addDocuments');
    Route::put('rename-group', 'GroupController@renameGroup');
    Route::delete('delete-group/{group_id}', 'GroupController@deleteGroup');

    // Get All users current Company
    Route::get('get-users-settings-panel', 'UserController@getAllUsersForCompany');
    Route::post('get-users-for-accessed-document', 'UserController@getAllUsersForCompany');

    // Permissions and Settings |>
    // get Permission Selected Document for selected user
    Route::post('check-user-has-document', 'DocumentController@getHasDocument');
    Route::put('edit-user-document-role', 'DocumentController@editDocumentRole');
    Route::post('edit-user-document-group', 'DocumentController@editDocumentGroup');
    Route::post('add-document-for-user', 'DocumentController@addUserInDocument');

    Route::delete('delete-document-in-group/{document_id}/{group_id}', 'DocumentController@deleteDocumentInGroup');
    Route::delete('delete-user-in-company/{userId}', 'UserController@deleteUserInCompany');

    Route::put('change-password', 'UserController@changePassword');
});
