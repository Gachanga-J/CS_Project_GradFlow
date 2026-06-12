<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class RegisterChoiceController extends Controller
{
    /**
     * Display the role selection screen.
     */
    public function __invoke(): View
    {
        return view('auth.register-choice');
    }
}