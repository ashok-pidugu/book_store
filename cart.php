<?php
session_start();
require_once "./include/functions.php";

$title = "Your shopping cart";
require_once "./include/db_config.php";
require_once "./include/header.php";

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_POST['book_qty'])) {
    verify_csrf_token($_POST['form_token']);
    foreach ($_POST['book_qty'] as $book_id => $qty) {
        if (0 == $qty) {
            unset($_SESSION['cart'][$book_id]);
        } else {
            $_SESSION['cart'][$book_id]['qty'] = $qty;
        }
    }
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
                <form action="cart.php" method="post" id="cart-form">
                    <input type="hidden" name="form_token"value="<?= form_token() ?>">
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
                                <td><input type="text" value="<?php echo $qty; ?>" size="2"
                                        name="book_qty[<?php echo $book['book_id']; ?>]"></td>
                                <td><?php echo display_price($qty * $book['book_price']); ?></td>
                            </tr>
                            <?php
                        }
                        $_SESSION['order_total'] = $total_price;
                        ?>
                        <tr>
                            <th colspan="3">&nbsp;</th>
                            <th><?= $total_qty; ?></th>
                            <th><?= display_price($total_price); ?></th>
                            <th></th>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
        <div class="card-footer text-end">
            <input type="submit" class="btn btn-primary rounded-0" name="save_change" value="Save Changes" form="cart-form">
            <a href="checkout.php" class="btn btn-dark rounded-0">Go To Checkout</a>
            <a href="index.php" class="btn btn-warning rounded-0">Continue Shopping</a>

        </div>
    </div>

    <?php
} else {
    ?>
    <div class="alert alert-warning rounded-0">Your cart is empty! Please add atleast 1 book to purchase first.</div>

    <?php
}
?>

<script>
    $(document).ready(function () {
        $('#cart-form').submit(function (e) {
            $("div.spanner").addClass("show");
            $("div.overlay").addClass("show");
        });
    });
</script>

<?php
require_once "./include/footer.php";
?>