<?php

class CreateDTO
{
    public static function createDTOIncome(array $data): ?array
    {
        $errors = [];

        if (!isset($data['user_id']) || $data['user_id'] === null) {
            $errors[] = "User ID is required.";
        }

        if (
            !isset($data['description']) ||
            trim($data['description']) === ''
        ) {
            $errors[] = "Description is required.";
        }

        if (
            !isset($data['amount']) ||
            $data['amount'] === null ||
            !is_numeric($data['amount']) ||
            (float) $data['amount'] <= 0
        ) {
            $errors[] = "Amount must be a valid positive number.";
        }

        if (
            !isset($data['received_at']) ||
            $data['received_at'] === null ||
            trim($data['received_at']) === ''
        ) {
            $errors[] = "Received date is required.";
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
                'user_id' => (int) $data['user_id'],
                'description' => trim($data['description']),
                'amount' => (float) $data['amount'],
                'received_at' => $data['received_at']
            ]
        ];
    }

    public static function createDTOTransaction(array $data): ?array
    {
        $errors = [];

        if (!isset($data['user_id']) || $data['user_id'] === null) {
            $errors[] = "User ID is required.";
        }

        if (
            !isset($data['description']) ||
            trim($data['description']) === ''
        ) {
            $errors[] = "Description is required.";
        }

        if (
            !isset($data['amount']) ||
            $data['amount'] === null ||
            !is_numeric($data['amount']) ||
            (float) $data['amount'] <= 0
        ) {
            $errors[] = "Amount must be a valid positive number.";
        }

        if (
            !isset($data['type']) ||
            !in_array(
                $data['type'],
                ['RECURRING', 'INSTALLMENT', 'ONE_TIME'],
                true
            )
        ) {
            $errors[] = "Invalid transaction type.";
        }

        if (
            !isset($data['due_date']) ||
            trim($data['due_date']) === ''
        ) {
            $errors[] = "Due date is required.";
        }

        if (
            isset($data['paid_at']) &&
            $data['paid_at'] !== null &&
            trim($data['paid_at']) === ''
        ) {
            $errors[] = "Paid date cannot be empty.";
        }

        if (
            isset($data['total_installments']) &&
            $data['total_installments'] !== null &&
            (
                !is_numeric($data['total_installments']) ||
                (int) $data['total_installments'] <= 0
            )
        ) {
            $errors[] = "Total installments must be a positive number.";
        }

        if (
            isset($data['paid_installments']) &&
            (
                !is_numeric($data['paid_installments']) ||
                (int) $data['paid_installments'] < 0
            )
        ) {
            $errors[] = "Paid installments cannot be negative.";
        }

        $totalInstallments = isset($data['total_installments'])
            ? (int) $data['total_installments']
            : 1;

        $paidInstallments = isset($data['paid_installments'])
            ? (int) $data['paid_installments']
            : 0;

        if ($paidInstallments > $totalInstallments) {
            $errors[] = "Paid installments cannot be greater than total installments.";
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
                'user_id' => (int) $data['user_id'],
                'description' => trim($data['description']),
                'amount' => (float) $data['amount'],
                'type' => $data['type'],
                'due_date' => $data['due_date'],
                'paid_at' => $data['paid_at'] ?? null,
                'total_installments' => $totalInstallments,
                'paid_installments' => $paidInstallments
            ]
        ];
    }
}