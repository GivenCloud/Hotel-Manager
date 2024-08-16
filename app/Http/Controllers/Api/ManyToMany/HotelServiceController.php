<?php

namespace App\Http\Controllers\Api\ManyToMany;

use App\Http\Controllers\Controller;
use App\Http\Requests\ManyToMany\HotelService\StoreHotelRequest;
use App\Http\Requests\ManyToMany\HotelService\StoreServiceRequest;
use App\Models\Hotel;
use App\Models\ManyToMany\HotelService;
use App\Models\Service;

/**
 * @OA\Tag(
 *   name="HotelService",
 *   description="API Endpoints of HotelService Controller"
 * )
 */

class HotelServiceController extends Controller
{
    /**
     * @OA\Get(
     *    path="/api/hotel-service",
     *    summary="Get all hotel-service",
     *    tags={"HotelService"},
     *    description="List of hotel-service",
     *    @OA\Response(
     *        response=200,
     *        description="Successful operation",
     *    )
     * )
     */
    public function index()
    {
        return response()->json(HotelService::get(), 200);
    }

    /**
     * @OA\Post(
     *     path="/api/hotel-services/add-hotels",
     *     tags={"HotelService"},
     *     summary="Add hotels to a service",
     *     description="Add hotels to a service", 
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="service_id", type="integer", example=1),
     *             @OA\Property(property="hotel_ids", type="array",
     *                 @OA\Items(type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     )
     * )
     */
    public function storeHotels(StoreHotelRequest $request)
    {
        $service = Service::findOrFail($request->service_id);
        $service->hotels()->sync($request->hotel_ids);
        return response()->json($service->hotels()->get(), 200);
    }

    /**
     * @OA\Post(
     *     path="/api/hotel-services/add-services",
     *     tags={"HotelService"},
     *     summary="Add services to a hotel",
     *     description="Add services to a hotel",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="hotel_id", type="integer", example=1),
     *             @OA\Property(property="service_ids", type="array",
     *                 @OA\Items(type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     )
     * )
     */
    public function storeServices(StoreServiceRequest $request)
    {
        $hotel = Hotel::findOrFail($request->hotel_id);
        $hotel->services()->sync($request->service_ids);
        return response()->json($hotel->services()->get(), 200);
    }

/**
 * Display the specified hotel-service.
 *
 * @OA\Get(
 *    path="/api/hotel-service/{id}",
 *    tags={"HotelService"},
 *    summary="Get a specific hotel-service",
 *    description="Retrieve a specific hotelservice by ID",
 *    @OA\Parameter(
 *        name="id",
 *        in="path",
 *        required=true,
 *        description="ID of the hotel-service",
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
 *        description="Hotel-Service not found"
 *    )
 * )
 */
    public function show($id)
    {
        $hotelservice = HotelService::findOrFail($id);
        return response()->json($hotelservice, 200);
    }

    /**
     * @OA\Put(
     *     path="/api/hotel-services/update-hotels",
     *     tags={"HotelService"},
     *     summary="Update hotels for a service",
     *     description="Update hotels for a service",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="service_id", type="integer", example=1),
     *             @OA\Property(property="hotel_ids", type="array",
     *                 @OA\Items(type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     )
     * )
     */
    public function updateHotels(StoreHotelRequest $request)
    {
        $service = Service::findOrFail($request->service_id);
        $service->hotels()->detach();
        $service->hotels()->sync($request->hotel_ids);
        return response()->json($service->hotels()->get(), 200);
    }

    /**
     * @OA\Put(
     *     path="/api/hotel-services/update-services",
     *     tags={"HotelService"},
     *     summary="Update services for a hotel",
     *     description="Update services for a hotel",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="hotel_id", type="integer", example=1),
     *             @OA\Property(property="service_ids", type="array",
     *                 @OA\Items(type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     )
     * )
     */
    public function updateServices(StoreServiceRequest $request)
    {
        $hotel = Hotel::findOrFail($request->hotel_id);
        $hotel->services()->detach();
        $hotel->services()->sync($request->service_ids);
        return response()->json($hotel->services()->get(), 200);
    }

    /**
     * @OA\Delete(
     *    path="/api/hotel-service/{id}",
     *    summary="Delete a specific hotel-service",
     *    tags={"HotelService"},
     *    description="Delete a specific hotel-service by ID",
     *    @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *        description="ID of the hotel-service",
     *        @OA\Schema(
     *            type="integer",
     *            format="int64"
     *        )
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="Hotel-Service deleted successfully",
     *    ),
     *    @OA\Response(
     *        response=404,
     *        description="Hotel-Service not found"
     *    )
     * )
     */
    public function destroy($id)
    {
        $hotelservice = HotelService::findOrFail($id);
        $hotelservice->delete();
        return response()->json(null, 200);
    }
}
