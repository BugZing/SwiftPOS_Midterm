<?php

namespace App\Models;

use CodeIgniter\Model;

class SaleModel extends Model
{
    protected $table            = 'sales';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'product_id',
        'customer_id',
        'sold_by',
        'quantity',
        'total_price',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    protected $validationRules = [
        'product_id'  => 'required|is_natural_no_zero',
        'customer_id' => 'permit_empty',
        'sold_by'     => 'required|is_natural_no_zero',
        'quantity'    => 'required|is_natural_no_zero',
        'total_price' => 'required|decimal|greater_than_equal_to[0]',
    ];

    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    public function getSalesHistory(): array
    {
        return $this->select('sales.*, products.name as product_name, products.price as product_price, products.image as product_image, customers.full_name as customer_name, customers.email as customer_email, users.full_name as staff_name, users.username as staff_username, users.avatar as staff_avatar')
            ->join('products', 'products.id = sales.product_id', 'inner')
            ->join('customers', 'customers.id = sales.customer_id', 'left')
            ->join('users', 'users.id = sales.sold_by', 'inner')
            ->orderBy('sales.created_at', 'DESC')
            ->orderBy('sales.id', 'DESC')
            ->findAll();
    }
}
