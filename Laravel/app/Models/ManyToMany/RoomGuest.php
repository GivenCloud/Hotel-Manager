<?php

namespace App\Models\ManyToMany;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class RoomGuest extends Model
{
    use HasFactory;
    protected $fillable = ['id', 'room_id', 'guest_id', 'checkInDate', 'checkOutDate'];

    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Asegurarse de que no haya solapamientos de fechas
            $overlap = self::where('room_id', $model->room_id)
                ->where('guest_id', $model->guest_id)
                ->where(function ($query) use ($model) {
                    $query->whereBetween('checkInDate', [$model->checkInDate, $model->checkOutDate])
                          ->orWhereBetween('checkOutDate', [$model->checkInDate, $model->checkOutDate])
                          ->orWhere(function ($query) use ($model) {
                              $query->where('checkInDate', '<=', $model->checkInDate)
                                    ->where('checkOutDate', '>=', $model->checkOutDate);
                          });
                })
                ->exists();

            if ($overlap) {
                throw new ValidationException("The dates overlap with an existing booking.");
            }
        });
    }

}
