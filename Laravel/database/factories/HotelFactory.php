<?php

namespace Database\Factories;

use App\Models\Hotel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Hotel>
 */
class HotelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Hotel::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Configura tu clave de API de Unsplash
        $unsplashApiKey = 'S16g_JuLbEJD6OBEHaBC5kpZs0MEvmW0PSIBHwg8MKk';
        
        // Realiza una solicitud a la API de Unsplash para obtener una imagen de hotel
        $response = Http::get("https://api.unsplash.com/photos/random", [
            'query' => 'hotel',
            'client_id' => $unsplashApiKey,
            'orientation' => 'landscape', // Opcional, para imágenes en paisaje
            'count' => 1
        ]);

        // Verifica si la respuesta contiene imágenes
        $imageUrl = $response->json()[0]['urls']['regular'] ?? 'https://via.placeholder.com/640x480'; // Usa una URL de marcador de posición si no se obtiene ninguna imagen
        
        return [
            'name' => $this->faker->company(),
            'address' => $this->faker->city(),
            'stars' => $this->faker->numberBetween(1, 5),
            'phone' => $this->faker->randomNumber(9),
            'email' => $this->faker->companyEmail(),
            'website' => $this->faker->url(),
            'image' => $imageUrl,
        ];
    }
}
