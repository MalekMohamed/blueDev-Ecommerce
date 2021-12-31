<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['isAdmin','isVerified'])->only(['store','update','destroy']);
        $this->middleware('auth:api')->except(['show', 'index']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $products = Product::with(['category', 'brand', 'user'])->get();
        return response()->json([
            'status' => (bool)$products,
            'data' => $products,
            'message' => $products ? '' : 'Error getting Products'
        ]);
    }
    public function uploadFile(Request $request)
    {
        if($request->hasFile('image')){
            $validation = Validator::make($request->all(), [
                'image' => 'required|mimes:jpg,jpeg,png|max:2048',
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validation->messages()->toArray()
                ], 500);
            }
            $name = time()."_".$request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('images'), $name);
        }
        return response()->json(asset("images/$name"),201);
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
            'name' => 'required|unique:products',
            'price' => 'required|integer',
            'qty' => 'integer',
            'brand_id' => 'required|integer',
            'category_id' => 'required|integer',
            'description' => 'max:150',
            'image' => 'required|string',
        ]);
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validation->messages()->toArray()
            ], 500);
        }

        $data = $request->all();
        $data['user_id'] = auth('api')->user()->id;
        $product = Product::create($data);

        return response()->json([
            'status' => (bool)$product,
            'data' => $product,
            'message' => $product ? 'Product Created!' : 'Error Creating Product'
        ]);
    }

    public function show($id)
    {
        $product = Product::with(['category', 'brand', 'user'])->findorfail($id);
        return response()->json([
            'status' => (bool)$product,
            'data' => $product,
            'message' => $product ? '' : 'Error getting Product'
        ]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $product = Product::findorfail($id);
        $validator = Validator::make($request->all(), [
            'name' => 'string|unique:products,name,' . $id,
            'price' => 'integer',
            'qty' => 'integer',
            'brand_id' => 'integer',
            'category_id' => 'integer',
            'description' => 'max:150',
            'image' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => (bool)$product,
                'message' => $validator->messages()->toArray()
            ], 500);
        }

        $product->update($request->all());
        $product->save();
        return response()->json($product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param integer $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $product = Product::findorfail($id);
        $product->delete();
        return response()->json([
            'status' => (bool)$product,
            'data' => $product,
            'message' => $product ? 'Product Deleted' : 'Error deleting Product'
        ]);
    }
}
