<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Home;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\JobController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [Home::class, 'index'])->name('home');
Route::get('/jobs', [JobController::class, 'index'])->name('jobs');
// Route::get('/job/details/{jobid}', [JobController::class, 'jobdetails'])->name('job.details');

Route::get('/job/details/{jobid}', [JobController::class, 'jobdetails'])->name('job.details');



Route::prefix('account')->group( function(){
 
    //guest Routes

   Route::group(['middleware' => 'guest'], function(){
            Route::get('/register', [AccountController::class, 'registration'])->name('account.register');
            Route::post('/process-register', [AccountController::class, 'registerprocess'])->name('account.register-process');
            Route::get('/login', [AccountController::class, 'login'])->name('account.login');
            Route::post("/account/authenticate", [AccountController::class, 'authenitcateUser'])->name('account.authenticate');
           });

    // Authenticated Routes

    Route::group(['middleware' => 'auth'], function(){
        
        Route::get('/profile', [AccountController::class, 'profilepage'])->name('account.profile');
        Route::get('/logout', [AccountController::class, 'logout'])->name('account.logout');
        Route::put('/profileupdate', [AccountController::class, 'updateprofie'])->name('account.updateprofie');
        Route::post('/profilepicupdate', [AccountController::class, 'updateProfilePic'])->name('account.update.profile.pic');
        Route::get('/createjob', [AccountController::class, 'createjob'])->name('account.create.job');
        Route::post('/postjob', [AccountController::class, 'saveJobs'])->name('account.post.job');
        Route::get('/myjobs', [AccountController::class, 'myjobs'])->name('account.myjobs');
        Route::get('/myjobs/edit/{jobid}',[ AccountController::class, 'editmyjob'])->name('account.edit.job');
        Route::post('/update-job/{jobid}',[AccountController::class, 'updatejob'])->name('account.updade.job');
        Route::post('/deletejob', [AccountController::class, 'deletejob'])->name('account.delete.job');
        Route::post('/applyjob', [JobController::class, 'applyjob'])->name('account.apply.job');

       
    });


});