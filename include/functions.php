<?php

declare(strict_types=1);

init_csrf_token();

function clean_input(mixed $data): mixed
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function init_csrf_token(): void
{
    if (!isset($_SESSION['csrf_token']) || empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    $token = $_SESSION['csrf_token'];
}

function form_token()
{
    return $_SESSION['csrf_token'];
}

function verify_csrf_token(string $token): ?bool
{
    if (!empty($token) && hash_equals($_SESSION['csrf_token'], $token)) {
        return true;
    } else {
        header('Location:forbidden.php');
    }
}

function display_price(int|float $data): string
{
    $data = number_format($data, 2, ".", ",");
    return "$" . $data;
}

function display_status(string $data): string
{
    switch ($data) {
        case 'P':
            $status = 'Pending';
            break;
        case 'C':
            $status = 'Cancelled';
            break;
        case 'S':
            $status = 'Shipped';
            break;
        case 'D':
            $status = 'Delivered';
            break;
        default:
            $status = 'Pending';
            break;
    }
    return $status;
}

function display_date(string $date): string
{
    return date("F jS, Y", strtotime($date));
}