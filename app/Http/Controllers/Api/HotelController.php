<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Hotel\StoreRequest;
use App\Http\Requests\ManyToMany\HotelService\StoreServiceRequest;
use App\Models\Hotel;

/**
 * @OA\Tag(
 *   name="Hotel",
 *   description="API Endpoints of Hotel Controller"
 * )
 */

class HotelController extends Controller
{
    /**
     * @OA\Get(
     *    path="/api/hotel",
     *    summary="Get all hotels",
     *    tags={"Hotel"},
     *    description="List of hotels",
     *    @OA\Response(
     *        response=200,
     *        description="Successful operation",
     *    )
     * )
     */
    public function index()
    {
        return response()->json(Hotel::get(), 200);
    }

    /**
     * Create a new hotel.
     *
     * @OA\Post(
     *    path="/api/hotel",
     *    summary="Create a new hotel",
     *    tags={"Hotel"},
     *    description="Create a new hotel with the provided details",
     *    @OA\RequestBody(
     *       required=true,
     *       @OA\JsonContent(
     *           required={"name", "address", "stars", "phone", "email", "website", "image"},
     *           @OA\Property(property="name", type="string", example="Name"),
     *           @OA\Property(property="address", type="string", example="Address"),
     *           @OA\Property(property="stars", type="integer", example="5"),
     *           @OA\Property(property="phone", type="number", example="987654321"),
     *           @OA\Property(property="email", type="string", format="email", example="hotel@example.com"),
     *           @OA\Property(property="website", type="string", format="url", example="hotel.com"),
     *           @OA\Property(property="image", type="string", format="url", example="https://example.com/image.jpg")
     *       )
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="Hotel created successfully",
     *    ),
     *    @OA\Response(
     *        response=422,
     *        description="Validation error"
     *    )
     * )
     */
    public function store(StoreRequest $request)
    {
        return response()->json(Hotel::create($request->validated()), 200);
    }

/**
 * Display the specified hotel.
 *
 * @OA\Get(
 *    path="/api/hotel/{id}",
 *    summary="Get a specific hotel",
 *    tags={"Hotel"},
 *    description="Retrieve a specific hotel by ID",
 *    @OA\Parameter(
 *        name="id",
 *        in="path",
 *        required=true,
 *        description="ID of the hotel",
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
 *        description="Hotel not found"
 *    )
 * )
 */
    public function show($id)
    {
        $hotel = Hotel::findOrFail($id);
        return response()->json($hotel, 200);
    }

/**
 * Update an existing hotel.
 *
 * @OA\Put(
 *    path="/api/hotel/{id}",
 *    summary="Update an existing hotel",
 *    tags={"Hotel"},
 *    description="Update an existing hotel by ID",
 *    @OA\Parameter(
 *        name="id",
 *        in="path",
 *        required=true,
 *        description="ID of the hotel",
 *        @OA\Schema(
 *            type="integer",
 *            format="int64"
 *        )
 *    ),
 *    @OA\RequestBody(
 *       required=true,
 *       @OA\JsonContent(
 *           required={"name", "address", "stars", "phone", "email", "website", "image"},
 *           @OA\Property(property="name", type="string", example="Updated Name"),
 *           @OA\Property(property="address", type="string", example="Updated Address"),
 *           @OA\Property(property="stars", type="integer", example="4"),
 *           @OA\Property(property="phone", type="number", example="987654111"),
 *           @OA\Property(property="email", type="string", format="email", example="updated.email@gmail.com"),
 *           @OA\Property(property="website", type="string", format="url", example="updated-hotel.com"),
 *           @OA\Property(property="image", type="string", format="url", example="https://example.com/updated-image.jpg")
 *       )
 *    ),
 *    @OA\Response(
 *        response=200,
 *        description="Hotel updated successfully",
 *    ),
 *    @OA\Response(
 *        response=404,
 *        description="Hotel not found"
 *    )
 * )
 */
    public function update(StoreRequest $request, $id)
    {
        $hotel = Hotel::findOrFail($id);
        $hotel->update($request->validated());
        return response()->json($hotel, 200);
    }

/**
     * @OA\Delete(
     *    path="/api/hotel/{id}",
     *    summary="Delete a specific hotel",
     *    tags={"Hotel"},
     *    description="Delete a specific hotel by ID",
     *    @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *        description="ID of the hotel",
     *        @OA\Schema(
     *            type="integer",
     *            format="int64"
     *        )
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="Hotel deleted successfully",
     *    ),
     *    @OA\Response(
     *        response=404,
     *        description="Hotel not found"
     *    )
     * )
     */
    public function destroy($id)
    {
        $hotel = Hotel::findOrFail($id);
        $hotel->delete();
        return response()->json(null, 200);
    }

    /**
     * @OA\Get(
     *    path="/api/hotel/search/{name}",
     *    summary="Search hotels by name",
     *    tags={"Hotel"},
     *    description="Search hotels by name",
     *    @OA\Parameter(
     *        name="name",
     *        in="path",
     *        required=true,
     *        description="Name of the hotel",
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
        return response()->json(Hotel::where('name', 'like', "%$name%")->get(), 200);
    }

    /**
     * @OA\Get(
     *    path="/api/hotel/{id}/rooms",
     *    summary="Get rooms of a specific hotel",
     *    tags={"Hotel"},
     *    description="Get rooms of a specific hotel by ID",
     *    @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *        description="ID of the hotel",
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
     *        description="Hotel not found"
     *    )
     * )
     */
    public function getRooms($id)
    {
        $hotel = Hotel::findOrFail($id);
        return response()->json($hotel->rooms, 200);
    }

    /**
     * @OA\Get(
     *    path="/api/hotel/{id}/services",
     *    summary="Get services of a specific hotel",
     *    tags={"Hotel"},
     *    description="Get services of a specific hotel by ID",
     *    @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *        description="ID of the hotel",
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
     *        description="Hotel not found"
     *    )
     * )
     */
    public function getServices($id)
    {
        $hotel = Hotel::findOrFail($id);
        return response()->json($hotel->services, 200);
    }

    /**
     * @OA\Post(
     *    path="/api/hotel/{id}/services",
     *    summary="Add services to a specific hotel",
     *    tags={"Hotel"},
     *    description="Add services to a specific hotel by ID",
     *    @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *        description="ID of the hotel",
     *        @OA\Schema(
     *            type="integer",
     *            format="int64"
     *        )
     *    ),
     *    @OA\RequestBody(
     *       required=true,
     *       @OA\JsonContent(
     *           required={"hotel_id", "service_id"},
    *            @OA\Property(property="hotel_id", type="integer", example="1"),
     *           @OA\Property(property="service_id", type="array", @OA\Items(type="integer", example=1))
     *       )
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="Services added successfully",
     *    ),
     *    @OA\Response(
     *        response=404,
     *        description="Hotel not found"
     *    )
     * )
     */
    public function addServices(Hotel $hotel, StoreServiceRequest $request)
    {
        $serviceIds = $request->validated()['service_id'] ?? [];
        $hotel->services()->syncWithoutDetaching($serviceIds);
        return response()->json($hotel->services, 200);
    }

    /**
     * @OA\Delete(
     *    path="/api/hotel/{id}/services",
     *    summary="Remove services from a specific hotel",
     *    tags={"Hotel"},
     *    description="Remove services from a specific hotel by ID",
     *    @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *        description="ID of the hotel",
     *        @OA\Schema(
     *            type="integer",
     *            format="int64"
     *        )
     *    ),
     *    @OA\RequestBody(
     *       required=true,
     *       @OA\JsonContent(
     *           required={"hotel_id", "service_id"},
    *            @OA\Property(property="hotel_id", type="integer", example="1"),
     *           @OA\Property(property="service_id", type="array", @OA\Items(type="integer", example=1))
     *       )
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="Services removed successfully",
     *    ),
     *    @OA\Response(
     *        response=404,
     *        description="Hotel not found"
     *    )
     * )
     */
    public function removeServices(Hotel $hotel, StoreServiceRequest $request)
    {
        $serviceIds = $request->validated()['service_id'] ?? [];
        $hotel->services()->detach($serviceIds);
        return response()->json($hotel->services, 200);
    }
}
