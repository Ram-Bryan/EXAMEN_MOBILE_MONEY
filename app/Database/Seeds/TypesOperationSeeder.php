<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TypesOperationSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['id' => 1, 'code' => 'DEPOT', 'nom' => 'Dépôt'],
            ['id' => 2, 'code' => 'RETRAIT', 'nom' => 'Retrait'],
            ['id' => 3, 'code' => 'TRANSFERT', 'nom' => 'Transfert'],
        ];

        $this->db->table('types_operation')->insertBatch($data);
    }
}