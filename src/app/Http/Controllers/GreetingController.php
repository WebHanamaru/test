<?php

namespace App\Http\Controllers;

use App\Models\Greeting;
use Illuminate\Http\Request;

class GreetingController extends Controller
{
    public function index()
    {
        $greetings = Greeting::all();
        return view('greeting', compact('greetings'));
    }

    public function store(Request $request)
    {
        Greeting::create($request->only('person', 'message'));
        return redirect()->route('greeting.index');
    }

    public function destroy(Greeting $greeting)
    {
        $greeting->delete();
        return redirect()->route('greeting.index');
    }
}
