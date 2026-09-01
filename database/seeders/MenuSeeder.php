<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['id' => 1, 'name' => 'Ayam Crispy', 'sort_order' => 1],
            ['id' => 2, 'name' => 'Fire Chicken', 'sort_order' => 2],
            ['id' => 3, 'name' => 'Fire Chicken Wings', 'sort_order' => 3],
            ['id' => 4, 'name' => 'Ayam Sambal Bawang', 'sort_order' => 4],
            ['id' => 5, 'name' => 'Ala Carte', 'sort_order' => 5],
            ['id' => 6, 'name' => 'Add On', 'sort_order' => 6],
            ['id' => 7, 'name' => 'Paket Hemat', 'sort_order' => 7],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['id' => $cat['id']], $cat);
        }

        $menus = [
            [1, 'Ayam Crispy Tanpa Nasi', 'Ayam crispy digoreng garing, disajikan dengan saus sachet dan free nugget.', 8000, 25, 1],
            [1, 'Ayam Crispy + Nasi', 'Ayam crispy digoreng garing dengan nasi hangat, saus sachet, dan free nugget.', 10000, 25, 2],
            [1, 'Ayam Crispy + Mie', 'Ayam crispy digoreng garing dengan mie, saus sachet, dan free nugget.', 13000, 20, 3],
            [2, 'Fire Chicken Tanpa Nasi', 'Ayam crispy dibalur saus fire pedas nampol, saus bisa dipisah/dicampur, free nugget.', 11000, 20, 1],
            [2, 'Fire Chicken + Nasi', 'Ayam crispy dibalur saus fire pedas nampol dengan nasi hangat, free nugget.', 13000, 20, 2],
            [2, 'Fire Chicken + Mie', 'Ayam crispy dibalur saus fire pedas nampol dengan mie, free nugget.', 15000, 15, 3],
            [3, 'Fire Chicken Wings Tanpa Nasi', 'Sayap ayam crispy dibalur saus fire pedas, saus bisa dipisah/dicampur, free nugget.', 12000, 15, 1],
            [3, 'Fire Chicken Wings + Nasi', 'Sayap ayam crispy dibalur saus fire pedas dengan nasi hangat, free nugget.', 14000, 15, 2],
            [3, 'Fire Chicken Wings + Mie', 'Sayap ayam crispy dibalur saus fire pedas dengan mie, free nugget.', 16000, 10, 3],
            [4, 'Ayam Crispy Sambal Bawang Tanpa Nasi', 'Ayam crispy dengan siraman sambal bawang segar, free nugget.', 11000, 20, 1],
            [4, 'Ayam Crispy Sambal Bawang + Nasi', 'Ayam crispy dengan siraman sambal bawang segar dan nasi hangat, free nugget.', 13000, 20, 2],
            [4, 'Ayam Crispy Sambal Bawang + Mie', 'Ayam crispy dengan siraman sambal bawang segar dan mie, free nugget.', 15000, 15, 3],
            [5, 'Nasi', 'Nasi putih hangat pulen.', 3000, null, 1],
            [5, 'Nugget (isi 4)', 'Nugget crispy isi 4 pcs, cocok jadi teman makan.', 5000, 30, 2],
            [5, 'Telur Ceplok', 'Telur ceplok digoreng matang sempurna.', 3500, 30, 3],
            [5, 'Mie + Telur + Sosis', 'Mie goreng dengan telur dan potongan sosis.', 8000, 15, 4],
            [5, 'Kerupuk Finna', 'Kerupuk renyah favorit semua orang.', 1500, null, 5],
            [6, 'Sambal Bawang', 'Sambal bawang segar, level pedas nampol.', 2500, null, 1],
            [6, 'Saus Fire', 'Saus fire pedas kental untuk cocolan ayam.', 2500, null, 2],
            [7, 'Hemat 1', 'Paket spesial hemat untuk makan kenyang tanpa bikin kantong bolong.', 12000, 10, 1],
            [7, 'Hemat 2', 'Paket spesial hemat untuk makan kenyang tanpa bikin kantong bolong.', 15000, 10, 2],
            [7, 'Hemat 3', 'Paket spesial hemat untuk makan kenyang tanpa bikin kantong bolong.', 15000, 10, 3],
        ];

        foreach ($menus as [$catId, $name, $desc, $price, $stock, $sort]) {
            Menu::firstOrCreate(
                ['category_id' => $catId, 'name' => $name],
                [
                    'description' => $desc,
                    'price' => $price,
                    'is_available' => true,
                    'stock' => $stock,
                    'sort_order' => $sort,
                ]
            );
        }
    }
}
