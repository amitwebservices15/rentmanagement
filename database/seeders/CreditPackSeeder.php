<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CreditPack;

class CreditPackSeeder extends Seeder
{
    public function run(): void
    {
        $packs = [
            [
                'name' => 'Starter Pack',
                'price' => 99,
                'credits' => 100,
                'tag' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Value Pack',
                'price' => 199,
                'credits' => 220,
                'tag' => 'Best Value',
                'is_active' => true,
            ],
            [
                'name' => 'Power Pack',
                'price' => 399,
                'credits' => 500,
                'tag' => 'Popular',
                'is_active' => true,
            ],
            [
                'name' => 'Mega Pack',
                'price' => 699,
                'credits' => 1000,
                'tag' => 'Most Credits',
                'is_active' => true,
            ],
        ];

        foreach ($packs as $pack) {
            CreditPack::updateOrCreate(
                ['name' => $pack['name']],
                $pack
            );
        }
    }
}