<?php

namespace App\Http\Controllers\Api\ManyToMany;

use App\Http\Controllers\Controller;
use App\Http\Requests\ManyToMany\RoomGuest\StoreRoomRequest;
use App\Http\Requests\ManyToMany\RoomGuest\StoreGuestRequest;
use App\Models\Room;
use App\Models\ManyToMany\RoomGuest;
use App\Models\Guest;

/**
 * @OA\Tag(
 *   name="RoomGuest",
 *   description="API Endpoints of RoomGuest Controller"
 * )
 */

class RoomGuestController extends Controller
{
    /**
     * @OA\Get(
     *    path="/api/room-guest",
     *    summary="Get all room-guest",
     *    tags={"RoomGuest"},
     *    description="List of room-guest",
     *    @OA\Response(
     *        response=200,
     *        description="Successful operation",
     *    )
     * )
     */
    public function index()
    {
        return response()->json(RoomGuest::get(), 200);
    }

    /**
     * @OA\Post(
     *     path="/api/room-guests/{guest}/add-rooms",
     *     tags={"RoomGuest"},
     *     summary="Add rooms to a guest",
     *     description="Add rooms to a guest", 
     *     @OA\Parameter(
     *        name="guest",
     *        in="path",
     *        required=true,
     *        description="ID of the guest",
     *        @OA\Schema(
     *            type="integer",
     *            format="int64"
     *        )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *           required={"guest_id", "room_id", "checkInDate", "checkOutDate"},
     *           @OA\Property(property="guest_id", type="integer", example=1),
     *           @OA\Property(property="room_id", type="array", @OA\Items(type="integer", example=1)),
     *           @OA\Property(property="checkInDate", type="string", format="date", example="2021-10-01"),
     *           @OA\Property(property="checkOutDate", type="string", format="date", example="2021-10-10"),
     *       )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     )
     * )
     */
    public function storeRooms(Guest $guest, StoreRoomRequest $request, $update = false)
    {
        $roomIds = $request->validated()['room_id'] ?? [];
        $errors = []; // Array para acumular los mensajes de error

        // Si estamos en modo actualización, eliminamos todas las habitaciones actuales
        if ($update) {
            $guest->rooms()->detach();
        }

        foreach ($roomIds as $roomId) {
            $room = Room::findOrFail($roomId);
            $capacity = $room->type->capacity;

            if ($room->guests()->count() >= $capacity) {
                // Agregar el mensaje de error al array de errores
                $errors[] = 'Room ' . $room->number . ' is full';
            } else {
                // Añadir el huésped a la habitación
                $room->guests()->attach($guest->id, [
                    'checkInDate' => $request->validated()['checkInDate'],
                    'checkOutDate' => $request->validated()['checkOutDate'],
                ]);
            }
        }

        // Construir la respuesta
        $response = [
            'guestRooms' => $guest->rooms,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
            // Devolver la respuesta con un código de estado 400 si hay errores
            // return response()->json($response, 400);
        }

        // Devolver la respuesta con un código de estado 200 si no hay errores
        return response()->json($response, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/room-guests/{room}/add-guests",
     *     tags={"RoomGuest"},
     *     summary="Add guests to a room",
     *     description="Add guests to a room",
     *     @OA\Parameter(
     *        name="room",
     *        in="path",
     *        required=true,
     *        description="ID of the room",
     *        @OA\Schema(
     *            type="integer",
     *            format="int64"
     *        )
     *    ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *           required={"guest_id", "room_id", "checkInDate", "checkOutDate"},
     *           @OA\Property(property="room_id", type="integer", example=1),
     *           @OA\Property(property="guest_id", type="array", @OA\Items(type="integer", example=1)),
     *           @OA\Property(property="checkInDate", type="string", format="date", example="2021-10-01"),
     *           @OA\Property(property="checkOutDate", type="string", format="date", example="2021-10-10"),
     *       )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     )
     * )
     */
    public function storeGuests(Room $room, StoreGuestRequest $request, $update = false)
    {
        // Obtener los IDs de los huéspedes a añadir
        $guestIds = $request->validated()['guest_id'] ?? [];
        $capacity = $room->type->capacity;
        $errors = []; // Array para acumular los mensajes de error

        // Si estamos en modo actualización, eliminamos todos los huéspedes actuales
        if ($update) {
            $room->guests()->detach();
        }

        foreach ($guestIds as $guestId) {
           if ($room->guests()->count() >= $capacity) {
               // Agregar el mensaje de error al array de errores
               $errors[] = 'Could not add guest ' . $guestId . ' to room ' . $room->id . ' because is full';
               break; // Romper el bucle si la habitación está llena
            } else {
               // Añadir el huésped a la habitación
               $room->guests()->attach($guestId, [
                  'checkInDate' => $request->validated()['checkInDate'],
                  'checkOutDate' => $request->validated()['checkOutDate'],
                ]);
            }
        }

        // Construir la respuesta
        $response = [
            'roomGuests' => $room->guests,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
            // Devolver la respuesta con un código de estado 400 si hay errores
            // return response()->json($response, 400);
        }

        // Devolver la respuesta con un código de estado 200 si no hay errores
        return response()->json($response, 200);
    }

/**
 * Display the specified room-guest.
 *
 * @OA\Get(
 *    path="/api/room-guest/{id}",
 *    tags={"RoomGuest"},
 *    summary="Get a specific room-guest",
 *    description="Retrieve a specific roomguest by ID",
 *    @OA\Parameter(
 *        name="id",
 *        in="path",
 *        required=true,
 *        description="ID of the room-guest",
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
 *        description="Room-Guest not found"
 *    )
 * )
 */
    public function show($id)
    {
        $roomguest = RoomGuest::findOrFail($id);
        return response()->json($roomguest, 200);
    }

    /**
     * @OA\Put(
     *     path="/api/room-guests/{guest}/update-rooms",
     *     tags={"RoomGuest"},
     *     summary="Update rooms for a guest",
     *     description="Update rooms for a guest",
     *     @OA\Parameter(
     *        name="guest",
     *        in="path",
     *        required=true,
     *        description="ID of the guest",
     *        @OA\Schema(
     *            type="integer",
     *            format="int64"
     *        )
     *    ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *           required={"guest_id", "room_id", "checkInDate", "checkOutDate"},
     *           @OA\Property(property="guest_id", type="integer", example=1),
     *           @OA\Property(property="room_id", type="array", @OA\Items(type="integer", example=1)),
     *           @OA\Property(property="checkInDate", type="string", format="date", example="2021-10-01"),
     *           @OA\Property(property="checkOutDate", type="string", format="date", example="2021-10-10"),
     *       )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     )
     * )
     */
    public function updateRooms(Guest $guest, StoreRoomRequest $request)
    {
        return $this->storeRooms($guest, $request, true);
    }


    /**
     * @OA\Put(
     *     path="/api/room-guests/{room}/update-guests",
     *     tags={"RoomGuest"},
     *     summary="Update guests for a room",
     *     description="Update guests for a room",
     *     @OA\Parameter(
     *        name="room",
     *        in="path",
     *        required=true,
     *        description="ID of the room",
     *        @OA\Schema(
     *            type="integer",
     *            format="int64"
     *        )
     *    ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *           required={"guest_id", "room_id", "checkInDate", "checkOutDate"},
     *           @OA\Property(property="room_id", type="integer", example=1),
     *           @OA\Property(property="guest_id", type="array", @OA\Items(type="integer", example=1)),
     *           @OA\Property(property="checkInDate", type="string", format="date", example="2021-10-01"),
     *           @OA\Property(property="checkOutDate", type="string", format="date", example="2021-10-10"),
     *       )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     )
     * )
     */
    public function updateGuests(Room $room, StoreGuestRequest $request)
    {
        $this->storeGuests($room, $request, true);
    }

    /**
     * @OA\Delete(
     *    path="/api/room-guest/{id}",
     *    summary="Delete a specific room-guest",
     *    tags={"RoomGuest"},
     *    description="Delete a specific room-guest by ID",
     *    @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *        description="ID of the room-guest",
     *        @OA\Schema(
     *            type="integer",
     *            format="int64"
     *        )
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="Room-Guest deleted successfully",
     *    ),
     *    @OA\Response(
     *        response=404,
     *        description="Room-Guest not found"
     *    )
     * )
     */
    
    public function destroy($id)
    {
        $roomguest = RoomGuest::findOrFail($id);
        $roomguest->delete();
        return response()->json(null, 200);
    }
}
