<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Flash;

class AuthController {
    public function login() {
        if (Auth::check()) {
            Response::redirect('dashboard');
        }

        if (Request::isPost()) {
            $username = Request::post('username');
            $password = Request::post('password');

            if (empty($username) || empty($password)) {
                Flash::error('Por favor, ingrese todos los campos.');
                Response::redirect('');
            }

            if (Auth::attempt($username, $password)) {
                Response::redirect('dashboard');
            } else {
                Flash::error('Usuario o contraseña incorrectos.');
                Response::redirect('');
            }
        }

        View::render('login', ['useBase' => false]);
    }

    public function logout() {
        Auth::logout();
        Response::redirect('');
    }
}
