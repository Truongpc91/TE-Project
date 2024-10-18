<?php

namespace App\Http\Controllers;

use App\Models\Language;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    protected $language;


    public function __construct()
    {
       $this->setLanguage();
    }

    public function setLanguage() {
        $locale = app()->getLocale(); // vn en cn
        $language = Language::where('canonical', $locale)->first(); 
        $this->language = $language->id;
    }
}
