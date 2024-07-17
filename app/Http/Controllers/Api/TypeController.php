<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Type\StoreRequest;
use App\Models\Type;

/**
 * @OA\Tag(
 *   name="Type",
 *   description="API Endpoints of Type Controller"
 * )
 */

class TypeController extends Controller
{
    /**
     * @OA\Get(
     *    path="/api/type",
     *    summary="Get all types",
     *    tags={"Type"},
     *    description="List of types",
     *    @OA\Response(
     *        response=200,
     *        description="Successful operation",
     *    )
     * )
     */
    public function index()
    {
        return response()->json(Type::get(), 200);
    }

    /**
 * @OA\Post(
 *    path="/api/type",
 *    summary="Create a new type",
 *    tags={"Type"},
 *    description="Create a new type with the provided details",
 *    @OA\RequestBody(
 *       required=true,
 *       @OA\JsonContent(
 *           required={"name, price, capacity"},
 *           @OA\Property(property="name", type="string", example="Type Name"),
 *           @OA\Property(property="price", type="number", example="100.00"),
 *           @OA\Property(property="capacity", type="integer", example="2"),
 *       )
 *    ),
 *    @OA\Response(
 *        response=200,
 *        description="Type created successfully",
 *    ),
 *    @OA\Response(
 *        response=422,
 *        description="Validation error"
 *    )
 * )
*/
    public function store(StoreRequest $request)
    {
        return response()->json(Type::create($request->validated()), 200);
    }

    /**
 * Display the specified type.
 *
 * @OA\Get(
 *    path="/api/type/{id}",
 *    summary="Get a specific type",
 *    tags={"Type"},
 *    description="Retrieve a specific type by ID",
 *    @OA\Parameter(
 *        name="id",
 *        in="path",
 *        required=true,
 *        description="ID of the type",
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
 *        description="Type not found"
 *    )
 * )
 */
    public function show($id)
    {
        $type = Type::findOrFail($id);
        return response()->json($type, 200);
    }

    /**
 * Update an existing type.
 *
 * @OA\Put(
 *    path="/api/type/{id}",
 *    summary="Update an existing type",
 *    tags={"Type"},
 *    description="Update an existing type by ID",
 *    @OA\Parameter(
 *        name="id",
 *        in="path",
 *        required=true,
 *        description="ID of the type",
 *        @OA\Schema(
 *            type="integer",
 *            format="int64"
 *        )
 *    ),
 *    @OA\RequestBody(
 *       required=true,
 *       @OA\JsonContent(
 *           required={"name, price, capacity"},
 *           @OA\Property(property="name", type="string", example="Type Name"),
 *           @OA\Property(property="price", type="number", example="100.00"),
 *           @OA\Property(property="capacity", type="integer", example="2"),
 *       )
 *    ),
 *    @OA\Response(
 *        response=200,
 *        description="Type updated successfully",
 *    ),
 *    @OA\Response(
 *        response=404,
 *        description="Type not found"
 *    )
 * )
 */
    public function update(StoreRequest $request, $id)
    {
        $type = Type::findOrFail($id);
        $type->update($request->validated());
        return response()->json($type, 200);
    }

    /**
     * @OA\Delete(
     *    path="/api/type/{id}",
     *    summary="Delete a specific type",
     *    tags={"Type"},
     *    description="Delete a specific type by ID",
     *    @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *        description="ID of the type",
     *        @OA\Schema(
     *            type="integer",
     *            format="int64"
     *        )
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="Type deleted successfully",
     *    ),
     *    @OA\Response(
     *        response=404,
     *        description="Type not found"
     *    )
     * )
     */
    public function destroy($id)
    {
        $type = Type::findOrFail($id);
        $type->delete();
        return response()->json(null, 200);
    }

    /**
     * @OA\Get(
     *    path="/api/type/search/{name}",
     *    summary="Search types by name",
     *    tags={"Type"},
     *    description="Search types by name",
     *    @OA\Parameter(
     *        name="name",
     *        in="path",
     *        required=true,
     *        description="Name of the type",
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
        return response()->json(Type::where('name', 'like', "%$name%")->get(), 200);
    }

    /**
     * @OA\Get(
     *    path="/api/type/{id}/rooms",
     *    summary="Get rooms of a specific type",
     *    tags={"Type"},
     *    description="Get rooms of a specific type by ID",
     *    @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *        description="ID of the type",
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
     *        description="Type not found"
     *    )
     * )
     */
    public function getRooms($id)
    {
        $type = Type::findOrFail($id);
        return response()->json($type->rooms, 200);
    }
}
