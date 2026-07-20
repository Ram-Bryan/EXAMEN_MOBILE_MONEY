<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(OperateurPrefixesSeeder::class);
        $this->call(TypesOperationSeeder::class);
        $this->call(BaremesFraisSeeder::class);
        $this->call(AdminSeeder::class);
        $this->call(ClientsSeeder::class);
        $this->call(TransactionsSeeder::class);
    }
}