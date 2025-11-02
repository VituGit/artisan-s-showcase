<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LpContactController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => ['required','string','max:120'],
            'email'   => ['required','email','max:180'],
            'message' => ['required','string','max:2000'],
        ]);

        // TODO: enviar e-mail / gravar lead
        return back()->with(['lp_contact_ok' => true]);
    }
}
