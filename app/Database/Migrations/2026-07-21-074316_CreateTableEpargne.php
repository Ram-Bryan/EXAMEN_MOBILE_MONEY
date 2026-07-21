<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableEpargne extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INTEGER',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'client_id' => [
                'type'       => 'INTEGER',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'montant' => [
                'type'       => 'REAL',
                'null'       => false,
            ],
            'pourcentage' => [
                'type'       => 'REAL',
                'null'       => false,
            ],
            'date_modif' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('client_id', 'clients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('epargne');
    }

    public function down()
    {
        $this->forge->dropTable('epargne');
    }
}