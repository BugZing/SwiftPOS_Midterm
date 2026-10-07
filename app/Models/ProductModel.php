<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table            = 'products';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'price',
        'stock_quantity',
        'image',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    protected $validationRules = [
        'name'           => 'required|max_length[100]',
        'price'          => 'required|decimal|greater_than_equal_to[0]',
        'stock_quantity' => 'required|integer|greater_than_equal_to[0]',
        'image'          => 'permit_empty|max_length[255]',
    ];

    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
