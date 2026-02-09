<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AiapplicationController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\ComponentpageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FormsController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\KeywordsController;
use App\Http\Controllers\ActivitiesController;
use App\Http\Controllers\YandexMetrikaController;


Route::controller(DashboardController::class)->group(function () {
    Route::get('/', 'index')->name('index')->middleware('auth');
});


Route::controller(HomeController::class)->group(function () {
    Route::get('calendar','calendar')->name('calendar');
    Route::get('chatmessage','chatMessage')->name('chatMessage');
    Route::get('chatempty','chatEmpty')->name('chatEmpty');
    Route::get('email','email')->name('email');
    Route::get('error','error1')->name('error');
    Route::get('faq','faq')->name('faq');
    Route::get('gallery','gallery')->name('gallery');
    Route::get('kanban','kanban')->name('kanban');
    Route::get('pricing','pricing')->name('pricing');
    Route::get('termscondition','termsCondition')->name('termsCondition');
    Route::get('widgets','widgets')->name('widgets');
    Route::get('chatprofile','chatProfile')->name('chatProfile');
    Route::get('veiwdetails','veiwDetails')->name('veiwDetails');
    });

    // aiApplication
Route::prefix('aiapplication')->group(function () {
    Route::controller(AiapplicationController::class)->group(function () {
        Route::get('/codegenerator', 'codeGenerator')->name('codeGenerator');
        Route::get('/codegeneratornew', 'codeGeneratorNew')->name('codeGeneratorNew');
        Route::get('/imagegenerator','imageGenerator')->name('imageGenerator');
        Route::get('/textgeneratornew','textGeneratorNew')->name('textGeneratorNew');
        Route::get('/textgenerator','textGenerator')->name('textGenerator');
        Route::get('/videogenerator','videoGenerator')->name('videoGenerator');
        Route::get('/voicegenerator','voiceGenerator')->name('voiceGenerator');
    });
});

// Authentication
Route::prefix('authentication')->group(function () {
    Route::controller(AuthenticationController::class)->group(function () {
        Route::get('/forgotpassword', 'forgotPassword')->name('forgotPassword');
        Route::get('/login', 'signIn')->name('login')->middleware('guest');
        Route::get('/signup', 'signUp')->name('signUp')->middleware('guest');
    });

    // POST маршруты для обработки аутентификации
    Route::controller(AuthController::class)->group(function () {
        Route::post('/login', 'login')->name('login-post')->middleware('guest');
        Route::post('/register', 'register')->name('register')->middleware('guest');
        Route::post('/logout', 'logout')->name('logout')->middleware('auth');
    });
});

// chart
Route::prefix('chart')->group(function () {
    Route::controller(ChartController::class)->group(function () {
        Route::get('/columnchart', 'columnChart')->name('columnChart');
        Route::get('/linechart', 'lineChart')->name('lineChart');
        Route::get('/piechart', 'pieChart')->name('pieChart');
    });
});

// Componentpage
Route::prefix('componentspage')->group(function () {
    Route::controller(ComponentpageController::class)->group(function () {
        Route::get('/alert', 'alert')->name('alert');
        Route::get('/avatar', 'avatar')->name('avatar');
        Route::get('/badges', 'badges')->name('badges');
        Route::get('/button', 'button')->name('button');
        Route::get('/calendar', 'calendar')->name('calendar');
        Route::get('/card', 'card')->name('card');
        Route::get('/carousel', 'carousel')->name('carousel');
        Route::get('/colors', 'colors')->name('colors');
        Route::get('/dropdown', 'dropdown')->name('dropdown');
        Route::get('/imageupload', 'imageUpload')->name('imageUpload');
        Route::get('/list', 'list')->name('list');
        Route::get('/pagination', 'pagination')->name('pagination');
        Route::get('/progress', 'progress')->name('progress');
        Route::get('/radio', 'radio')->name('radio');
        Route::get('/starrating', 'starRating')->name('starRating');
        Route::get('/switch', 'switch')->name('switch');
        Route::get('/tabs', 'tabs')->name('tabs');
        Route::get('/tags', 'tags')->name('tags');
        Route::get('/tooltip', 'tooltip')->name('tooltip');
        Route::get('/typography', 'typography')->name('typography');
        Route::get('/videos', 'videos')->name('videos');
    });
});

// Dashboard
Route::prefix('dashboard')->group(function () {
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/index', 'index')->name('index');
        Route::get('/index2', 'index2')->name('index2');
        Route::get('/index3', 'index3')->name('index3');
        Route::get('/index4', 'index4')->name('index4');
        Route::get('/index5','index5')->name('index5');
        Route::get('/wallet','wallet')->name('wallet');
    });
});

// Forms
Route::prefix('forms')->group(function () {
    Route::controller(FormsController::class)->group(function () {
        Route::get('/form-layout', 'formLayout')->name('formLayout');
        Route::get('/form-validation', 'formValidation')->name('formValidation');
        Route::get('/form', 'form')->name('form');
        Route::get('/wizard', 'wizard')->name('wizard');
    });
});

// invoice/invoiceList
Route::prefix('invoice')->group(function () {
    Route::controller(InvoiceController::class)->group(function () {
        Route::get('/invoice-add', 'invoiceAdd')->name('invoiceAdd');
        Route::get('/invoice-edit', 'invoiceEdit')->name('invoiceEdit');
        Route::get('/invoice-list', 'invoiceList')->name('invoiceList');
        Route::get('/invoice-preview', 'invoicePreview')->name('invoicePreview');
    });
});

// Settings
Route::prefix('settings')->group(function () {
    Route::controller(SettingsController::class)->group(function () {
        Route::get('/', 'index')->name('settings');
        Route::post('/', 'index')->name('settings.store');
        Route::get('/company', 'company')->name('company');
        Route::get('/currencies', 'currencies')->name('currencies');
        Route::get('/language', 'language')->name('language');
        Route::get('/notification', 'notification')->name('notification');
        Route::get('/notification-alert', 'notificationAlert')->name('notificationAlert');
        Route::get('/payment-gateway', 'paymentGateway')->name('paymentGateway');
        Route::get('/theme', 'theme')->name('theme');
    });
});

// Table
Route::prefix('table')->group(function () {
    Route::controller(TableController::class)->group(function () {
        Route::get('/tablebasic', 'tableBasic')->name('tableBasic');
        Route::get('/tabledata', 'tableData')->name('tableData');
    });
});

// Users
Route::prefix('users')->group(function () {
    Route::controller(UsersController::class)->group(function () {
        Route::get('/add-user', 'addUser')->name('addUser');
        Route::get('/users-grid', 'usersGrid')->name('usersGrid');
        Route::get('/users-list', 'usersList')->name('usersList');
        Route::get('/view-profile', 'viewProfile')->name('viewProfile');
    });
});

    // Projects
Route::prefix('projects')->group(function () {
    Route::controller(ProjectsController::class)->group(function () {
        Route::get('/', 'index')->name('projects.index');
        Route::get('/create', 'create')->name('projects.create');
        Route::post('/', 'store')->name('projects.store');
        Route::get('/{project}', 'show')->name('projects.show');
        Route::get('/{project}/edit', 'edit')->name('projects.edit');
        Route::put('/{project}', 'update')->name('projects.update');
        Route::delete('/{project}', 'destroy')->name('projects.destroy');
    });

    // Keywords (nested under project - all keywords in project)
    Route::prefix('{project}/keywords')->group(function () {
        Route::controller(KeywordsController::class)->group(function () {
            Route::get('/', 'indexForProject')->name('projects.keywords.index');
        });
    });

    // Activities (nested under project - general project activities)
    Route::prefix('{project}/activities')->group(function () {
        Route::controller(ActivitiesController::class)->group(function () {
            Route::get('/', 'index')->name('projects.activities.index');
            Route::get('/create', 'create')->name('projects.activities.create');
            Route::post('/', 'store')->name('projects.activities.store');
            Route::get('/{activity}/edit', 'edit')->name('projects.activities.edit');
            Route::put('/{activity}', 'update')->name('projects.activities.update');
            Route::delete('/{activity}', 'destroy')->name('projects.activities.destroy');
        });
    });

    // Donors (nested under project - all donors)
    Route::prefix('{project}/donors')->group(function () {
        Route::controller(DonorController::class)->group(function () {
            Route::get('/', 'index')->name('projects.donors.index');
        });
    });

    // Pages (nested resource)
    Route::prefix('{project}/pages')->group(function () {
        Route::controller(PagesController::class)->group(function () {
            Route::get('/', 'index')->name('projects.pages.index');
            Route::get('/create', 'create')->name('projects.pages.create');
            Route::post('/', 'store')->name('projects.pages.store');
            Route::post('/import', 'import')->name('projects.pages.import');
            Route::get('/{page}', 'show')->name('projects.pages.show');
            Route::get('/{page}/edit', 'edit')->name('projects.pages.edit');
            Route::put('/{page}', 'update')->name('projects.pages.update');
            Route::delete('/{page}', 'destroy')->name('projects.pages.destroy');
        });

         // Donors (nested under pages)
         Route::prefix('{page}/donors')->group(function () {
             Route::controller(DonorController::class)->group(function () {
                 Route::get('/', 'indexForPage')->name('projects.pages.donors.index');
                 Route::get('/create', 'create')->name('projects.pages.donors.create');
                 Route::post('/', 'store')->name('projects.pages.donors.store');
                 Route::get('/{donor}', 'show')->name('projects.pages.donors.show');
                 Route::get('/{donor}/edit', 'edit')->name('projects.pages.donors.edit');
                 Route::put('/{donor}', 'update')->name('projects.pages.donors.update');
                 Route::delete('/{donor}', 'destroy')->name('projects.pages.donors.destroy');
             });
         });

         // Keywords (nested under pages)
         Route::prefix('{page}/keywords')->group(function () {
             Route::controller(KeywordsController::class)->group(function () {
                 Route::get('/', 'index')->name('projects.pages.keywords.index');
                 Route::get('/create', 'create')->name('projects.pages.keywords.create');
                 Route::post('/', 'store')->name('projects.pages.keywords.store');
                 Route::get('/{keyword}', 'show')->name('projects.pages.keywords.show');
                 Route::get('/{keyword}/edit', 'edit')->name('projects.pages.keywords.edit');
                 Route::put('/{keyword}', 'update')->name('projects.pages.keywords.update');
                 Route::delete('/{keyword}', 'destroy')->name('projects.pages.keywords.destroy');
             });
         });

         // Activities (nested under pages)
         Route::prefix('{page}/activities')->group(function () {
             Route::controller(ActivitiesController::class)->group(function () {
                 Route::get('/', 'index')->name('projects.pages.activities.index');
                 Route::get('/create', 'create')->name('projects.pages.activities.create');
                 Route::post('/', 'store')->name('projects.pages.activities.store');
                 Route::get('/{activity}/edit', 'edit')->name('projects.pages.activities.edit');
                 Route::put('/{activity}', 'update')->name('projects.pages.activities.update');
                 Route::delete('/{activity}', 'destroy')->name('projects.pages.activities.destroy');
          });
     });
});

// Яндекс Метрика
Route::prefix('yandex-metrika')->group(function () {
    Route::controller(YandexMetrikaController::class)->group(function () {
        Route::get('/data', 'getData')->name('yandex.metrika.data')->middleware('auth');
    });
});
});
