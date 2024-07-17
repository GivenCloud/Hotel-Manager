<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ManyToMany\HotelService\StoreHotelRequest;
use App\Http\Requests\ManyToMany\GuestService\StoreGuestRequest;
use App\Http\Requests\Service\StoreRequest;
use App\Models\Service;

/**
 * @OA\Tag(
 *   name="Service",
 *   description="API Endpoints of Service Controller"
 * )
 */

class ServiceController extends Controller
{
    /**
     * @OA\Get(
     *    path="/api/service",
     *    summary="Get all services",
     *    tags={"Service"},
     *    description="List of services",
     *    @OA\Response(
     *        response=200,
     *        description="Successful operation",
     *    )
     * )
     */
    public function index()
    {
        return response()->json(Service::get(), 200);
    }

    /**
 * @OA\Post(
 *    path="/api/service",
 *    summary="Create a new service",
 *    tags={"Service"},
 *    description="Create a new service with the provided details",
 *    @OA\RequestBody(
 *       required=true,
 *       @OA\JsonContent(
 *           required={"name", "description", "category_id"},
 *           @OA\Property(property="name", type="integer", example="Service"),
 *           @OA\Property(property="description", type="integer", example="Description"),
 *           @OA\Property(property="category_id", type="integer", example="1"),
 *       )
 *    ),
 *    @OA\Response(
 *        response=200,
 *        description="Service created successfully",
 *    ),
 *    @OA\Response(
 *        response=422,
 *        description="Validation error"
 *    )
 * )
*/
    public function store(StoreRequest $request)
    {
        return response()->json(Service::create($request->validated()), 200);
    }

    /**
 * Display the specified service.
 *
 * @OA\Get(
 *    path="/api/service/{id}",
 *    summary="Get a specific service",
 *    tags={"Service"},
 *    description="Retrieve a specific service by ID",
 *    @OA\Parameter(
 *        name="id",
 *        in="path",
 *        required=true,
 *        description="ID of the service",
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
 *        description="Service not found"
 *    )
 * )
 */
    public function show($id)
    {
        $service = Service::findOrFail($id);
        return response()->json($service, 200);
    }

    /**
 * Update an existing service.
 *
 * @OA\Put(
 *    path="/api/service/{id}",
 *    summary="Update an existing service",
 *    tags={"Service"},
 *    description="Update an existing service by ID",
 *    @OA\Parameter(
 *        name="id",
 *        in="path",
 *        required=true,
 *        description="ID of the service",
 *        @OA\Schema(
 *            type="integer",
 *            format="int64"
 *        )
 *    ),
 *    @OA\RequestBody(
 *       required=true,
 *       @OA\JsonContent(
 *           required={"name", "description", "category_id"},
 *           @OA\Property(property="name", type="integer", example="Updated Service"),
 *           @OA\Property(property="description", type="integer", example="Updated Description"),
 *           @OA\Property(property="category_id", type="integer", example="2"),
 *       )
 *    ),
 *    @OA\Response(
 *        response=200,
 *        description="Service updated successfully",
 *    ),
 *    @OA\Response(
 *        response=404,
 *        description="Service not found"
 *    )
 * )
 */
    public function update(StoreRequest $request, $id)
    {
        $service = Service::findOrFail($id);
        $service->update($request->validated());
        return response()->json($service, 200);
    }

    /**
     * @OA\Delete(
     *    path="/api/service/{id}",
     *    summary="Delete a specific service",
     *    tags={"Service"},
     *    description="Delete a specific service by ID",
     *    @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *        description="ID of the service",
     *        @OA\Schema(
     *            type="integer",
     *            format="int64"
     *        )
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="Service deleted successfully",
     *    ),
     *    @OA\Response(
     *        response=404,
     *        description="Service not found"
     *    )
     * )
     */
    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        $service->delete();
        return response()->json(null, 200);
    }

    /**
     * @OA\Get(
     *    path="/api/service/search/{name}",
     *    summary="Search services by name",
     *    tags={"Service"},
     *    description="Search services by name",
     *    @OA\Parameter(
     *        name="name",
     *        in="path",
     *        required=true,
     *        description="Name of the service",
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
        return response()->json(Service::where('name', 'like', "%$name%")->get(), 200);
    }

    /**
     * @OA\Get(
     *    path="/api/service/{id}/category",
     *    summary="Get categories of a specific service",
     *    tags={"Service"},
     *    description="Get categories of a specific service by ID",
     *    @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *        description="ID of the service",
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
     *        description="Service not found"
     *    )
     * )
     */
    public function getCategory($id)
    {
        $service = Service::findOrFail($id);
        return response()->json($service->category, 200);
    }

    /**
     * @OA\Get(
     *    path="/api/service/{id}/hotels",
     *    summary="Get hotels of a specific service",
     *    tags={"Service"},
     *    description="Get hotels of a specific service by ID",
     *    @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *        description="ID of the service",
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
     *        description="Service not found"
     *    )
     * )
     */
    public function getHotels($id)
    {
        $service = Service::findOrFail($id);
        return response()->json($service->hotels, 200);
    }

    /**
     * @OA\Post(
     *    path="/api/service/{service}/hotels",
     *    summary="Add hotels to a specific service",
     *    tags={"Service"},
     *    description="Add hotels to a specific service by ID",
     *    @OA\Parameter(
     *        name="service",
     *        in="path",
     *        required=true,
     *        description="ID of the service",
     *        @OA\Schema(
     *            type="integer",
     *            format="int64"
     *        )
     *    ),
     *    @OA\RequestBody(
     *       required=true,
     *       @OA\JsonContent(
     *           required={"service_id", "hotel_id"},
    *            @OA\Property(property="service_id", type="integer", example="1"),
     *           @OA\Property(property="hotel_id", type="array", @OA\Items(type="integer", example=1))
     *       )
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="Hotels added successfully",
     *    ),
     *    @OA\Response(
     *        response=404,
     *        description="service not found"
     *    )
     * )
     */
    public function addHotels(Service $service, StoreHotelRequest $request)
    {
        $hotelIds = $request->validated()['hotel_id'] ?? [];
        $service->hotels()->syncWithoutDetaching($hotelIds);
        return response()->json($service->hotels, 200);
    }

    /**
     * @OA\Delete(
     *    path="/api/service/{service}/hotels",
     *    summary="Remove hotels from a specific service",
     *    tags={"Service"},
     *    description="Remove hotels from a specific service by ID",
     *    @OA\Parameter(
     *        name="service",
     *        in="path",
     *        required=true,
     *        description="ID of the service",
     *        @OA\Schema(
     *            type="integer",
     *            format="int64"
     *        )
     *    ),
     *    @OA\RequestBody(
     *       required=true,
     *       @OA\JsonContent(
     *           required={"service_id" ,"hotel_id"},
     *           @OA\Property(property="service_id", type="integer", example="1"),
     *           @OA\Property(property="hotel_id", type="array", @OA\Items(type="integer", example=1))
     *       )
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="Hotels removed successfully",
     *    ),
     *    @OA\Response(
     *        response=404,
     *        description="service not found"
     *    )
     * )
     */
    public function removeHotels(Service $service, StoreHotelRequest $request)
    {
        $hotelIds = $request->validated()['hotel_id'] ?? [];
        $service->hotels()->detach($hotelIds);
        return response()->json($service->hotels, 200);
    }

    /**
     * @OA\Get(
     *    path="/api/service/{id}/guests",
     *    summary="Get guests of a specific service",
     *    tags={"Service"},
     *    description="Get guests of a specific service by ID",
     *    @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *        description="ID of the service",
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
     *        description="service not found"
     *    )
     * )
     */
    public function getGuests($id)
    {
        $service = Service::findOrFail($id);
        return response()->json($service->guests, 200);
    }

    /**
     * @OA\Post(
     *    path="/api/service/{service}/guests",
     *    summary="Add guests to a specific service",
     *    tags={"Service"},
     *    description="Add guests to a specific service by ID",
     *    @OA\Parameter(
     *        name="service",
     *        in="path",
     *        required=true,
     *        description="ID of the service",
     *        @OA\Schema(
     *            type="integer",
     *            format="int64"
     *        )
     *    ),
     *    @OA\RequestBody(
     *       required=true,
     *       @OA\JsonContent(
     *           required={"service_id", "guest_id"},
    *            @OA\Property(property="service_id", type="integer", example="1"),
     *           @OA\Property(property="guest_id", type="array", @OA\Items(type="integer", example=1))
     *       )
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="Guests added successfully",
     *    ),
     *    @OA\Response(
     *        response=404,
     *        description="service not found"
     *    )
     * )
     */
    public function addGuests(Service $service, StoreGuestRequest $request)
    {
        $guestIds = $request->validated()['guest_id'] ?? [];
        $service->guests()->attach($guestIds);
        return response()->json($service->guests, 200);
    }

    /**
     * @OA\Delete(
     *    path="/api/service/{service}/guests",
     *    summary="Remove guests from a specific service",
     *    tags={"Service"},
     *    description="Remove guests from a specific service by ID",
     *    @OA\Parameter(
     *        name="service",
     *        in="path",
     *        required=true,
     *        description="ID of the service",
     *        @OA\Schema(
     *            type="integer",
     *            format="int64"
     *        )
     *    ),
     *    @OA\RequestBody(
     *       required=true,
     *       @OA\JsonContent(
     *           required={"service_id" ,"guest_id"},
     *           @OA\Property(property="service_id", type="integer", example="1"),
     *           @OA\Property(property="guest_id", type="array", @OA\Items(type="integer", example=1))
     *       )
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="Guests removed successfully",
     *    ),
     *    @OA\Response(
     *        response=404,
     *        description="service not found"
     *    )
     * )
     */
    public function removeGuests(Service $service, StoreGuestRequest $request)
    {
        $guestIds = $request->validated()['guest_id'] ?? [];
        $service->guests()->detach($guestIds);
        return response()->json($service->guests, 200);
    }
}