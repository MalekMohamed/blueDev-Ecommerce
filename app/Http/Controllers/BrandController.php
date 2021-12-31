<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    public function __construct()
    {
        $this->middleware(['isAdmin','isVerified'])->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $brands = Brand::with(['products','products.category','products.brand'])->get();
        return response()->json([
            'status' => (bool)$brands,
            'data' => $brands,
            'message' => $brands ? '' : 'Error getting Brands'
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
            'name' => 'required|unique:brands',
        ]);
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validation->messages()->toArray()
            ], 500);
        }
        $data = $request->all();
        $brand = Brand::create($data);

        return response()->json([
            'status' => (bool)$brand,
            'data' => $brand,
            'message' => $brand ? 'Brand Created!' : 'Error Creating Brand'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $brand = Brand::with(['products','products.category','products.brand'])->findorfail($id);
        return response()->json([
            'status' => (bool)$brand,
            'data' => $brand,
            'message' => $brand ? '' : 'Error getting Brand'
        ]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $brand = Brand::findorfail($id);
        $validation = Validator::make($request->all(), [
            'name' => 'required|unique:brands,name,' . $id,
        ]);
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validation->messages()->toArray()
            ], 500);
        }
        $data = $request->all();
        $brand->update($data);
        $brand->save();
        return response()->json([
            'status' => (bool)$brand,
            'data' => $brand,
            'message' => $brand ? 'Brand Updated!' : 'Error Updating Brand'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $brand = Brand::findorfail($id);
        $brand->delete();
        return response()->json([
            'status' => (bool)$brand,
            'data' => $brand,
            'message' => $brand ? 'Brand Deleted' : 'Error deleting Brand'
        ]);
    }
}
