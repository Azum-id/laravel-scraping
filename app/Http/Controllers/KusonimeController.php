<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// get data from Kusonime Model
use App\Models\Kusonime;

class KusonimeController extends Controller
{
    public function index(Request $request)
    {
        return Kusonime::getAnime($request);
    }
}
