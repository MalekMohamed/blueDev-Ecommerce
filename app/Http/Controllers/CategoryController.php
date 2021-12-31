<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['isAdmin','isVerified'])->only(['store', 'update', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $categories = Category::with(['products','products.category','products.brand'])->get();
        return response()->json([
            'status' => (bool)$categories,
            'data' => $categories,
            'message' => $categories ? '' : 'Error getting Categories'
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
            'name' => 'required|unique:categories',
        ]);
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validation->messages()->toArray()
            ], 500);
        }
        $data = $request->all();
        $category = Category::create($data);

        return response()->json([
            'status' => (bool)$category,
            'data' => $category,
            'message' => $category ? 'Category Created!' : 'Error Creating Category'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $category = Category::with(['products','products.category','products.brand'])->findorfail($id);
        return response()->json([
            'status' => (bool)$category,
            'data' => $category,
            'message' => $category ? '' : 'Error getting category'
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
        $category = Category::findorfail($id);
        $validation = Validator::make($request->all(), [
            'name' => 'required|unique:categories,name,' . $id,
        ]);
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validation->messages()->toArray()
            ], 500);
        }
        $data = $request->all();
        $category->update($data);
        $category->save();
        return response()->json([
            'status' => (bool)$category,
            'data' => $category,
            'message' => $category ? 'Category Updated!' : 'Error Updating Category'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $category = Category::findorfail($id);
        $category->delete();
        return response()->json([
            'status' => (bool)$category,
            'data' => $category,
            'message' => $category ? 'Category Deleted' : 'Error deleting Category'
        ]);
    }
}
