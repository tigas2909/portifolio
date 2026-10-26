<?php

class MonthRange
{
    /**
     * Aceita 2026-10, 2026/10, 2026-9 e 2026/9. Sem valor, usa o mês atual.
     * Retorna [início, fim) no formato Y-m-d, ou null se o mês for inválido.
     */
    public static function from(?string $value): ?array
    {
        $value = $value === null ? '' : trim($value);

        if ($value === '') {
            $value = date('Y-m');
        }

        if (!preg_match('/^(\d{4})[\/-](\d{1,2})$/', $value, $m)) {
            return null;
        }

        $month = (int) $m[2];

        if ($month < 1 || $month > 12) {
            return null;
        }

        $start = new DateTimeImmutable(sprintf('%04d-%02d-01', (int) $m[1], $month));
        $end = $start->modify('first day of next month');

        return [$start->format('Y-m-d'), $end->format('Y-m-d')];
    }
}