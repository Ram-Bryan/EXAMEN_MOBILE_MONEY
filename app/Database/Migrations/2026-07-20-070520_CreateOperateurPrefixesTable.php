<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOperateurPrefixesTable extends Migration
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
            'prefixe' => [
                'type'       => 'TEXT',
                'null'       => false,
                'unique'     => true,
            ],
            'created_at' => [
                'type'       => 'DATETIME',
                'null'       => false,
                'default'    => 'CURRENT_TIMESTAMP',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('operateur_prefixes');
    }

    public function down()
    {
        $this->forge->dropTable('operateur_prefixes');
    }
}