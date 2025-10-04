<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\ExpenseList;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Livewire\Admin\Users\CreateUser;
use App\Livewire\Admin\Users\EditUser;
use App\Livewire\Admin\Users\UserCreateList;
use App\Livewire\Admin\Collect\CollectList;
use App\Livewire\Admin\Collectors\CollectorList;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;

if (env('APP_ENV') === 'production') {
    URL::forceScheme('https');
}

Route::get('/enviawpp', function () {
    $token = 'EAASVVbJ8DL8BPMDHaW5DTHyoZBV8ZCMGZBc7YFWsADntJplLH6ZABEC9SCmYeGXuj9ZBMOExCV0gHhvSulOQWlaLF4uXV2PV8x3DmSkCRpiOvXaQZCZCQ9NdRVZAzuZBserYaPUDBOT0qHXIYS2pyJwP1TTN0YYSLbJmp2SJoqxMpfFkouejZCnaXnLyTe6G7oshmWZAjn5siXrnh4dFZBP27CypEwEcC2K5IzacaZAPwZB2WHiluz0wZDZD';

    $telefono = '523122133120';

    $response = Http::withToken($token)->post('https://graph.facebook.com/v22.0/624771680729325/messages', [
        'messaging_product' => 'whatsapp',
        'to' => $telefono,
        'type' => 'template',
        'template' => [
            'name' => 'hello_world',
            'language' => [
                'code' => 'en_US',
            ],
        ],
    ]);

    return response()->json([
        'status' => $response->status(),
        'body' => $response->json(),
    ]);
});


Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    //Admin Panel 
    Route::get('/dashboard', function () {
        $user = Auth()->user();

        return match ($user->user_type) {
            0 => redirect()->route('admin.dashboard'),
            //1 => redirect()->route('cobrador.dashboard'),
            default => abort(403)
        };
    })->name('dashboard');

    Route::get('/admin/dashboard', \App\Livewire\Admin\Dashboard::class)->name('admin.dashboard');//->middleware('admin');
    Route::get('/admin/loans', App\Livewire\Admin\Loans::class)->name('admin.loans');//->middleware('admin');
    Route::get('/admin/loans/create', App\Livewire\Admin\Loans\Create::class)->name('admin.loans.create');
    Route::get('/admin/loans/loanlist', App\Livewire\Admin\Loans\LoanList::class)->name('admin.loans.loanlist');
    Route::get('/admin/loantypes/create', \App\Livewire\Admin\LoanTypes\Create::class)->name('admin.loantypes.create');
    Route::get('/admin/loantypes/loantypeslist', \App\Livewire\Admin\LoanTypes\LoanTypeList::class)->name('admin.loantypes.loantypeslist');
    Route::get('/admin/users/create', CreateUser::class)->name('admin.users.create');
    Route::get('/admin/users/createlist', UserCreateList::class)->name('admin.users.user.create.list');
    Route::get('/admin/users/edit/{user_id}', EditUser::class)->name('admin.users.edit');
    Route::get('/admin/collect', CollectList::class)->name('admin.collect');
    Route::get('/expenses', ExpenseList::class)->name('expenses.list');
    Route::get('/admin/collectors', CollectorList::class)->name('admin.collectors');
//  Route::get('/cobrador/dashboard', \App\Livewire\Cobrador\Dashboard::class)
//  ->name('cobrador.dashboard')
// ->middleware('cobrador');
});

// Route::middleware(['auth', 'admin'])->prefix('admin/loans')->name('admin.loans.')->group(function () {
//     Route::get('/create', App\Livewire\Admin\Loans\Create::class)->name('create');
//     // Route::get('/list', App\Livewire\Admin\Loans\List::class)->name('list');
// });


// Route::middleware(['auth'])->group(function () {
//     Route::get('/dashboard', function () {

//         $user = Auth()->user();

//         return match ($user->user_type) {
//             0 => redirect()->route('admin.dashboard'),
//             //1 => redirect()->route('cobrador.dashboard'),
//             default => abort(403)
//         };
//     })->name('dashboard');

//     Route::get('/admin/dashboard', \App\Livewire\Admin\Dashboard::class)
//         ->name('admin.dashboard')
//         ->middleware('admin');

//     // Route::get('/cobrador/dashboard', \App\Livewire\Cobrador\Dashboard::class)
//     //     ->name('cobrador.dashboard')
//     //     ->middleware('cobrador');
// });



require __DIR__.'/auth.php';
