<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PempekSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Kategori
        $cat1 = \App\Models\Category::create(['name' => 'Pempek Satuan', 'slug' => 'pempek-satuan']);
        $cat2 = \App\Models\Category::create(['name' => 'Paket Hemat', 'slug' => 'paket-hemat']);

        // 2. Isi Produk (Contoh Menu Ibu)
        \App\Models\Product::create([
            'category_id' => $cat1->id,
            'name' => 'Pempek Kapal Selam Besar',
            'price' => 25000,
            'discount_price' => 20000,
            'description' => 'Pempek telur besar dengan cuko mantap',
            'is_available' => true
        ]);

        \App\Models\Product::create([
            'category_id' => $cat2->id,
            'name' => 'Paket Kenyang A (5 Lenjer + 5 Adaan)',
            'price' => 50000,
            'discount_price' => 45000,
            'description' => 'Paket hemat buat makan bareng keluarga',
            'is_available' => true
        ]);
    }
}
