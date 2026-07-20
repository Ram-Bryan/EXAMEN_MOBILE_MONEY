<?php

namespace App\Models;

use CodeIgniter\Model;

class OperateurPrefixeModel extends Model
{
    protected $table            = 'operateur_prefixes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['prefixe'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = null;
}