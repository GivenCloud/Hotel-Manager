<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    protected static function boot()
    {
        parent::boot();

        // static::creating(function ($category) {
        //     // Asignar el ID o nombre del usuario autenticado
        //     $category->created_by = Auth::id(); 
        // });

        // static::updating(function ($category) {
        //     // Asignar el ID del usuario autenticado al actualizar
        //     $category->updated_by = Auth::id();
        // });
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
