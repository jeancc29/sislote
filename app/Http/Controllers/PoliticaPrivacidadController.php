<?php

namespace App\Http\Controllers;

use Request;
use Illuminate\Support\Facades\Route; 
use Illuminate\Support\Facades\Response; 

class PoliticaPrivacidadController extends Controller
{
    public function index()
    {
        $controlador = Route::getCurrentRoute()->getName();
        return view('privacidad.index', compact('controlador'));
    }
}
