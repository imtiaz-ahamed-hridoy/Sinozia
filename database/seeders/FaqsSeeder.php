<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Faq::insert([
            [
                'question' => 'Pourquoi le résultat n’est-il pas toujours identique au visage sélectionné ?',
                'answer' => 'Le rendu dépend de la qualité du fichier utilisé : éclairage, angle du visage, netteté et expressions.
                            Des images claires, bien éclairées et de face donnent de meilleurs résultats.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Quelle est la différence entre “Ressemblance” et “Qualité” ?',
                'answer' => 'Le rendu dépend de la qualité du fichier utilisé : éclairage, angle du visage, netteté et expressions.
                            Des images claires, bien éclairées et de face donnent de meilleurs résultats.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Pourquoi la création peut-elle échouer ?',
                'answer' => 'Le rendu dépend de la qualité du fichier utilisé : éclairage, angle du visage, netteté et expressions.
                            Des images claires, bien éclairées et de face donnent de meilleurs résultats.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Combien de temps prend la création ?',
                'answer' => 'Le rendu dépend de la qualité du fichier utilisé : éclairage, angle du visage, netteté et expressions.
                            Des images claires, bien éclairées et de face donnent de meilleurs résultats.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Mes photos et vidéos sont-elles stockées ou envoyées ailleurs ?',
                'answer' => 'Le rendu dépend de la qualité du fichier utilisé : éclairage, angle du visage, netteté et expressions.
                            Des images claires, bien éclairées et de face donnent de meilleurs résultats.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Pourquoi l’application demande l’accès à la galerie ou à la caméra ?',
                'answer' => 'Le rendu dépend de la qualité du fichier utilisé : éclairage, angle du visage, netteté et expressions.
                            Des images claires, bien éclairées et de face donnent de meilleurs résultats.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Puis-je utiliser plusieurs visages sur une même image ou vidéo ?',
                'answer' => 'Le rendu dépend de la qualité du fichier utilisé : éclairage, angle du visage, netteté et expressions.
                            Des images claires, bien éclairées et de face donnent de meilleurs résultats.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'À quoi sert l’option “Conserver la bouche” ?',
                'answer' => 'Le rendu dépend de la qualité du fichier utilisé : éclairage, angle du visage, netteté et expressions.
                            Des images claires, bien éclairées et de face donnent de meilleurs résultats.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Pourquoi le rendu est différent entre le mode Galerie et le mode Temps réel ?',
                'answer' => 'Le rendu dépend de la qualité du fichier utilisé : éclairage, angle du visage, netteté et expressions.
                            Des images claires, bien éclairées et de face donnent de meilleurs résultats.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Que faire si l’application semble bloquée pendant la création ?',
                'answer' => 'Le rendu dépend de la qualité du fichier utilisé : éclairage, angle du visage, netteté et expressions.
                            Des images claires, bien éclairées et de face donnent de meilleurs résultats.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
