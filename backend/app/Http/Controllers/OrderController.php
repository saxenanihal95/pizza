<?php

namespace App\Http\Controllers;

use App\Address;
use App\Order;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        $order = Order::with("products")->where('user_id', $user->user_id)->orderBy('created_at', 'desc')->get();
        return response()->json(["success" => true, "orders" => $order]);
    }

    private function saveAddress($input, $order)
    {
        $address =  new Address();
        $address->address = $input["address"];
        $address->district = $input["district"];
        $address->number = $input["number"];
        $address->complement = isset($input["complement"]) ? $input["complement"] : "";
        $address->order_id = $order->order_id;
        $address->save();
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'order' => 'required',
            'address' => 'required',
            'district' => 'required',
            'number' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $user = auth()->user();
        $input = $request->all();
        $order = new Order();
        $order->total = $input["order"]["total"];
        $order->user_id = $user->user_id;
        $order->costs = $input["costs"];
        $order->save();

        foreach ($input["order"]["products"] as $product) {
            if ($product != null) {
                $cproduct = Product::find($product["product_id"]);
                $total = $product["amount"] * $product["price"];
                $order->products()->save($cproduct, ["amount" => $product["amount"], "price" => $product["price"], "total" => $total]);
            }
        }
        $this->saveAddress($input, $order);
        return response()->json(["success" => true]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }
}
