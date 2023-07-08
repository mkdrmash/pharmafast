<?php

namespace App\Http\Controllers;

use App\Events\OrderAccepted;
use App\Events\OrderCreated;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required',
            'description' => 'required',
        ]);

        $fileName = time().'.'.$request->file->getClientOriginalExtension();
        $request->file->move(public_path('uploads'), $fileName);

        $order = $request->user()->orders()->create([
            'description' => $request->input('description'),
            'prescription_file' => $fileName,
        ]);

        $order = $order->load('user');

        OrderCreated::dispatch($order, $request->user());

        return $order;
    }

    public function accept(Request $request, Order $order)
    {
        // a pharmacy accepts a order
        $order->update([
            'pharmacy_id' => $request->user()->pharmacy->id,
            'is_accepted' => true,
        ]);

        $order->load('pharmacy.user');

        OrderAccepted::dispatch($order, $order->user);

        return $order;
    }

    public function complete(Request $request, Order $order)
    {
        // a user has completed the order
        $order->update([
            'is_complete' => true
        ]);

        $order->load('pharmacy.user');

        return $order;
    }
}
