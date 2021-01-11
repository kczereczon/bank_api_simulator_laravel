<?php

namespace Database\Seeders;

use App\Models\Status;
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
        Status::create(["name" => "Oczekujący"]);
        Status::create(["name" => "W trakcie realizacji"]);
        Status::create(["name" => "Zakończony pomyślnie"]);
        Status::create(["name" => "Odrzucony"]);
    }
}
