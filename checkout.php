<?php
session_start();
$title = "Checkout";
require_once "./include/functions.php";
require_once "./include/db_config.php";
require_once "./include/header.php";

if (!isset($_SESSION["user"])) {
    $_SESSION['login_referrer'] = 'checkout';
    header("Location:login.php");
}
?>

<h4 class="fw-bolder text-center">Cart List</h4>
<center>
    <hr class="bg-warning" style="width:5em;height:3px;opacity:1">
</center>
<?php
if (isset($_SESSION['cart']) && (!empty($_SESSION['cart']))) {

    $query = "Select 
                book_id, 
                book_title, 
                book_author, 
                book_thumb, 
                book_price 
            from books 
            where book_id IN (" . implode(',', array_fill(0, count($_SESSION['cart']), '?')) . ")";
    $books = $db_connection->execute_query($query, array_keys($_SESSION['cart']));
    $cart_items = [];
    while ($book = $books->fetch_assoc()) {
        $cart_items[$book['book_id']] = $book;
    }
    ?>

    <div class="card rounded-0 shadow">
        <div class="card-body">
            <div class="container-fluid">
                <table class="table">
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                    <?php
                    $total_qty = 0;
                    $total_price = 0;
                    foreach ($_SESSION['cart'] as $id => $item) {
                        $book = $cart_items[$id];
                        $qty = $item['qty'];
                        $total_qty += $qty;
                        $total_price += $qty * $book['book_price'];
                        $_SESSION['cart'][$id]['unit_price'] = $book['book_price'];
                        $_SESSION['cart'][$id]['price'] = $qty * $book['book_price'];
                        ?>
                        <tr>
                            <td><?php echo $book['book_title'] ?></td>
                            <td><?php echo $book['book_author'] ?></td>
                            <td><?php echo display_price($book['book_price']); ?></td>
                            <td><?php echo $qty; ?></td>
                            <td><?php echo display_price($qty * $book['book_price']); ?></td>
                        </tr>
                        <?php
                    }
                    $_SESSION['order_total'] = $total_price;
                    ?>
                    <tr>
                        <th colspan="3">&nbsp;</th>
                        <th><?php echo $total_qty; ?></th>
                        <th><?php echo display_price($total_price) ?></th>
                        <th></th>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-5 mt-5 col-md-8 col-sm-10 col-xs-12">
            <div class="card rounded-0 shadow">
                <div class="card-header">
                    <div class="card-title h6 fw-bold">Please fill the shipping address</div>
                </div>
                <div class="card-body container-fluid">
                    <form method="post" action="order.php" class="form-horizontal">
                        <input type="hidden" name="form_token"value="<?= form_token() ?>">
                        <?php if (isset($_SESSION['err_checkout'])) { ?>
                            <p class="text-danger"><?= $_SESSION['err_checkout'] ?></p>
                        <?php } ?>
                        <div class="mb-3">
                            <label for="name" class="control-label">Name</label>
                            <input required pattern="[A-Za-z ]{1,32}" title="Alphabets and spaces only allowed" type="text"
                                name="name" value="<?= $_SESSION['user']['full_name'] ?>" class="form-control rounded-0">
                        </div>
                        <div class="mb-3">
                            <label for="mobile" class="control-label">Mobile</label>
                            <input required pattern="[0-9]{10}" title="Ten digit mobile number" type="text" name="mobile"
                                class="form-control rounded-0" value="<?= $_SESSION['user']['user_mobile'] ?>">
                        </div>
                        <div class="mb-3">
                            <label for="address" class="control-label">Address</label>
                            <input required type="text" name="address" class="form-control rounded-0">
                        </div>
                        <div class="mb-3">
                            <label for="city" class="control-label">City</label>
                            <input required pattern="[A-Za-z ]{1,32}" title="Alphabets and spaces only allowed" type="text"
                                name="city" class="form-control rounded-0">
                        </div>
                        <div class="mb-3">
                            <label for="state" class="control-label">State</label>
                            <input required pattern="[A-Za-z ]{1,32}" title="Alphabets and spaces only allowed" type="text"
                                name="state" class="form-control rounded-0">
                        </div>
                        <div class="mb-3">
                            <label for="zip_code" class="control-label">Zip Code</label>
                            <input required pattern="[0-9]{6}" title="Six digit zip code" type="text" name="zip_code"
                                class="form-control rounded-0">
                        </div>
                        <div class="mb-3">
                            <label for="country" class="control-label">Country</label>
                            <input required pattern="[A-Za-z ]{1,32}" title="Alphabets and spaces only allowed" type="text"
                                name="country" class="form-control rounded-0">
                        </div>
                        <div class="mb-3 d-grid">
                            <input type="submit" name="submit" value="Place Order" class="btn btn-primary rounded-0">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php
} else {
    ?>
    <div class="alert alert-warning rounded-0">Your cart is empty! Please add atleast 1 book to purchase first.</div>

    <?php
}
require_once "./include/footer.php";
?>