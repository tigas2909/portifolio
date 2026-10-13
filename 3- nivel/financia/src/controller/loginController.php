<?php

require_once __DIR__ . '/../DAO/UserDao.php';
require_once __DIR__ . '/../DTOs/CreateDTO.php';
require_once __DIR__ . '/../jwt/ConfigJWT.php';

class LoginController
{
    private UserDao $userDao;

    public function __construct(PDO $conn)
    {
        $this->userDao = new UserDao($conn);
    }

    // Autenticação
    public function Login(array $dados = []): array
    {
        if (empty($dados)) {
            http_response_code(400);

            return [
                'success' => false,
                'errors' => ['Data cannot be empty.']
            ];
        }

        try {
            $data = CreateDTO::Login($dados);

            if (
                !is_array($data) ||
                !isset($data['success']) ||
                $data['success'] !== true
            ) {
                http_response_code(400);

                return [
                    'success' => false,
                    'errors' => $data['result'] ?? ['Invalid login data.']
                ];
            }

            $email = $data['result']['email'] ?? null;
            $password = $data['result']['password'] ?? null;

            if (
                !is_string($email) ||
                !is_string($password) ||
                $email === '' ||
                $password === ''
            ) {
                http_response_code(400);

                return [
                    'success' => false,
                    'errors' => ['Email and password are required.']
                ];
            }

            $user = $this->userDao->findByEmail($email);

            if (
                !is_array($user) ||
                !isset($user['id'], $user['password']) ||
                !password_verify($password, $user['password'])
            ) {
                http_response_code(401);

                return [
                    'success' => false,
                    'errors' => ['Invalid email or password.']
                ];
            }

            $token = ConfigJWT::generateToken((int) $user['id']);

            if (!is_string($token) || $token === '') {
                throw new RuntimeException('Token generation failed.');
            }

            if (!setcookie('token', $token, [
                'expires' => time() + 1800,
                'path' => '/',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Lax'
            ])) {
                throw new RuntimeException('Failed to set authentication cookie.');
            }

            http_response_code(200);

            return [
                'success' => true,
                'result' => [
                    'id' => (int) $user['id'],
                    'name' => $user['name'] ?? null,
                    'email' => $user['email'] ?? $email,
                    'password_type' => $user['password_type'] ?? null
                ]
            ];
        } catch (Throwable $e) {
            error_log('Login error: ' . $e->getMessage());

            http_response_code(500);

            return [
                'success' => false,
                'errors' => ['Internal server error during login.']
            ];
        }
    }

    // Encerramento da sessão
    public static function Logout(): array
    {
        if (!setcookie('token', '', [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Lax'
        ])) {
            http_response_code(500);

            return [
                'success' => false,
                'errors' => ['Failed to clear authentication cookie.']
            ];
        }

        unset($_COOKIE['token']);

        http_response_code(200);

        return [
            'success' => true,
            'result' => 'Logout completed.'
        ];
    }

    // Renovação do token
    public static function RefreshToken(): array
    {
        $token = $_COOKIE['token'] ?? null;

        if (!is_string($token) || $token === '') {
            http_response_code(401);

            return [
                'success' => false,
                'errors' => ['Refresh token is missing.']
            ];
        }

        try {
            if (ConfigJWT::validateRefreshToken($token) === false) {
                http_response_code(401);

                return [
                    'success' => false,
                    'errors' => ['Invalid or expired refresh token.']
                ];
            }

            $newToken = ConfigJWT::refreshToken($token);

            if (!is_string($newToken) || $newToken === '') {
                http_response_code(401);

                return [
                    'success' => false,
                    'errors' => ['Failed to refresh token.']
                ];
            }

            if (!setcookie('token', $newToken, [
                'expires' => time() + 1800,
                'path' => '/',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Lax'
            ])) {
                throw new RuntimeException('Failed to set refreshed token cookie.');
            }

            http_response_code(200);

            return [
                'success' => true,
                'result' => 'Token refreshed successfully.'
            ];
        } catch (Throwable $e) {
            error_log('Refresh token error: ' . $e->getMessage());

            http_response_code(500);

            return [
                'success' => false,
                'errors' => ['Internal server error while refreshing token.']
            ];
        }
    }

    // Cadastro de usuário
    public function CreateUser(array $dados = []): array
    {
        if (empty($dados)) {
            http_response_code(400);

            return [
                'success' => false,
                'errors' => ['Data cannot be empty.']
            ];
        }

        try {
            $data = CreateDTO::CreateUser($dados);

            if (
                !is_array($data) ||
                !isset($data['success']) ||
                $data['success'] !== true
            ) {
                http_response_code(400);

                return [
                    'success' => false,
                    'errors' => $data['result'] ?? ['Invalid user data.']
                ];
            }

            $name = $data['result']['name'] ?? null;
            $email = $data['result']['email'] ?? null;
            $password = $data['result']['password'] ?? null;

            if (
                !is_string($name) ||
                !is_string($email) ||
                !is_string($password) ||
                $name === '' ||
                $email === '' ||
                $password === ''
            ) {
                http_response_code(400);

                return [
                    'success' => false,
                    'errors' => ['Name, email and password are required.']
                ];
            }

            // O hash é gerado aqui caso ainda não tenha sido gerado.
            // Idealmente, escolha um único local para fazer esse hash.
            $passwordHash = password_get_info($password)['algo'] !== null
                ? $password
                : password_hash($password, PASSWORD_DEFAULT);

            $bool = $this->userDao->insert(
                $name,
                $email,
                $passwordHash
            );

            if ($bool === false) {
                http_response_code(500);

                return [
                    'success' => false,
                    'errors' => ['Failed to create user.']
                ];
            }

            http_response_code(201);

            return [
                'success' => true,
                'result' => ['User created successfully.']
            ];
        } catch (PDOException $e) {
            error_log('Create user database error: ' . $e->getMessage());

            if ($e->getCode() === '23000') {
                http_response_code(409);

                return [
                    'success' => false,
                    'errors' => ['A user with this email may already exist.']
                ];
            }

            http_response_code(500);

            return [
                'success' => false,
                'errors' => ['Database error while creating user.']
            ];
        } catch (Throwable $e) {
            error_log('Create user error: ' . $e->getMessage());

            http_response_code(500);

            return [
                'success' => false,
                'errors' => ['Internal server error while creating user.']
            ];
        }
    }

    // Atualização de senha
    public function UpdatePassword(string $senha): array
    {
        if (trim($senha) === '') {
            http_response_code(400);

            return [
                'success' => false,
                'errors' => ['Password cannot be empty.']
            ];
        }

        try {
            $token = $_COOKIE['token'] ?? null;

            if (!is_string($token) || $token === '') {
                http_response_code(401);

                return [
                    'success' => false,
                    'errors' => ['Authentication token is missing.']
                ];
            }

            $id = ConfigJWT::getUserId($token);

            if ($id === false) {
                http_response_code(401);

                return [
                    'success' => false,
                    'errors' => ['Invalid or expired authentication token.']
                ];
            }

            $passwordHash = password_hash($senha, PASSWORD_DEFAULT);

            if ($passwordHash === false) {
                throw new RuntimeException('Password hashing failed.');
            }

            $bool = $this->userDao->UpdatePassword($id, $passwordHash);

            if ($bool === false) {
                http_response_code(500);

                return [
                    'success' => false,
                    'errors' => ['Failed to update password.']
                ];
            }

            http_response_code(200);

            return [
                'success' => true,
                'result' => ['Password updated successfully.']
            ];
        } catch (Throwable $e) {
            error_log('Update password error: ' . $e->getMessage());

            http_response_code(500);

            return [
                'success' => false,
                'errors' => ['Internal server error while updating password.']
            ];
        }
    }
}