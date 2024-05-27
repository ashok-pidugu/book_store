<?php
session_start();
$title = "Welcome to Online Book Store";
require_once "./include/functions.php";
require_once "./include/db_config.php";
require_once "./include/header.php";

$query = "Select 
                book_id, 
                book_title, 
                book_author, 
                book_thumb, 
                book_price 
            from books 
            where book_status='1'";
$result = $db_connection->query($query);
?>

<p class="lead text-center text-muted">List of All Books</p>

<div class="container">
    <?php if ($result->num_rows <= 0): ?>
        <div class="alert alert-warning rounded-0">Books are not available at the moment. Sorry for the inconvenience.
            Please check after some time.</div>
    <?php endif; ?>
    <div class="row">
    <?php while ($book = $result->fetch_assoc()) { ?>
        <div class="book">
            <img src="./bootstrap/img/<?php echo $book['book_thumb']; ?>" alt="<?= $book['book_title'] ?>">
            <h3><?= $book['book_title'] ?></h3>
            <p>Author: <?= $book['book_author'] ?></p>
            <p>Price: <?= display_price($book['book_price']) ?></p>
            <form class='add-to-cart-form'>
                <input type="hidden" name="form_token"value="<?= form_token() ?>">
                <?php
                if (isset($_SESSION['cart'][$book["book_id"]])) { ?>
                    <p style="color:green">Added to cart</p>
                <?php } else { ?>
                    <input type='hidden' name='book_id' value='<?= $book["book_id"] ?>'>
                    <input type='submit' class="btn btn-primary add-to-cart" value='Add to Cart'>
                <?php }
                ?>

            </form>
        </div>
    <?php } ?>
</div>
</div>

<script>
    $(document).ready(function () {
        // Add to cart
        $('.add-to-cart-form').submit(function (e) {
            e.preventDefault();
            $("div.spanner").addClass("show");
            $("div.overlay").addClass("show");
            var form = $(this);
            var formData = form.serialize();
            $.ajax({
                type: 'POST',
                url: 'add_to_cart.php',
                data: formData,
                success: function (response) {
                    form.html('<p style="color:green">Added to cart</p>');
                    $("div.spanner").removeClass("show");
                    $("div.overlay").removeClass("show");
                }
            });
        });
    });
</script>

<?php
require_once "./include/footer.php";
?>