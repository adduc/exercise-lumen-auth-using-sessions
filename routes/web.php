<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Models;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Routing\Router;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', fn () => <<<HTML
    <h1>Lumen Auth: Session Example</h1>
    <ul>
        <li><a href="/session/flush">Clear Session</a></li>
        <li><a href="/session/invalidate">Clear Invalidate</a></li>
        <li><a href="/session/register">Register</a></li>
        <li><a href="/session/login">Login</a></li>
        <li><a href="/session/status">Authentication Status</a></li>
        <li><a href="/session/gate">Gated Endpoint</a></li>
    </ul>
HTML);

$router->group(['middleware' => ['session', 'auth-guard:session']], function (Router $router) {
    $router->get('/session/flush', function (Request $request) {
        $request->session()->flush();
        return 'Session flushed';
    });

    $router->get('/session/invalidate', function (Request $request) {
        $request->session()->invalidate();
        return 'Session invalidated';
    });

    $router->get('/session/register', fn () => view('session-register'));

    $router->post('/session/register', function (Request $request, Auth $auth) use ($router) {
        $user = Models\User::create([
            'username' => $request->input('user'),
            'password' => app('hash')->make($request->input('pass')),
        ]);

        auth()->login($user);

        return redirect('/session/gate');
    });

    $router->get('/session/login', fn () => view('session-login'));

    $router->post('/session/login', function (Request $request) {

        $data = [
            'username' => $request->input('user'),
            'password' => $request->input('pass'),
        ];

        if (!auth()->attempt($data)) {
            return redirect('/session/login');
        }

        return redirect('/session/gate');
    });

    $router->get('/session/status', function (Request $request) {
        $user = $request->user();
        return $user ? 'Logged In as ' . $user->username : 'Not Logged In';
    });

    $router->get('/session/gate', ['middleware' => 'auth:session', function (Request $request) {
        return $request->user();
    }]);
});
