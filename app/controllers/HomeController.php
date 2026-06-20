<?php

namespace App\controllers;

use App\View;

class HomeController extends Controller
{
    public function index()
    {
       View::view('index');
    }

    public function test()
    {
        View::view('test');
    }

}
