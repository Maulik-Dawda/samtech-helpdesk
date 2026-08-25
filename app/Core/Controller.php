<?php

class Controller
{
    protected function view($view, $data = [])
    {
        extract($data);

        require ROOT_PATH . "/app/Views/" . $view . ".php";
    }
}