<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    private function pass_verify($password, $hash_pass)
    {
        return password_verify($password, $hash_pass);
    }
    public function loginwithtoken(Request $request)
    {
        $name = $request->name;
        $password = $request->passkey;

        $user = User::where('name', $name)->get();
        if (isset($user[0])) {
            //$pass_verify = $this->pass_verify($user[0]->name,$password);
            //if($pass_verify){
            Auth::loginUsingId($user[0]->id);
            return redirect(route('dashboard'));
            //	} else {
            //	return redirect(env('BASE').'auth/login');
            //}
        } else {
            return redirect(env('BASE') . 'auth/login');
        }
    }

    public function logout(Request $request)
    {
        //dd($request);
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();
        //echo env('BASE').'/auth/logut';
        return redirect('login');
    }
}
