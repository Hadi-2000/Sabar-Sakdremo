<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ViewController extends Controller {
    protected $errorMessage;

    public function __construct() {
        $this->errorMessage = 'Anda harus login terlebih dahulu';
    }

    public function ViewLogin() {
        return view('login');
    }

    private function checkSession()
    {
        if (!session()->has('key')) {
            return redirect()->route('/login')->with('error', $this->errorMessage);
        }
        return null;
    }
}