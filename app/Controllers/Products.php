<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\SaleModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

class Products extends BaseController
{
    public function index(): string
    {
        $productModel = new ProductModel();

        $data = [
            'title'    => 'Product Management | Complete POS',
            'products' => $productModel->orderBy('id', 'ASC')->findAll(),
        ];

        return view('templates/header', $data)
             . view('products/index', $data)
             . view('templates/footer');
    }

    public function newForm(): string
    {
        return $this->form('new', null, [
            'name'           => '',
            'price'          => '',
            'stock_quantity' => '0',
        ]);
    }

    public function create(): string|RedirectResponse
    {
        $values = $this->postedValues();
        $file   = $this->request->getFile('image');
        $upload = $file !== null && $file->getError() !== UPLOAD_ERR_NO_FILE;
        $rules  = $this->rules();

        if ($upload) {
            $rules['image'] = 'uploaded[image]|is_image[image]|mime_in[image,image/jpeg,image/png,image/webp]'
                . '|ext_in[image,jpg,jpeg,png,webp]|max_size[image,3072]';
        }

        if (! $this->validateData($values, $rules)) {
            $errors = $this->validator->getErrors();
            if ($upload && ! $file->isValid()) {
                $errors['image'] = $file->getErrorString();
            }
            return $this->form('new', null, $values, $errors);
        }

        $valid = $this->validator->getValidated();
        $newImage = null;

        if ($upload && $file->isValid() && ! $file->hasMoved()) {
            $directory = FCPATH . 'uploads/products';
            try {
                if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
                    throw new \RuntimeException('Product images directory could not be created.');
                }

                $mime = $file->getMimeType();
                $extension = $mime === 'image/png' ? 'png' : ($mime === 'image/webp' ? 'webp' : 'jpg');
                $newImage = bin2hex(random_bytes(16)) . '.' . $extension;

                service('image')->withFile($file->getTempName())
                    ->fit(300, 300, 'center')
                    ->save($directory . '/' . $newImage, 85);
            } catch (\Throwable $exception) {
                if ($newImage !== null && is_file($directory . '/' . $newImage)) {
                    unlink($directory . '/' . $newImage);
                }
                log_message('error', 'Product image processing failed: {message}', ['message' => $exception->getMessage()]);
                return $this->form('new', null, $values, [
                    'image' => 'The image could not be prepared for display. Check the image format and try again.',
                ]);
            }
        }

        $productModel = new ProductModel();
        $insertData   = [
            'name'           => $valid['name'],
            'price'          => (float) $valid['price'],
            'stock_quantity' => (int) $valid['stock_quantity'],
            'image'          => $newImage,
        ];

        if ($productModel->insert($insertData) === false) {
            if ($newImage !== null && is_file(FCPATH . 'uploads/products/' . $newImage)) {
                unlink(FCPATH . 'uploads/products/' . $newImage);
            }
            return $this->form('new', null, $values, $productModel->errors());
        }

        return redirect()->to(site_url('products'))->with('success', 'Product "' . esc($valid['name']) . '" created successfully.');
    }

    public function edit(int $id): string
    {
        $product = $this->findOr404($id);

        return $this->form('edit', $product, [
            'name'           => $product['name'],
            'price'          => $product['price'],
            'stock_quantity' => $product['stock_quantity'],
        ]);
    }

    public function update(int $id): string|RedirectResponse
    {
        $product = $this->findOr404($id);
        $values  = $this->postedValues();
        $file    = $this->request->getFile('image');
        $upload  = $file !== null && $file->getError() !== UPLOAD_ERR_NO_FILE;
        $rules   = $this->rules();

        if ($upload) {
            $rules['image'] = 'uploaded[image]|is_image[image]|mime_in[image,image/jpeg,image/png,image/webp]'
                . '|ext_in[image,jpg,jpeg,png,webp]|max_size[image,3072]';
        }

        if (! $this->validateData($values, $rules)) {
            $errors = $this->validator->getErrors();
            if ($upload && ! $file->isValid()) {
                $errors['image'] = $file->getErrorString();
            }
            return $this->form('edit', $product, $values, $errors);
        }

        $valid = $this->validator->getValidated();
        $newImage = null;
        $data = [
            'name'           => $valid['name'],
            'price'          => (float) $valid['price'],
            'stock_quantity' => (int) $valid['stock_quantity'],
        ];

        if ($upload && $file->isValid() && ! $file->hasMoved()) {
            $directory = FCPATH . 'uploads/products';
            try {
                if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
                    throw new \RuntimeException('Product images directory could not be created.');
                }

                $mime = $file->getMimeType();
                $extension = $mime === 'image/png' ? 'png' : ($mime === 'image/webp' ? 'webp' : 'jpg');
                $newImage = bin2hex(random_bytes(16)) . '.' . $extension;

                service('image')->withFile($file->getTempName())
                    ->fit(300, 300, 'center')
                    ->save($directory . '/' . $newImage, 85);
                $data['image'] = $newImage;
            } catch (\Throwable $exception) {
                if ($newImage !== null && is_file($directory . '/' . $newImage)) {
                    unlink($directory . '/' . $newImage);
                }
                log_message('error', 'Product image processing failed: {message}', ['message' => $exception->getMessage()]);
                return $this->form('edit', $product, $values, [
                    'image' => 'The image could not be prepared for display. Check the image format and try again.',
                ]);
            }
        }

        $productModel = new ProductModel();

        if ($productModel->update($id, $data) === false) {
            if ($newImage !== null && is_file(FCPATH . 'uploads/products/' . $newImage)) {
                unlink(FCPATH . 'uploads/products/' . $newImage);
            }
            return $this->form('edit', $product, $values, $productModel->errors());
        }

        $previous = $product['image'] ?? null;
        if ($newImage !== null && is_string($previous)
            && preg_match('/^[a-f0-9]{32}\.(?:jpg|png|webp)$/D', $previous)
            && is_file(FCPATH . 'uploads/products/' . $previous)) {
            unlink(FCPATH . 'uploads/products/' . $previous);
        }

        return redirect()->to(site_url('products'))->with('success', 'Product updated successfully.');
    }

    public function delete(int $id): RedirectResponse
    {
        $product = $this->findOr404($id);

        $saleModel = new SaleModel();
        if ($saleModel->where('product_id', $id)->countAllResults() > 0) {
            return redirect()->to(site_url('products'))
                ->with('error', 'Cannot delete "' . esc($product['name']) . '" because it has recorded sales transactions. You can set its stock to 0 instead.');
        }

        $productModel = new ProductModel();
        $productModel->delete($id);

        $image = $product['image'] ?? null;
        if (is_string($image) && preg_match('/^[a-f0-9]{32}\.(?:jpg|png|webp)$/D', $image)
            && is_file(FCPATH . 'uploads/products/' . $image)) {
            unlink(FCPATH . 'uploads/products/' . $image);
        }

        return redirect()->to(site_url('products'))
            ->with('success', 'Product "' . esc($product['name']) . '" deleted successfully.');
    }

    private function findOr404(int $id): array
    {
        $product = $id > 0 ? (new ProductModel())->find($id) : null;

        if ($product === null) {
            throw PageNotFoundException::forPageNotFound('Product not found.');
        }

        return $product;
    }

    private function postedValues(): array
    {
        $values = [];
        foreach (['name', 'price', 'stock_quantity'] as $field) {
            $input = $this->request->getPost($field);
            $values[$field] = is_string($input) ? trim($input) : '';
        }
        return $values;
    }

    private function rules(): array
    {
        return [
            'name'           => 'required|max_length[100]',
            'price'          => 'required|decimal|greater_than_equal_to[0]',
            'stock_quantity' => 'required|integer|greater_than_equal_to[0]',
        ];
    }

    private function form(string $mode, ?array $product, array $values, array $errors = []): string
    {
        $data = [
            'title'   => ($mode === 'new' ? 'New Product' : 'Edit Product') . ' | Complete POS',
            'mode'    => $mode,
            'product' => $product,
            'values'  => $values,
            'errors'  => $errors,
        ];

        return view('templates/header', $data)
             . view('products/form', $data)
             . view('templates/footer');
    }
}
