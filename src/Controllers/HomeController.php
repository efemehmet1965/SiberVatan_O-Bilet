<?php

require_once 'BaseController.php';
require_once __DIR__ . '/../Lib/cities.php';

class HomeController extends BaseController {

    public function index() {
        $cities = getTurkishCities();
        sort($cities);
        $this->view('home', ['cities' => $cities]);
    }
}
?>