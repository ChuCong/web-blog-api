<?php

namespace App\Helpers;

function paginate(int $total, int|null $limit, int $page): array
{
    $page = abs($page);
    $totalPage = !empty($limit) ? ceil($total / $limit) : 0;
    $offset = !empty($limit) ? ($page - 1) * $limit : null;
    return [
        'total' => $total,
        'limit' => $limit,
        'offset' => $offset,
        'total_page' => (int)$totalPage,
        'current_page' => $page
    ];
}

function formatDate(string|null $date, string $formatCurrent = 'd/m/Y', string $format = 'Y-m-d'): string|null
{
    try {
        $result = \DateTime::createFromFormat($formatCurrent, $date)->format($format);
    } catch (\Throwable $th) {
        $result = null;
    }
    return $result;
}

function formatBetweenDate(string|null $startDate, string|null $endDate, string $formatCurrent = 'd/m/Y', string $format = 'Y-m-d'): array|null
{
    try {
        $startFormat = $format == 'Y-m-d' ? 'Y-m-d 00:00:00' : $format;
        $endFormat = $format == 'Y-m-d' ? 'Y-m-d 23:59:59' : $format;
        $start = formatDate($startDate, $formatCurrent, $startFormat);
        $end = formatDate($endDate, $formatCurrent, $endFormat);
        $result = (!is_null($start) || !is_null($end)) ? [$start, $end] : null;
    } catch (\Throwable $th) {
        $result = null;
    }
    return $result;
}

function paginateWithStart(int $total, int|null $limit, int $start): array
{
    $totalPage = !empty($limit) ? ceil($total / $limit) : 0;
    $offset = !empty($start) ? $start : 0;
    return [
        'total' => $total,
        'limit' => $limit,
        'offset' => $offset,
        'total_page' => (int)$totalPage
    ];
}


