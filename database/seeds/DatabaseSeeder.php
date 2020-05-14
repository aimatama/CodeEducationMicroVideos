<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        //$this->call(CategoriesTableSeeder::class);
        factory(\App\Models\Category::class, 25)->create();
        factory(\App\Models\Genre::class, 12)->create();
        factory(\App\Models\CastMember::class, 100)->create();
    }
}
