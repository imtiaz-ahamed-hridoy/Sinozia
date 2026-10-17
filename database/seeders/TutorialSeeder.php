<?php

namespace Database\Seeders;

use App\Models\Tutorial;
use Illuminate\Database\Seeder;

class TutorialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Tutorial::insert([
            [
                'title' => 'Commencez avec une photo ou une vidéo',
                'description' => 'Importez un fichier depuis votre galerie ou utilisez la caméra.',
                'image_url' => 'backend/assets/images/Group2.png',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Sélectionnez un visage',
                'description' => 'KChoisissez le visage à appliquer.',
                'image_url' => 'backend/assets/images/Group2.png',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

    }
}
