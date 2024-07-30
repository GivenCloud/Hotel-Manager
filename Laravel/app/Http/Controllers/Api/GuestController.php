<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\ManyToMany\RoomGuestController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Guest\StoreRequest;
use App\Http\Requests\ManyToMany\GuestService\StoreServiceRequest;
use App\Http\Requests\ManyToMany\RoomGuest\StoreRoomRequest;
use App\Models\Guest;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *   name="Guest",
 *   description="API Endpoints of Guest Controller"
 * )
 */

class GuestController extends Controller
{
    /**
     * @OA\Get(
     *    path="/api/guest",
     *    summary="Get all guests",
     *    tags={"Guest"},
     *    description="List of guests",
     *    @OA\Response(
     *        response=200,
     *        description="Successful operation",
     *    )
     * )
     */
    public function index()
    {
        return response()->json(Guest::get(), 200);
    }

    /**
 * Create a new guest.
 *
 * @OA\Post(
 *    path="/api/guest",
 *    summary="Create a new guest",
 *    tags={"Guest"},
 *    description="Create a new guest with the provided details",
 *    @OA\RequestBody(
 *       required=true,
 *       @OA\JsonContent(
 *           required={"name", "lastName", "dniPassport", "phone", "email"},
 *           @OA\Property(property="name", type="string", example="Guest Name"),
 *           @OA\Property(property="lastName", type="string", example="Guest Last Name"),
 *           @OA\Property(property="dniPassport", type="string", example="25588978L"),
 *           @OA\Property(property="email", type="string", format="email", example="guest@example.com"),
 *           @OA\Property(property="phone", type="number", example="123456789"),
 *       )
 *    ),
 *    @OA\Response(
 *        response=200,
 *        description="Guest created successfully",
 *    ),
 *    @OA\Response(
 *        response=422,
 *        description="Validation error"
 *    )
 * )
 */
    public function store(StoreRequest $request)
    {
        return response()->json(Guest::create($request->validated()), 200);
    }

    /**
 * Display the specified guest.
 *
 * @OA\Get(
 *    path="/api/guest/{id}",
 *    summary="Get a specific guest",
 *    tags={"Guest"},
 *    description="Retrieve a specific guest by ID",
 *    @OA\Parameter(
 *        name="id",
 *        in="path",
 *        required=true,
 *        description="ID of the guest",
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
 *        description="Guest not found"
 *    )
 * )
 */
    public function show($id)
    {
        $guest = Guest::findOrFail($id);
        return response()->json($guest, 200);
    }

/**
 * Update an existing guest.
 *
 * @OA\Put(
 *    path="/api/guest/{id}",
 *    summary="Update an existing guest",
 *    tags={"Guest"},
 *    description="Update an existing guest by ID",
 *    @OA\Parameter(
 *        name="id",
 *        in="path",
 *        required=true,
 *        description="ID of the guest",
 *        @OA\Schema(
 *            type="integer",
 *            format="int64"
 *        )
 *    ),
 *    @OA\RequestBody(
 *       required=true,
 *       @OA\JsonContent(
 *           required={"name", "lastName", "dniPassport", "email", "phone"},
 *           @OA\Property(property="name", type="string", example="Updated Guest Name"),
 *           @OA\Property(property="lastName", type="string", example="Updated Guest Last Name"),
 *           @OA\Property(property="dniPassport", type="string", example="89653691D"),
 *           @OA\Property(property="email", type="string", format="email", example="updated-guest-email@gmail.com"),
 *           @OA\Property(property="phone", type="number", example="987654321"),
 *       )
 *    ),
 *    @OA\Response(
 *        response=200,
 *        description="Guest updated successfully",
 *    ),
 *    @OA\Response(
 *        response=404,
 *        description="Guest not found"
 *    )
 * )
 */
    public function update(StoreRequest $request, $id)
    {
        $guest = Guest::findOrFail($id);
        $guest->update($request->validated());
        return response()->json($guest, 200);
    }

    /**
     * @OA\Delete(
     *    path="/api/guest/{id}",
     *    summary="Delete a specific guest",
     *    tags={"Guest"},
     *    description="Delete a specific guest by ID",
     *    @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *        description="ID of the guest",
     *        @OA\Schema(
     *            type="integer",
     *            format="int64"
     *        )
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="Guest deleted successfully",
     *    ),
     *    @OA\Response(
     *        response=404,
     *        description="Guest not found"
     *    )
     * )
     */
    public function destroy($id)
    {
        $guest = Guest::findOrFail($id);
        $guest->delete();
        return response()->json(null, 200);
    }

    /**
     * @OA\Get(
     *    path="/api/guest/search/{name}",
     *    summary="Search guests by name",
     *    tags={"Guest"},
     *    description="Search guests by name",
     *    @OA\Parameter(
     *        name="name",
     *        in="path",
     *        required=true,
     *        description="Name of the guest",
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
        return response()->json(Guest::where('name', 'like', "%$name%")->get());
    }

    /**
     * @OA\Get(
     *    path="/api/guest/{guestId}/rooms",
     *    summary="Get rooms of a specific guest",
     *    tags={"Guest"},
     *    description="Get rooms of a specific guest by ID",
     *    @OA\Parameter(
     *        name="guestId",
     *        in="path",
     *        required=true,
     *        description="ID of the guest",
     *        @OA\Schema(
     *            type="integer",
     *            format="int64"
     *        )
     *    ),
     *    @OA\Response(
     *        response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="number", type="number", example="1"),
     *                 @OA\Property(property="checkInDate", type="string", format="date", example="2024-07-30"),
     *                 @OA\Property(property="checkOutDate", type="string", format="date", example="2024-08-01")
     *             )
     *         )
     *    ),
     *    @OA\Response(
     *        response=404,
     *        description="Guest not found"
     *    )
     * )
     */
    public function getRooms($guestId)
    {
        // $guest = Guest::findOrFail($id);
        // return response()->json($guest->rooms, 200);

        $rooms = DB::table('room_guests')
            ->join('rooms', 'room_guests.room_id', '=', 'rooms.id')
            ->where('room_guests.guest_id', $guestId)
            ->select('rooms.id', 'rooms.number', 'room_guests.checkInDate', 'room_guests.checkOutDate')
            ->get();

        return response()->json($rooms);
    }

    /**
     * @OA\Post(
     *    path="/api/guest/{guest}/rooms",
     *    summary="Add rooms to a specific guest",
     *    tags={"Guest"},
     *    description="Add rooms to a specific guest by ID",
     *    @OA\Parameter(
     *        name="guest",
     *        in="path",
     *        required=true,
     *        description="ID of the guest",
     *        @OA\Schema(
     *            type="integer",
     *            format="int64"
     *        )
     *    ),
     *    @OA\RequestBody(
     *       required=true,
     *       @OA\JsonContent(
     *           required={"guest_id", "room_id", "checkInDate", "checkOutDate"},
     *           @OA\Property(property="guest_id", type="integer", example=1),
     *           @OA\Property(property="room_id", type="array", @OA\Items(type="integer", example=1)),
     *           @OA\Property(property="checkInDate", type="string", format="date", example="2021-10-01"),
     *           @OA\Property(property="checkOutDate", type="string", format="date", example="2021-10-10"),
     *       )
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="Rooms added successfully",
     *    ),
     *    @OA\Response(
     *        response=404,
     *        description="Guest not found"
     *    )
     * )
     */
    public function addRooms(Guest $guest, StoreRoomRequest $request)
    {
        return app(RoomGuestController::class)->storeRooms($guest, $request);
    }

    // /**
    //  * @OA\Delete(
    //  *    path="/api/guest/{guest}/rooms",
    //  *    summary="Remove rooms from a specific guest",
    //  *    tags={"Guest"},
    //  *    description="Remove rooms from a specific guest by ID",
    //  *    @OA\Parameter(
    //  *        name="guest",
    //  *        in="path",
    //  *        required=true,
    //  *        description="ID of the guest",
    //  *        @OA\Schema(
    //  *            type="integer",
    //  *            format="int64"
    //  *        )
    //  *    ),
    //  *    @OA\RequestBody(
    //  *       required=true,
    //  *       @OA\JsonContent(
    //  *           required={"guest_id", "room_id", "checkInDate", "checkOutDate"},
    //  *           @OA\Property(property="guest_id", type="integer", example=1),
    //  *           @OA\Property(property="room_id", type="array", @OA\Items(type="integer", example=1)),
    //  *           @OA\Property(property="checkInDate", type="string", format="date", example="2021-10-01"),
    //  *           @OA\Property(property="checkOutDate", type="string", format="date", example="2021-10-10"),
    //  *       )
    //  *    ),
    //  *    @OA\Response(
    //  *        response=200,
    //  *        description="rooms removed successfully",
    //  *    ),
    //  *    @OA\Response(
    //  *        response=404,
    //  *        description="Guest not found"
    //  *    )
    //  * )
    //  */
    // public function removeRooms(Guest $guest, StoreRoomRequest $request)
    // {
    //     $roomIds = $request->validated()['room_id'] ?? [];
    //     $guest->rooms()->detach($roomIds, [
    //         'checkInDate' => $request->validated()['checkInDate'],
    //         'checkOutDate' => $request->validated()['checkOutDate'],
    //     ]);
    //     return response()->json($guest->rooms, 200);
    // }

    /**
 * @OA\Delete(
 *    path="/api/guest/{guestId}/rooms",
 *    summary="Remove rooms from a guest",
 *    tags={"Guest"},
 *    description="Remove rooms from a specific guest by ID and matching dates",
 *    @OA\Parameter(
 *        name="guestId",
 *        in="path",
 *        required=true,
 *        description="ID of the guest",
 *        @OA\Schema(
 *            type="integer",
 *            format="int64"
 *        )
 *    ),
 *    @OA\RequestBody(
 *       required=true,
 *       @OA\JsonContent(
 *           required={"guest_id", "room_id", "checkInDate", "checkOutDate"},
 *           @OA\Property(property="guest_id", type="integer", example=1),
 *           @OA\Property(property="room_id", type="array", @OA\Items(type="integer", example=1)),
 *           @OA\Property(property="checkInDate", type="string", format="date", example="2021-10-01"),
 *           @OA\Property(property="checkOutDate", type="string", format="date", example="2021-10-10"),
 *       )
 *    ),
 *    @OA\Response(
 *        response=200,
 *        description="Rooms removed successfully",
 *    ),
 *    @OA\Response(
 *        response=404,
 *        description="Guest not found"
 *    )
 * )
 */
public function removeRooms(Guest $guest, StoreRoomRequest $request)
{
    $roomIds = $request->input('room_id', []);
    $checkInDate = $request->input('checkInDate');
    $checkOutDate = $request->input('checkOutDate');

    if (empty($roomIds) || !$checkInDate || !$checkOutDate) {
        return response()->json(['message' => 'Invalid input'], 400);
    }

    foreach ($roomIds as $roomId) {
        $guest->rooms()->wherePivot('room_id', $roomId)
            ->wherePivot('checkInDate', $checkInDate)
            ->wherePivot('checkOutDate', $checkOutDate)
            ->detach();
    }

    return response()->json($guest->rooms, 200);
}


    /**
     * @OA\Get(
     *    path="/api/guest/{id}/services",
     *    summary="Get services of a specific guest",
     *    tags={"Guest"},
     *    description="Get services of a specific guest by ID",
     *    @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *        description="ID of the guest",
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
     *        description="Guest not found"
     *    )
     * )
     */
    public function getServices($id)
    {
        $guest = Guest::findOrFail($id);
        return response()->json($guest->services, 200);
    }

    /**
     * @OA\Post(
     *    path="/api/guest/{guest}/services",
     *    summary="Add services to a specific guest",
     *    tags={"Guest"},
     *    description="Add services to a specific guest by ID",
     *    @OA\Parameter(
     *        name="guest",
     *        in="path",
     *        required=true,
     *        description="ID of the guest",
     *        @OA\Schema(
     *            type="integer",
     *            format="int64"
     *        )
     *    ),
     *    @OA\RequestBody(
     *       required=true,
     *       @OA\JsonContent(
     *           required={"guest_id", "service_id"},
     *           @OA\Property(property="guest_id", type="integer", example=1),
     *           @OA\Property(property="service_id", type="array", @OA\Items(type="integer", example=1))
     *       )
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="Services added successfully",
     *    ),
     *    @OA\Response(
     *        response=404,
     *        description="Guest not found"
     *    )
     * )
     */
    public function addServices(Guest $guest, StoreServiceRequest $request)
    {
        $serviceIds = $request->validated()['service_id'] ?? [];
        $guest->services()->attach($serviceIds);
        return response()->json($guest->services, 200);
    }

    /**
     * @OA\Delete(
     *    path="/api/guest/{guest}/services",
     *    summary="Remove services from a specific guest",
     *    tags={"Guest"},
     *    description="Remove services from a specific guest by ID",
     *    @OA\Parameter(
     *        name="guest",
     *        in="path",
     *        required=true,
     *        description="ID of the guest",
     *        @OA\Schema(
     *            type="integer",
     *            format="int64"
     *        )
     *    ),
     *    @OA\RequestBody(
     *       required=true,
     *       @OA\JsonContent(
     *           required={"guest_id", "service_id"},
     *           @OA\Property(property="guest_id", type="integer", example=1),
     *           @OA\Property(property="service_id", type="array", @OA\Items(type="integer", example=1))
     *       )
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="Services removed successfully",
     *    ),
     *    @OA\Response(
     *        response=404,
     *        description="Guest not found"
     *    )
     * )
     */
    public function removeServices(Guest $guest, StoreServiceRequest $request)
    {
        $serviceIds = $request->validated()['service_id'] ?? [];
        $guest->services()->detach($serviceIds);
        return response()->json($guest->services, 200);
    }
}
