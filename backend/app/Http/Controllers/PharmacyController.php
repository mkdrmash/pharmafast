<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PharmacyController extends Controller
{
    public function show(Request $request)
    {
        // return back the user and associated pharmacy model
        $user = $request->user();
        $user->load('pharmacy');

        return $user;
    }

    public function update(Request $request)
    {
        $request->validate([
            'file' => 'required',
            'pharmacyName' => 'required',
            'address' => 'required',
            'contactPerson' => 'required',
            'phoneNo' => 'required'
        ]);

        $user = $request->user();

        $user->update(['name' => $request->input('contactPerson')]);

        $fileName = time().'.'.$request->file->getClientOriginalExtension();
        $request->file->move(public_path('uploads'), $fileName);

        // create or update a pharmacy associated with this user
        $user->pharmacy()->updateOrCreate([
            'logo' => $fileName,
            'pharmacy_name' => $request->input('pharmacyName'),
            'address' => $request->input('address'),
            'contact_person' => $request->input('contactPerson'),
            'phone_no' => $request->input('phoneNo')
        ]);

        $user->load('pharmacy');

        return $user;
    }
}
