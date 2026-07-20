<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TypesOperationSeeder extends Seeder
{
    public function run()
    {
        if ($this->db->table('types_operation')->countAll() > 0) {
            return;
        }

        $data = [
            ['code' => 'DEPOT', 'nom' => 'Dépôt'],
            ['code' => 'RETRAIT', 'nom' => 'Retrait'],
            ['code' => 'TRANSFERT', 'nom' => 'Transfert'],
        ];

        $this->db->table('types_operation')->insertBatch($data);
    }
}