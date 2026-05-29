<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Session;
use App\Core\Validator;
use App\Models\User;
use App\Models\Cart;

class AuthController extends Controller
{
    public function showLogin(): void
    {
        if (Auth::check()) { $this->redirect('/'); }
        $this->view('auth/login', ['title' => 'Đăng nhập']);
    }

    public function login(): void
    {
        Csrf::verify();
        $email = $this->input('email');
        $password = $this->input('password');

        if (Auth::attempt($email, $password)) {
            // Gộp giỏ khách vào giỏ user
            (new Cart())->mergeSessionToUser(session_id(), Auth::id());
            Session::flash('success', 'Đăng nhập thành công.');
            $this->redirect(Auth::isAdmin() ? '/admin' : '/');
        }
        Session::flash('error', 'Email hoặc mật khẩu không đúng.');
        $this->view('auth/login', ['title' => 'Đăng nhập', 'email' => $email]);
    }

    public function showRegister(): void
    {
        if (Auth::check()) { $this->redirect('/'); }
        $this->view('auth/register', ['title' => 'Đăng ký']);
    }

    public function register(): void
    {
        Csrf::verify();
        $data = [
            'name'                  => $this->input('name'),
            'email'                 => $this->input('email'),
            'password'              => $this->input('password'),
            'password_confirmation' => $this->input('password_confirmation'),
            'phone'                 => $this->input('phone'),
        ];

        $v = new Validator($data);
        $ok = $v->validate([
            'name'     => 'required|max:120',
            'email'    => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $userModel = new User();
        if ($ok && $userModel->findByEmail($data['email'])) {
            $v = new Validator($data); // reset
            $errors = ['email' => ['Email đã được sử dụng.']];
            $this->view('auth/register', ['title' => 'Đăng ký', 'errors' => $errors, 'old' => $data]);
            return;
        }

        if (!$ok) {
            $this->view('auth/register', ['title' => 'Đăng ký', 'errors' => $v->errors(), 'old' => $data]);
            return;
        }

        $id = $userModel->create($data);
        Auth::login($userModel->find($id));
        (new Cart())->mergeSessionToUser(session_id(), $id);
        Session::flash('success', 'Đăng ký thành công. Chào mừng bạn!');
        $this->redirect('/');
    }

    public function logout(): void
    {
        Csrf::verify();
        Auth::logout();
        Session::flash('info', 'Bạn đã đăng xuất.');
        $this->redirect('/');
    }
}
