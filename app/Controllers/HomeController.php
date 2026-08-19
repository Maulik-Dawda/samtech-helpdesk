<?php

require_once ROOT_PATH . "/app/Core/Controller.php";

class HomeController extends Controller
{
    public function index()
    {
        $this->view('home/index');
    }
}