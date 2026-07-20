<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBaremesFraisTable extends Migration
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
            'type_operation_id' => [
                'type'       => 'INTEGER',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'operateur_id' => [
                'type'       => 'INTEGER',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('type_operation_id', 'types_operation', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('operateur_id', 'operateur_prefixes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('baremes_frais');
    }

    public function down()
    {
        $this->forge->dropTable('baremes_frais');
    }
}