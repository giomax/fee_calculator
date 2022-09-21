<?php

namespace App\Http\Controllers;
use App\Http\Requests\StoreFile;

use App\Services\FeeService;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    	
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {	
        return view('home');
    }

    public function uploadFile(StoreFile $request){        
        $file = fopen($request->file, "r");
        $data = (new FeeService())->calculate($file);
        fclose($file);
        return response()->json($data);
    }
}
