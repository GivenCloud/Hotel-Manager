<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreRequest;
use App\Models\Category;

/**
 * @OA\Tag(
 *   name="Category",
 *   description="API Endpoints of Category Controller"
 * )
 */

class CategoryController extends Controller
{
    /**
     * @OA\Get(
     *    path="/api/category",
     *    summary="Get all categories",
     *    tags={"Category"},
     *    description="List of categories",
     *    @OA\Response(
     *        response=200,
     *        description="Successful operation",
     *    )
     * )
     */
    public function index()
    {
        return response()->json(Category::get(), 200);
    }

/**
 * @OA\Post(
 *    path="/api/category",
 *    summary="Create a new category",
 *    tags={"Category"},
 *    description="Create a new category with the provided details",
 *    @OA\RequestBody(
 *       required=true,
 *       @OA\JsonContent(
 *           required={"name"},
 *           @OA\Property(property="name", type="string", example="Category Name"),
 *       )
 *    ),
 *    @OA\Response(
 *        response=200,
 *        description="Category created successfully",
 *    ),
 *    @OA\Response(
 *        response=422,
 *        description="Validation error"
 *    )
 * )
*/
    public function store(StoreRequest $request)
    {
        return response()->json(Category::create($request->validated()), 200);
    }

/**
 * Display the specified category.
 *
 * @OA\Get(
 *    path="/api/category/{id}",
 *    summary="Get a specific category",
 *    tags={"Category"},
 *    description="Retrieve a specific category by ID",
 *    @OA\Parameter(
 *        name="id",
 *        in="path",
 *        required=true,
 *        description="ID of the category",
 *        @OA\Schema(
 *            type="integer",
 *            format="int64"
 *        )
 *    ),
 *    @OA\Response(
 *        response=200,
 *        description="Successful operation",
 *    ),
 *    @OA\Response(
 *        response=404,
 *        description="Category not found"
 *    )
 * )
 */
    public function show($id)
    {
        $category = Category::findOrFail($id);
        return response()->json($category, 200);
    }

/**
 * Update an existing category.
 *
 * @OA\Put(
 *    path="/api/category/{id}",
 *    summary="Update an existing category",
 *    tags={"Category"},
 *    description="Update an existing category by ID",
 *    @OA\Parameter(
 *        name="id",
 *        in="path",
 *        required=true,
 *        description="ID of the category",
 *        @OA\Schema(
 *            type="integer",
 *            format="int64"
 *        )
 *    ),
 *    @OA\RequestBody(
 *       required=true,
 *       @OA\JsonContent(
 *           required={"name"},
 *           @OA\Property(property="name", type="string", example="Updated Category Name"),
 *       )
 *    ),
 *    @OA\Response(
 *        response=200,
 *        description="Category updated successfully",
 *    ),
 *    @OA\Response(
 *        response=404,
 *        description="Category not found"
 *    )
 * )
 */
    public function update(StoreRequest $request, $id)
    {
        $category = Category::findOrFail($id);
        $category->update($request->validated());
        return response()->json($category, 200);
    }

/**
     * @OA\Delete(
     *    path="/api/category/{id}",
     *    summary="Delete a specific category",
     *    tags={"Category"},
     *    description="Delete a specific category by ID",
     *    @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *        description="ID of the category",
     *        @OA\Schema(
     *            type="integer",
     *            format="int64"
     *        )
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="Category deleted successfully",
     *    ),
     *    @OA\Response(
     *        response=404,
     *        description="Category not found"
     *    )
     * )
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return response()->json(null, 200);
    }

    /**
     * @OA\Get(
     *    path="/api/category/search/{name}",
     *    summary="Search categories by name",
     *    tags={"Category"},
     *    description="Search categories by name",
     *    @OA\Parameter(
     *        name="name",
     *        in="path",
     *        required=true,
     *        description="Name of the category",
     *        @OA\Schema(
     *            type="string"
     *        )
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="Successful operation",
     *    )
     * )
     */
    public function search($name)
    {
        return response()->json(Category::where('name', 'like', "%$name%")->get(), 200);
    }

    /**
     * @OA\Get(
     *    path="/api/category/{id}/services",
     *    summary="Get services of a specific category",
     *    tags={"Category"},
     *    description="Get services of a specific category by ID",
     *    @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *        description="ID of the category",
     *        @OA\Schema(
     *            type="integer",
     *            format="int64"
     *        )
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="Successful operation",
     *    ),
     *    @OA\Response(
     *        response=404,
     *        description="Category not found"
     *    )
     * )
     */
    public function getServices($id)
    {
        $category = Category::findOrFail($id);
        return response()->json($category->services, 200);
    }
}
