<?php

namespace App\Controllers;

use App\Models\CustomerModel;
use App\Models\ProductModel;
use App\Models\SaleModel;
use CodeIgniter\HTTP\RedirectResponse;

class Sales extends BaseController
{
    public function index(): string
    {
        $saleModel = new SaleModel();
        $sales     = $saleModel->getSalesHistory();

        $totalRevenue = 0.0;
        $totalItems   = 0;
        foreach ($sales as $sale) {
            $totalRevenue += (float) $sale['total_price'];
            $totalItems   += (int) $sale['quantity'];
        }

        $data = [
            'title'        => 'Sales History | Complete POS',
            'sales'        => $sales,
            'totalRevenue' => $totalRevenue,
            'totalItems'   => $totalItems,
            'totalSales'   => count($sales),
        ];

        return view('templates/header', $data)
             . view('sales/index', $data)
             . view('templates/footer');
    }

    public function newForm(): string
    {
        $productModel  = new ProductModel();
        $customerModel = new CustomerModel();

        $data = [
            'title'     => 'Record Sale | Complete POS',
            'products'  => $productModel->orderBy('name', 'ASC')->findAll(),
            'customers' => $customerModel->orderBy('full_name', 'ASC')->findAll(),
            'values'    => [
                'product_id'  => (string) $this->request->getGet('product_id'),
                'customer_id' => '',
                'quantity'    => '1',
            ],
            'errors'    => [],
        ];

        return view('templates/header', $data)
             . view('sales/form', $data)
             . view('templates/footer');
    }

    public function create(): string|RedirectResponse
    {
        $productId  = (int) $this->request->getPost('product_id');
        $customerId = $this->request->getPost('customer_id');
        $customerId = ($customerId !== null && trim((string) $customerId) !== '') ? (int) $customerId : null;
        $quantity   = (int) $this->request->getPost('quantity');

        $productModel  = new ProductModel();
        $customerModel = new CustomerModel();

        if ($productId < 1 || $quantity < 1) {
            return $this->renderFormWithErrors([
                'Please select a valid product and enter a quantity of at least 1.',
            ]);
        }

        if ($customerId !== null && $customerModel->find($customerId) === null) {
            return $this->renderFormWithErrors([
                'The selected customer could not be found.',
            ]);
        }

        $product = $productModel->find($productId);
        if ($product === null) {
            return $this->renderFormWithErrors([
                'The selected product was not found in the catalog.',
            ]);
        }

        $availableStock = (int) $product['stock_quantity'];

        if ($quantity > $availableStock) {
            $message = sprintf(
                'Sale rejected: Requested quantity (%d) exceeds available stock (%d) for "%s".',
                $quantity,
                $availableStock,
                $product['name']
            );
            return $this->renderFormWithErrors([$message]);
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        $totalPrice = (float) $product['price'] * $quantity;
        $newStock   = $availableStock - $quantity;
        $staffId    = (int) session()->get('auth_user_id');

        $productModel->update($productId, [
            'stock_quantity' => $newStock,
        ]);

        $saleModel = new SaleModel();
        $saleModel->insert([
            'product_id'  => $productId,
            'customer_id' => $customerId,
            'sold_by'     => $staffId,
            'quantity'    => $quantity,
            'total_price' => $totalPrice,
        ]);

        if ($db->transStatus() === false) {
            $db->transRollback();
            return $this->renderFormWithErrors([
                'Failed to record the sale transaction due to a database error.',
            ]);
        }

        $db->transCommit();

        $successMessage = sprintf(
            'Sale completed successfully! Sold %d x "%s" for ₱%s. (Remaining stock: %d)',
            $quantity,
            esc($product['name']),
            number_format($totalPrice, 2),
            $newStock
        );

        return redirect()->to(site_url('sales/history'))->with('success', $successMessage);
    }

    private function renderFormWithErrors(array $errors): string
    {
        $productModel  = new ProductModel();
        $customerModel = new CustomerModel();

        $data = [
            'title'     => 'Record Sale | Complete POS',
            'products'  => $productModel->orderBy('name', 'ASC')->findAll(),
            'customers' => $customerModel->orderBy('full_name', 'ASC')->findAll(),
            'values'    => [
                'product_id'  => (string) $this->request->getPost('product_id'),
                'customer_id' => (string) $this->request->getPost('customer_id'),
                'quantity'    => (string) $this->request->getPost('quantity'),
            ],
            'errors'    => $errors,
        ];

        return view('templates/header', $data)
             . view('sales/form', $data)
             . view('templates/footer');
    }
}
