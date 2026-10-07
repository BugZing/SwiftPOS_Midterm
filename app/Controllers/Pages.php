<?php

namespace App\Controllers;

use App\Models\CustomerModel;
use App\Models\ProductModel;
use App\Models\SaleModel;
use App\Models\UserModel;

class Pages extends BaseController
{
    public function index(): string
    {
        $customerModel = new CustomerModel();
        $userModel     = new UserModel();
        $productModel  = new ProductModel();
        $saleModel     = new SaleModel();

        $productCount  = $productModel->countAllResults();
        $lowStockCount = $productModel->where('stock_quantity <=', 5)->countAllResults();
        $customerCount = $customerModel->countAllResults();
        $userCount     = $userModel->countAllResults();

        $recentSales = $saleModel->getSalesHistory();
        $recentSales = array_slice($recentSales, 0, 5);

        $allSales     = $saleModel->findAll();
        $totalRevenue = 0.0;
        $totalSold    = 0;
        foreach ($allSales as $s) {
            $totalRevenue += (float) $s['total_price'];
            $totalSold    += (int) $s['quantity'];
        }

        $data = [
            'title'         => 'Dashboard | Complete POS',
            'productCount'  => $productCount,
            'lowStockCount' => $lowStockCount,
            'customerCount' => $customerCount,
            'userCount'     => $userCount,
            'totalRevenue'  => $totalRevenue,
            'totalSales'    => count($allSales),
            'totalSold'     => $totalSold,
            'recentSales'   => $recentSales,
        ];

        return view('templates/header', $data)
             . view('pages/landing', $data)
             . view('templates/footer');
    }

    public function about(): string
    {
        $data = [
            'title' => 'System Info | Complete POS',
        ];

        return view('templates/header', $data)
             . view('pages/about', $data)
             . view('templates/footer');
    }
}
