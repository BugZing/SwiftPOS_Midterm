<?php

namespace App\Controllers;

use App\Models\CustomerModel;
use App\Models\SaleModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

class Customers extends BaseController
{
    public function index(): string
    {
        $customerModel = new CustomerModel();

        $data = [
            'title'     => 'Customer Accounts | Complete POS',
            'customers' => $customerModel
                ->select(['id', 'full_name', 'email', 'phone', 'created_at'])
                ->orderBy('id', 'ASC')
                ->findAll(),
        ];

        return view('templates/header', $data)
             . view('customers/index', $data)
             . view('templates/footer');
    }

    public function newForm(): string
    {
        return $this->form('new', null, [
            'full_name' => '',
            'email'     => '',
            'phone'     => '',
        ]);
    }

    public function create(): string|RedirectResponse
    {
        $values = $this->postedValues();
        $rules  = $this->rules('is_unique[customers.email]');

        if (! $this->validateData($values, $rules)) {
            return $this->form('new', null, $values, $this->validator->getErrors());
        }

        $valid = $this->validator->getValidated();
        $model = new CustomerModel();
        $model->setValidationRule('email', 'required|valid_email|max_length[100]|is_unique[customers.email]');

        if ($model->insert([
            'full_name' => $valid['full_name'],
            'email'     => $valid['email'],
            'phone'     => $valid['phone'] === '' ? null : $valid['phone'],
        ]) === false) {
            return $this->form('new', null, $values, $model->errors());
        }

        return redirect()->to(site_url('customers'))->with('success', 'Customer "' . esc($valid['full_name']) . '" created successfully.');
    }

    public function edit(int $id): string
    {
        $customer = $this->findOr404($id);

        return $this->form('edit', $customer, [
            'full_name' => $customer['full_name'],
            'email'     => $customer['email'],
            'phone'     => $customer['phone'] ?? '',
        ]);
    }

    public function update(int $id): string|RedirectResponse
    {
        $customer = $this->findOr404($id);
        $values   = $this->postedValues();
        $unique   = 'is_unique[customers.email,id,' . $id . ']';

        if (! $this->validateData($values, $this->rules($unique))) {
            return $this->form('edit', $customer, $values, $this->validator->getErrors());
        }

        $valid = $this->validator->getValidated();
        $data  = [
            'full_name' => $valid['full_name'],
            'email'     => $valid['email'],
            'phone'     => $valid['phone'] === '' ? null : $valid['phone'],
        ];

        $model = new CustomerModel();
        $model->setValidationRule('email', 'required|valid_email|max_length[100]|' . $unique);

        if ($model->update($id, $data) === false) {
            return $this->form('edit', $customer, $values, $model->errors());
        }

        return redirect()->to(site_url('customers'))->with('success', 'Customer updated successfully.');
    }

    public function delete(int $id): RedirectResponse
    {
        $customer = $this->findOr404($id);

        $saleModel = new SaleModel();
        $saleModel->where('customer_id', $id)->set(['customer_id' => null])->update();

        $customerModel = new CustomerModel();
        $customerModel->delete($id);

        return redirect()->to(site_url('customers'))
            ->with('success', 'Customer "' . esc($customer['full_name']) . '" deleted successfully.');
    }

    private function findOr404(int $id): array
    {
        $customer = $id > 0 ? (new CustomerModel())->find($id) : null;

        if ($customer === null) {
            throw PageNotFoundException::forPageNotFound('Customer not found.');
        }

        return $customer;
    }

    private function postedValues(): array
    {
        $values = [];
        foreach (['full_name', 'email', 'phone'] as $field) {
            $input = $this->request->getPost($field);
            $values[$field] = is_string($input) ? trim($input) : '';
        }
        return $values;
    }

    private function rules(string $unique): array
    {
        return [
            'full_name' => 'required|max_length[100]',
            'email'     => 'required|valid_email|max_length[100]|' . $unique,
            'phone'     => 'permit_empty|max_length[20]',
        ];
    }

    private function form(string $mode, ?array $customer, array $values, array $errors = []): string
    {
        $data = [
            'title'    => ($mode === 'new' ? 'New Customer' : 'Edit Customer') . ' | Complete POS',
            'mode'     => $mode,
            'customer' => $customer,
            'values'   => $values,
            'errors'   => $errors,
        ];

        return view('templates/header', $data)
             . view('customers/form', $data)
             . view('templates/footer');
    }
}
