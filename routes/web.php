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
