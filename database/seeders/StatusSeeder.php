<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Status::create("Oczekujący");
        Status::create("W trakcie realizacji");
        Status::create("Zakończony pomyślnie");
        Status::create("Odrzucony");
    }
}
