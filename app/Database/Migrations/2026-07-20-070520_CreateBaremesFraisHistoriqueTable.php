<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBaremesFraisHistoriqueTable extends Migration
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
            'bareme_id' => [
                'type'       => 'INTEGER',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'montant_min' => [
                'type'       => 'REAL',
                'null'       => false,
            ],
            'montant_max' => [
                'type'       => 'REAL',
                'null'       => true,
            ],
            'frais_fixe' => [
                'type'       => 'REAL',
                'null'       => false,
                'default'    => 0.0,
            ],
            'date_modif' => [
                'type'       => 'DATETIME',
                'null'       => false,
                'default'    => 'CURRENT_TIMESTAMP',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('bareme_id', 'baremes_frais', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('baremes_frais_historique');
    }

    public function down()
    {
        $this->forge->dropTable('baremes_frais_historique');
    }
}