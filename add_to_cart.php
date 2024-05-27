<?php
session_start();

require_once "./include/functions.php";

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
    $_SESSION['order_total'] = 0;
}

if (isset($_POST['book_id'])) {
    verify_csrf_token($_POST['form_token']);
    if (!isset($_SESSION['cart'][$_POST['book_id']])) {
        $_SESSION['cart'][$_POST['book_id']]['qty'] = 1;
    } else {
        $_SESSION['cart'][$_POST['book_id']]['qty']++;
    }
}
