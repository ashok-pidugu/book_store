<?php
declare(strict_types=1);

session_start();

$title = "Order placed. Thank you..";
require_once "./include/functions.php";
require_once "./include/db_config.php";
require_once "./include/header.php";

if ($_POST) {
    try {
        verify_csrf_token($_POST['form_token']);

        $name = clean_input($_POST["name"]);
        $mobile = clean_input($_POST["mobile"]);
        $address = clean_input($_POST["address"]);
        $city = clean_input($_POST["city"]);
        $state = clean_input($_POST["state"]);
        $zip_code = clean_input($_POST["zip_code"]);
        $country = clean_input($_POST["country"]);

        $validation_errors = [];
        if ("" === $name) {
            $validation_errors[] = "Name is missing";
        }
        if ("" === $mobile) {
            $validation_errors[] = "Mobile is missing";
        }
        if ("" === $address) {
            $validation_errors[] = "Address is missing";
        }
        if ("" === $city) {
            $validation_errors[] = "City is missing";
        }
        if ("" === $state) {
            $validation_errors[] = "State is missing";
        }
        if ("" === $zip_code) {
            $validation_errors[] = "Zipcode is missing";
        }
        if ("" === $country) {
            $validation_errors[] = "Country is missing";
        }
        if (!empty($validation_errors)) {
            throw new Exception(implode(",", $validation_errors));
        }

        $name = $db_connection->real_escape_string($name);
        $mobile = $db_connection->real_escape_string($mobile);
        $address = $db_connection->real_escape_string($address);
        $city = $db_connection->real_escape_string($city);
        $state = $db_connection->real_escape_string($state);
        $zip_code = $db_connection->real_escape_string($zip_code);
        $country = $db_connection->real_escape_string($country);
        $date = date("Y-m-d");

        $sql = "INSERT INTO user_address (
                    uad_usr_id, 
                    uad_is_primary, 
                    uad_mobile,
                    uad_name,
                    uad_address,
                    uad_city,
                    uad_state,
                    uad_zip,
                    uad_country
                ) VALUES (?,'Y',?,?,?,?,?,?,?)";
        $db_connection->execute_query($sql, [
            $_SESSION['user']['user_id'],
            $mobile,
            $name,
            $address,
            $city,
            $state,
            $zip_code,
            $country
        ]);
        $address_id = $db_connection->insert_id;

        $sql = "INSERT INTO orders (
                    ord_usr_id, 
                    ord_sub_total, 
                    ord_total,
                    ord_address_id,
                    ord_date
                ) VALUES (?,?,?,?,?)";
        $db_connection->execute_query($sql, [$_SESSION['user']['user_id'], $_SESSION['order_total'], $_SESSION['order_total'], $address_id, $date]);
        $order_id = $db_connection->insert_id;

        foreach ($_SESSION['cart'] as $book_id => $data) {
            $sql = "INSERT INTO order_details (
                orde_ord_id, 
                orde_book_id, 
                orde_book_price,
                orde_book_quantity,
                orde_sub_total
            ) VALUES (?,?,?,?,?)";
            $db_connection->execute_query($sql, [$order_id, $book_id, $data['unit_price'], $data['qty'], $data['price']]);
        }
        unset($_SESSION['cart']);
        unset($_SESSION['order_total']);
        header('Location:user_orders.php?order_placed=success');
    } catch (Exception $e) {
        $_SESSION['err_checkout'] = $e->getMessage();
        header('Location:checkout.php');
    }
}
?>

<div class="alert alert-success rounded-0 my-4">
    Your order has been placed sucessfully. We will be reaching you out to confirm your order. Thanks!
</div>

<?php
require_once "./include/footer.php";
?>