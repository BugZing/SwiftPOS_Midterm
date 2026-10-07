<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

class Auth extends BaseController
{
    public function login(): string|RedirectResponse
    {
        $id = session()->get('auth_user_id');
        if (is_numeric($id) && (new UserModel())->find((int) $id) !== null) {
            return redirect()->to(site_url('products'));
        }

        $data = ['title' => 'Staff Login | Complete POS'];

        return view('templates/header', $data)
             . view('auth/login', $data)
             . view('templates/footer');
    }

    public function authenticate(): RedirectResponse
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $username = is_string($username) ? trim($username) : '';
        $password = is_string($password) ? $password : '';

        $user = $username === '' ? null : (new UserModel())->where('username', $username)->first();

        if ($user === null || ! is_string($user['password'] ?? null)
            || ! password_verify($password, $user['password'])) {
            return redirect()->to(site_url('login'))->with('error', 'Invalid username or password.');
        }

        session()->regenerate(true);
        session()->set([
            'auth_user_id'   => (int) $user['id'],
            'auth_username'  => (string) $user['username'],
            'auth_full_name' => (string) $user['full_name'],
            'auth_avatar'    => (string) ($user['avatar'] ?? ''),
        ]);

        return redirect()->to(site_url('products'))->with('success', 'Welcome back, ' . esc($user['full_name']) . '!');
    }

    public function logout(): RedirectResponse
    {
        session()->destroy();

        return redirect()->to(site_url('login'))->with('success', 'You have been logged out.');
    }
}
