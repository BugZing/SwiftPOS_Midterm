<?php

namespace App\Controllers;

use App\Models\SaleModel;
use App\Models\UserModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

class Users extends BaseController
{
    public function index(): string
    {
        $userModel = new UserModel();

        $data = [
            'title' => 'Staff Accounts | Complete POS',
            'users' => $userModel
                ->select(['id', 'username', 'full_name', 'avatar', 'created_at'])
                ->orderBy('id', 'ASC')
                ->findAll(),
        ];

        return view('templates/header', $data)
             . view('users/index', $data)
             . view('templates/footer');
    }

    public function newForm(): string
    {
        return $this->form('new', null, [
            'username'  => '',
            'full_name' => '',
        ]);
    }

    public function create(): string|RedirectResponse
    {
        $values = $this->postedValues();
        $file   = $this->request->getFile('avatar');
        $upload = $file !== null && $file->getError() !== UPLOAD_ERR_NO_FILE;
        $rules  = $this->rules('is_unique[users.username]', true);

        if ($upload) {
            $rules['avatar'] = 'uploaded[avatar]|is_image[avatar]|mime_in[avatar,image/jpeg,image/png,image/webp]'
                . '|ext_in[avatar,jpg,jpeg,png,webp]|max_size[avatar,2048]|max_dims[avatar,4096,4096]';
        }

        if (! $this->validateData($values, $rules)) {
            $errors = $this->validator->getErrors();
            if ($upload && ! $file->isValid()) {
                $errors['avatar'] = $file->getErrorString();
            }
            return $this->form('new', null, $values, $errors);
        }

        $valid = $this->validator->getValidated();
        $newAvatar = null;

        if ($upload && $file->isValid() && ! $file->hasMoved()) {
            $directory = FCPATH . 'uploads/avatars';
            try {
                if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
                    throw new \RuntimeException('Avatar directory could not be created.');
                }

                $mime = $file->getMimeType();
                $extension = $mime === 'image/png' ? 'png' : ($mime === 'image/webp' ? 'webp' : 'jpg');
                $newAvatar = bin2hex(random_bytes(16)) . '.' . $extension;

                service('image')->withFile($file->getTempName())
                    ->fit(160, 160, 'center')
                    ->save($directory . '/' . $newAvatar, 85);
            } catch (\Throwable $exception) {
                if ($newAvatar !== null && is_file($directory . '/' . $newAvatar)) {
                    unlink($directory . '/' . $newAvatar);
                }
                log_message('error', 'Avatar processing failed: {message}', ['message' => $exception->getMessage()]);
                return $this->form('new', null, $values, [
                    'avatar' => 'The avatar image could not be prepared. Check the image format and try again.',
                ]);
            }
        }

        $model = new UserModel();
        $model->setValidationRule('username', 'required|max_length[50]|is_unique[users.username]');

        $insertData = [
            'username'  => $valid['username'],
            'full_name' => $valid['full_name'],
            'password'  => password_hash($valid['password'], PASSWORD_DEFAULT),
            'avatar'    => $newAvatar,
        ];

        if ($model->insert($insertData) === false) {
            if ($newAvatar !== null && is_file(FCPATH . 'uploads/avatars/' . $newAvatar)) {
                unlink(FCPATH . 'uploads/avatars/' . $newAvatar);
            }
            return $this->form('new', null, $values, $model->errors());
        }

        return redirect()->to(site_url('users'))->with('success', 'Staff account "' . esc($valid['username']) . '" created successfully.');
    }

    public function edit(int $id): string
    {
        $user = $this->findOr404($id);

        return $this->form('edit', $user, [
            'username'  => $user['username'],
            'full_name' => $user['full_name'],
        ]);
    }

    public function update(int $id): string|RedirectResponse
    {
        $user   = $this->findOr404($id);
        $values = $this->postedValues();
        $file   = $this->request->getFile('avatar');
        $upload = $file !== null && $file->getError() !== UPLOAD_ERR_NO_FILE;
        $unique = 'is_unique[users.username,id,' . $id . ']';
        $rules  = $this->rules($unique, false);

        if ($upload) {
            $rules['avatar'] = 'uploaded[avatar]|is_image[avatar]|mime_in[avatar,image/jpeg,image/png,image/webp]'
                . '|ext_in[avatar,jpg,jpeg,png,webp]|max_size[avatar,2048]|max_dims[avatar,4096,4096]';
        }

        if (! $this->validateData($values, $rules)) {
            $errors = $this->validator->getErrors();
            if ($upload && ! $file->isValid()) {
                $errors['avatar'] = $file->getErrorString();
            }
            return $this->form('edit', $user, $values, $errors);
        }

        $valid = $this->validator->getValidated();
        $newAvatar = null;
        $data = [
            'username'  => $valid['username'],
            'full_name' => $valid['full_name'],
        ];

        if ($valid['password'] !== '') {
            $data['password'] = password_hash($valid['password'], PASSWORD_DEFAULT);
        }

        if ($upload && $file->isValid() && ! $file->hasMoved()) {
            $directory = FCPATH . 'uploads/avatars';
            try {
                if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
                    throw new \RuntimeException('Avatar directory could not be created.');
                }

                $mime = $file->getMimeType();
                $extension = $mime === 'image/png' ? 'png' : ($mime === 'image/webp' ? 'webp' : 'jpg');
                $newAvatar = bin2hex(random_bytes(16)) . '.' . $extension;

                service('image')->withFile($file->getTempName())
                    ->fit(160, 160, 'center')
                    ->save($directory . '/' . $newAvatar, 85);
                $data['avatar'] = $newAvatar;
            } catch (\Throwable $exception) {
                if ($newAvatar !== null && is_file($directory . '/' . $newAvatar)) {
                    unlink($directory . '/' . $newAvatar);
                }
                log_message('error', 'Avatar processing failed: {message}', ['message' => $exception->getMessage()]);
                return $this->form('edit', $user, $values, [
                    'avatar' => 'The image could not be prepared. Check the image format and try again.',
                ]);
            }
        }

        $model = new UserModel();
        $model->setValidationRule('username', 'required|max_length[50]|' . $unique);

        if ($model->update($id, $data) === false) {
            if ($newAvatar !== null && is_file(FCPATH . 'uploads/avatars/' . $newAvatar)) {
                unlink(FCPATH . 'uploads/avatars/' . $newAvatar);
            }
            return $this->form('edit', $user, $values, $model->errors());
        }

        if ((int) session()->get('auth_user_id') === $id) {
            session()->set('auth_username', $data['username']);
        }

        $previous = $user['avatar'] ?? null;
        if ($newAvatar !== null && is_string($previous)
            && preg_match('/^[a-f0-9]{32}\.(?:jpg|png|webp)$/D', $previous)
            && is_file(FCPATH . 'uploads/avatars/' . $previous)) {
            unlink(FCPATH . 'uploads/avatars/' . $previous);
        }

        return redirect()->to(site_url('users'))->with('success', 'Staff account updated successfully.');
    }

    public function delete(int $id): RedirectResponse
    {
        $user = $this->findOr404($id);

        if ((int) session()->get('auth_user_id') === $id) {
            return redirect()->to(site_url('users'))
                ->with('error', 'Action rejected: You cannot delete your own staff account while logged in.');
        }

        $saleModel = new SaleModel();
        if ($saleModel->where('sold_by', $id)->countAllResults() > 0) {
            return redirect()->to(site_url('users'))
                ->with('error', 'Cannot delete "' . esc($user['username']) . '" because they have recorded transactions in sales history.');
        }

        $userModel = new UserModel();
        $userModel->delete($id);

        $avatar = $user['avatar'] ?? null;
        if (is_string($avatar) && preg_match('/^[a-f0-9]{32}\.(?:jpg|png|webp)$/D', $avatar)
            && is_file(FCPATH . 'uploads/avatars/' . $avatar)) {
            unlink(FCPATH . 'uploads/avatars/' . $avatar);
        }

        return redirect()->to(site_url('users'))
            ->with('success', 'Staff account "' . esc($user['username']) . '" deleted successfully.');
    }

    private function findOr404(int $id): array
    {
        $user = $id > 0 ? (new UserModel())->find($id) : null;

        if ($user === null) {
            throw PageNotFoundException::forPageNotFound('Staff account not found.');
        }

        return $user;
    }

    private function postedValues(): array
    {
        $values = [];
        foreach (['username', 'full_name', 'password'] as $field) {
            $input = $this->request->getPost($field);
            $values[$field] = is_string($input)
                ? ($field === 'password' ? $input : trim($input))
                : '';
        }
        return $values;
    }

    private function rules(string $unique, bool $creating): array
    {
        return [
            'username'  => 'required|max_length[50]|' . $unique,
            'full_name' => 'required|max_length[100]',
            'password'  => ($creating ? 'required' : 'permit_empty') . '|min_length[8]|max_length[72]',
        ];
    }

    private function form(string $mode, ?array $user, array $values, array $errors = []): string
    {
        $data = [
            'title'  => ($mode === 'new' ? 'New Staff Member' : 'Edit Staff Member') . ' | Complete POS',
            'mode'   => $mode,
            'user'   => $user,
            'values' => $values,
            'errors' => $errors,
        ];

        return view('templates/header', $data)
             . view('users/form', $data)
             . view('templates/footer');
    }
}
