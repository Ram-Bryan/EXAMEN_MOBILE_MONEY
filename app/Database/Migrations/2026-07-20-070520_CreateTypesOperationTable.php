<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTypesOperationTable extends Migration
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
            'code' => [
                'type'       => 'TEXT',
                'null'       => false,
                'unique'     => true,
            ],
            'nom' => [
                'type'       => 'TEXT',
                'null'       => false,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('types_operation');
    }

    public function down()
    {
        $this->forge->dropTable('types_operation');
    }
}