<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Failed Subscriptions
|--------------------------------------------------------------------------
|
| If a customer's credit card expires,
| Cashier includes a Webhook controller that can easily cancel the customer's subscription for you
| Just point a route to the controller.
*/

Route::post(
    'stripe/webhook',
    '\Laravel\Cashier\Http\Controllers\WebhookController@handleWebhook'
);

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => 'web'], function () {

    Route::auth();
    Route::group(['middleware' => 'locale'], function () {

        Route::get('/home', 'HomeController@index');

        /*
        |--------------------------------------------------------------------------
        | Routes for Subscriptions
        |--------------------------------------------------------------------------
        |
        | Routes for cancel and update subscriptions
        */

        /* View subscription update page */
        Route::get('/subscription/plan/update/page', [
            'as' => 'subscriptionPlanUpdatePage', 'uses' => 'SubscriptionController@planUpdatePage'
        ]);

        /* Update subscription plan */
        Route::post('/subscription/plan/update/save', [
            'as' => 'subscriptionPlanUpdate', 'uses' => 'SubscriptionController@planUpdate'
        ]);

        /* Cancel subscription plan */
        Route::get('/subscription/plan/cancel', [
            'as' => 'subscriptionCancel', 'uses' => 'SubscriptionController@cancelPlan'
        ]);

        /* Resume subscription plan */
        Route::get('/subscription/plan/resume', [
            'as' => 'subscriptionResume', 'uses' => 'SubscriptionController@resumePlan'
        ]);

        /* View page of update card*/
        Route::get('/subscription/card/update/page', [
            'as' => 'subscriptionCardUpdatePage', 'uses' => 'SubscriptionController@cardUpdatePage'
        ]);

        /* Update card details */
        Route::post('/subscription/card/update', [
            'as' => 'subscriptionCardUpdate', 'uses' => 'SubscriptionController@cardUpdate'
        ]);

        /*Download the invoices for the customer*/
        Route::get('user/invoice/{invoice}', [
            'as' => 'subscriptionInvoiceDownload', 'uses' => 'SubscriptionController@downloadInvoice'
        ]);

        /*
        |--------------------------------------------------------------------------
        | Routes for Customers
        |--------------------------------------------------------------------------
        |
        | Routes for create, add, list customers
        */

        /* View customer page */
        Route::get('/customer/create', [
            'as' => 'customerCreate', 'uses' => 'CustomerController@index'
        ]);

        /* Store customer details */
        Route::post('/customer/save', [
            'as' => 'customerSave', 'uses' => 'CustomerController@store'
        ]);

        /* List customers data*/
        Route::get('/customers', [
            'as' => 'customerList', 'uses' => 'CustomerController@show'
        ]);

        /* Edit customers data*/
        Route::get('/customer/edit/{id}', [
            'as' => 'customerEdit', 'uses' => 'CustomerController@edit'
        ])->where(['id' => '[0-9]+']);

        /* Update customers data*/
        Route::post('/customer/update/{id}', [
            'as' => 'customerUpdate', 'uses' => 'CustomerController@update'
        ])->where(['id' => '[0-9]+']);

        /* Delete customers data*/
        Route::post('/customer/delete/{id}', [
            'as' => 'customerDelete', 'uses' => 'CustomerController@destroy'
        ])->where(['id' => '[0-9]+']);

        /*
        |--------------------------------------------------------------------------
        | Routes for user / company registration
        |--------------------------------------------------------------------------
        |
        | Routes for create, add company statuses
        */

        /* View company statuses page */
        Route::get('/company/statuses/create', [
            'as' => 'companyStatusesCreate', 'uses' => 'CompanyStatusesController@index'
        ]);

        /* Store company statuses details */
        Route::post('/company/statuses/save', [
            'as' => 'companyStatusesSave', 'uses' => 'CompanyStatusesController@store'
        ]);

        /* List company statuses data - for ajax only*/
        Route::get('/company/statuses/', [
            'as' => 'companyStatuseslist', 'uses' => 'CompanyStatusesController@showListData'
        ]);

        /* View company status page */
        Route::get('/company/statuses/list/{id}', [
            'as' => 'companyStatusEdit', 'uses' => 'CompanyStatusesController@edit'
        ]);

        /* Update company status */
        Route::post('/company/statuses/list/{id}/update', [
            'as' => 'companyStatusUpdate', 'uses' => 'CompanyStatusesController@update'
        ]);

        /* Update sort_id of company status */
        Route::post('/company/statuses/list/update/sort/{ID}/{sortID}', [
            'as' => 'companyStatusUpdateSort', 'uses' => 'CompanyStatusesController@updateSort'
        ]);

        /* Delete company status */
        Route::post('company/status/delete/{id}', [
            'as' => 'companyStatusDelete', 'uses' => 'CompanyStatusesController@destroy'
        ])->where(['id' => '[0-9]+']);

        /*
        |--------------------------------------------------------------------------
        | Routes for process
        |--------------------------------------------------------------------------
        |
        | Routes for create, add, list process
        */

        /* View process page */
        Route::get('/customer/process/create/{id}', [
            'as' => 'customerProcessCreate', 'uses' => 'ProcessController@create'
        ])->where(['id' => '[0-9]+']);

        /* Store process details */
        Route::post('/customer/process/save', [
            'as' => 'customerProcessSave', 'uses' => 'ProcessController@store'
        ]);

        /* List process of customers*/
        Route::get('/customers/list/process/{id}', [
            'as' => 'customerListProcess', 'uses' => 'ProcessController@show'
        ])->where(['id' => '[0-9]+']);

        /* List processes data*/
        Route::get('/processes', [
            'as' => 'processesList', 'uses' => 'ProcessController@showProcessesData'
        ]);

        /* Edit process data*/
        Route::get('/process/edit/{id}', [
            'as' => 'processesEdit', 'uses' => 'ProcessController@edit'
        ])->where(['id' => '[0-9]+']);

        /* Update process data*/
        Route::post('/process/update/{id}', [
            'as' => 'processesUpdate', 'uses' => 'ProcessController@update'
        ])->where(['id' => '[0-9]+']);

        /* Delete process data*/
        Route::post('/process/delete/{id}', [
            'as' => 'processesDelete', 'uses' => 'ProcessController@destroy'
        ])->where(['id' => '[0-9]+']);

        /*
        |--------------------------------------------------------------------------
        | Routes for process entries
        |--------------------------------------------------------------------------
        |
        | Routes for list process entries
        */

        /* List process entries data */
        Route::get('/customers/list/process/{id}/entries', [
            'as' => 'customerProcessEntries', 'uses' => 'ProcessEntriesController@show'
        ])->where(['id' => '[0-9]+']);

        /* View process entries page */
        Route::get('/customers/list/process/{id}/entries/create', [
            'as' => 'customerProcessEntriesCreate', 'uses' => 'ProcessEntriesController@index'
        ])->where(['id' => '[0-9]+']);

        /* Store process entries */
        Route::post('/customers/list/process/{id}/entries/save', [
            'as' => 'customerProcessEntriesSave', 'uses' => 'ProcessEntriesController@store'
        ]);

        /* Store process entries file*/
        Route::post('/customers/list/process/{id}/entries/file', [
            'as' => 'customerProcessEntriesSaveFile', 'uses' => 'ProcessEntriesController@storeFile'
        ])->where(['id' => '[0-9]+']);

        /* Store process entries file details*/
        Route::post('/customers/list/process/entries/{eid}/file/details/{fid}', [
            'as' => 'customerProcessEntriesSaveFile', 'uses' => 'ProcessEntriesController@storeFileDetails'
        ]);

        /* Get process entries file details*/
        Route::post('/customers/list/process/entries/file/details/get', [
            'as' => 'customerProcessEntriesGetFileDetails', 'uses' => 'ProcessEntriesController@getFileDetails'
        ]);

        /* View process entry page */
        Route::get('/customers/list/process/entries/{id}', [
            'as' => 'customerProcessEntriesEdit', 'uses' => 'ProcessEntriesController@edit'
        ])->where(['id' => '[0-9]+']);

        /* Process entries update*/
        Route::post('/customers/list/process/entries/{id}/update', [
            'as' => 'customerProcessEntriesUpdate', 'uses' => 'ProcessEntriesController@update'
        ]);

        /* Process entries files update*/
        Route::post('/customers/list/process/entries/{id}/update/file', [
            'as' => 'customerProcessEntriesFileUpdate', 'uses' => 'ProcessEntriesController@updateFile'
        ]);

        /* Download process entry file */
        Route::get('/customers/list/process/entries/{id}/file/download/{file}', [
            'as' => 'customerProcessEntriesFileDownload', 'uses' => 'ProcessEntriesController@fileDownload'
        ]);

        /* Delete process entry file */
        Route::get('/customers/list/process/entries/{id}/file/delete/{file}', [
            'as' => 'customerProcessEntriesFileDelete', 'uses' => 'ProcessEntriesController@destroy'
        ]);

        /* Delete process entry data*/
        Route::post('/process/entry/delete/{id}', [
            'as' => 'processesEntryDelete', 'uses' => 'ProcessEntriesController@deleteEntry'
        ])->where(['id' => '[0-9]+']);

        /* View process entry comments*/
        Route::get('/customers/list/process/entry/{id}/comments', [
            'as' => 'processesEntryComments', 'uses' => 'ProcessEntriesController@showComments'
        ])->where(['id' => '[0-9]+']);

        /* Post process entry comments*/
        Route::post('/process/entry/comment/post', [
            'as' => 'processesEntryCommentPost', 'uses' => 'ProcessEntriesController@postComment'
        ])->where(['id' => '[0-9]+']);

        /*Company user's profile view/edit*/
        Route::get('/company/profile', ['uses'=>'CompanyProfileController@showProfile']);

        /*Company user's profile update*/
        Route::post('/company/profile/update', ['uses'=>'CompanyProfileController@updateProfile']);

        /*Company user's profile settings - change password, statuses */
        Route::get('/company/profile/settings', ['uses'=>'CompanyProfileController@showSettings']);

        /*Company user's profile password update*/
        Route::post('/company/profile/password/update', ['uses'=>'CompanyProfileController@updatePassword']);

        Route::get('/search', ['uses'=>'SearchController@search']);

    });

});


    /*
    |--------------------------------------------------------------------------|
    | Routes for customer frontend page / login and customer-timeline          |
    |--------------------------------------------------------------------------|
    */




////////////////Customer Email verification////////////////
Route::get('/customer/activation/{param}', 'ActivationController@activateAccount');

////////////////Customer LOGIN////////////////
Route::get('/customer/login', 'Customerauth\AuthController@showLoginForm');

Route::post('/customer/login', 'Customerauth\AuthController@login');

Route::get('/profile', 'Customerauth\AuthController@showProfile');

////////////////Customer Password Reset////////////////
Route::get('/customer/password/reset/{token?}', 'Customerauth\PasswordController@showResetForm');

Route::post('/customer/password/email', 'Customerauth\PasswordController@sendResetLinkEmail');

Route::post('/customer/password/reset', 'Customerauth\PasswordController@reset');


////////////////Customer Logged In actions////////////////
Route::group(['middleware' => 'customer'], function () {

    Route::get('/customer/logout', 'Customerauth\AuthController@logout');

    Route::get('/customer/password/change', 'CustomerFrontendPageController@changePassword');

    Route::post('/customer/password/update', 'CustomerFrontendPageController@updatePassword');

    Route::get('/{hash}', [
        'as' => 'customerFrontendPageCreate', 'uses' => 'CustomerFrontendPageController@show'
    ]);

    Route::get('/entryImage/{path}', 'CustomerFrontendPageController@showImage')
        ->where('path', '[A-Za-z0-9\/\-\.\+]+');

    Route::get('/entry/download/{path}', 'CustomerFrontendPageController@getEntryDownload')
        ->where('path', '[A-Za-z0-9\/\-\.\+]+');

    Route::post('/entry/history/update', 'CustomerFrontendPageController@updateHistory');

    Route::post('/entry/comment/post', 'CustomerFrontendPageController@postComment');

});

        
