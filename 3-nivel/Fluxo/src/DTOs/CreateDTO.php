<?php

require_once __DIR__ . '/../jwt/ConfigJWT.php';

class CreateDTO
{
    private static function isValidDate(mixed $date): bool
    {
        if (!is_string($date)) {
            return false;
        }

        $parsedDate = DateTimeImmutable::createFromFormat('!Y-m-d', $date);

        return $parsedDate !== false
            && $parsedDate->format('Y-m-d') === $date;
    }

    private static function getAuthenticatedUserId(): int|false
    {
        $token = $_COOKIE['token'] ?? null;

        if (!is_string($token) || $token === '') {
            return false;
        }

        return ConfigJWT::getUserId($token);
    }

    private static function isValidPositiveInteger(mixed $value): bool
    {
        return (
            is_int($value)
            || (is_string($value) && ctype_digit($value))
        ) && (int) $value > 0;
    }

    private static function isValidNonNegativeInteger(mixed $value): bool
    {
        return (
            is_int($value)
            || (is_string($value) && ctype_digit($value))
        ) && (int) $value >= 0;
    }

    // =========================
    // GANHOS
    // =========================

    public static function IncomeCreate(array $data): array
    {
        $errors = [];

        $userId = self::getAuthenticatedUserId();

        if ($userId === false) {
            $errors[] = 'Authentication is required or the token is invalid.';
        }

        if (
            !isset($data['description'])
            || !is_string($data['description'])
            || trim($data['description']) === ''
        ) {
            $errors[] = 'Description is required.';
        }

        if (
            !isset($data['amount'])
            || !is_numeric($data['amount'])
            || !is_finite((float) $data['amount'])
            || (float) $data['amount'] <= 0
        ) {
            $errors[] = 'Amount must be a valid positive number.';
        }

        if (
            !isset($data['received_at'])
            || !self::isValidDate($data['received_at'])
        ) {
            $errors[] = 'Received date must be a valid date in YYYY-MM-DD format.';
        }

        if (!empty($errors)) {
            return [
                'success' => false,
                'result' => $errors
            ];
        }

        return [
            'success' => true,
            'result' => [
                'user_id' => $userId,
                'description' => trim($data['description']),
                'amount' => (float) $data['amount'],
                'received_at' => $data['received_at']
            ]
        ];
    }

    // =========================
    // TRANSAÇÕES
    // =========================

    public static function TransactionCreate(array $data): array
    {
        $errors = [];

        $userId = self::getAuthenticatedUserId();

        if ($userId === false) {
            $errors[] = 'Authentication is required or the token is invalid.';
        }

        if (
            !isset($data['description'])
            || !is_string($data['description'])
            || trim($data['description']) === ''
        ) {
            $errors[] = 'Description is required.';
        }

        if (
            !isset($data['amount'])
            || !is_numeric($data['amount'])
            || !is_finite((float) $data['amount'])
            || (float) $data['amount'] <= 0
        ) {
            $errors[] = 'Amount must be a valid positive number.';
        }

        $validTypes = ['RECURRING', 'INSTALLMENT', 'ONE_TIME'];

        if (
            !isset($data['type'])
            || !is_string($data['type'])
            || !in_array($data['type'], $validTypes, true)
        ) {
            $errors[] = 'Invalid transaction type.';
        }

        if (
            !isset($data['due_date'])
            || !self::isValidDate($data['due_date'])
        ) {
            $errors[] = 'Due date must be a valid date in YYYY-MM-DD format.';
        }

        $paidAt = $data['paid_at'] ?? null;

        if ($paidAt !== null && !self::isValidDate($paidAt)) {
            $errors[] = 'Paid date must be null or a valid date in YYYY-MM-DD format.';
        }

        $totalInstallments = $data['total_installments'] ?? 1;
        $paidInstallments = $data['paid_installments'] ?? 0;

        if (!self::isValidPositiveInteger($totalInstallments)) {
            $errors[] = 'Total installments must be a positive integer.';
        }

        if (!self::isValidNonNegativeInteger($paidInstallments)) {
            $errors[] = 'Paid installments must be a non-negative integer.';
        }

        if (
            self::isValidPositiveInteger($totalInstallments)
            && self::isValidNonNegativeInteger($paidInstallments)
            && (int) $paidInstallments > (int) $totalInstallments
        ) {
            $errors[] = 'Paid installments cannot be greater than total installments.';
        }

        if (!empty($errors)) {
            return [
                'success' => false,
                'result' => $errors
            ];
        }

        return [
            'success' => true,
            'result' => [
                'user_id' => $userId,
                'description' => trim($data['description']),
                'amount' => (float) $data['amount'],
                'type' => $data['type'],
                'due_date' => $data['due_date'],
                'paid_at' => $paidAt,
                'total_installments' => (int) $totalInstallments,
                'paid_installments' => (int) $paidInstallments
            ]
        ];
    }

    // =========================
    // USUÁRIOS
    // =========================

    public static function CreateUser(array $data): array
    {
        $errors = [];

        if (
            !isset($data['name'])
            || !is_string($data['name'])
            || trim($data['name']) === ''
        ) {
            $errors[] = 'Name is required.';
        }

        if (
            !isset($data['email'])
            || !is_string($data['email'])
            || trim($data['email']) === ''
        ) {
            $errors[] = 'Email is required.';
        } elseif (!filter_var(trim($data['email']), FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email must be valid.';
        }

        if (
            !isset($data['password'])
            || !is_string($data['password'])
            || $data['password'] === ''
        ) {
            $errors[] = 'Password is required.';
        }

        if (!empty($errors)) {
            return [
                'success' => false,
                'result' => $errors
            ];
        }

        return [
            'success' => true,
            'result' => [
                'name' => trim($data['name']),
                'email' => trim($data['email']),
                'password' => $data['password']
            ]
        ];
    }
}