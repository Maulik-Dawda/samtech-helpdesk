<?php

require_once "../app/Core/Controller.php";

class HomeController extends Controller
{
    public function index()
    {
        $this->view('home/index');
    }
}