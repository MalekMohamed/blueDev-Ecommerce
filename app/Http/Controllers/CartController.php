<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api','isVerified']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $cart = Cart::where('user_id', auth('api')->user()->id)
            ->with(['product','user'])
            ->get();
        return response()->json([
            'status' => (bool)$cart,
            'data' => $cart,
            'message' => $cart ? '' : 'Error getting cart'
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {

        $validation = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer',
        ]);
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validation->messages()->toArray()
            ], 500);
        }
        // Product already in cart
        $cart = Cart::with(['product', 'user'])
            ->where('user_id', auth('api')->user()->id)
            ->where('product_id', $request->product_id)
            ->first();
        if ($cart) {
            $cart->qty += $request->qty;
            $cart->save();
        } else {
            $cart = new Cart();
            $cart->fill($request->except('user_id'));
            $cart->user_id = auth('api')->user()->id;
            $cart->save();
        }
        return response()->json([
            'status' => (bool)$cart,
            'data' => $cart,
            'message' => $cart ? 'Item Added to cart!' : 'Error Creating cart'
        ]);

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $cart = Cart::where('user_id', auth('api')->user()->id)
            ->where('product_id', $id)
            ->first();
        $cart->delete();
        return response()->json([
            'status' => (bool)$cart,
            'data' => $cart,
            'message' => $cart ? 'Item removed from cart!' : 'Error updating cart'
        ]);

    }
}
