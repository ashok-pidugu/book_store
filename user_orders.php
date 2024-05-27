<?php
session_start();
$title = "My Orders";
require_once "./include/functions.php";
require_once "./include/db_config.php";
require_once "./include/header.php";

if (!isset($_SESSION["user"])) {
  header("Location:login.php");
}

$query = "Select 
                ord_id,
                ord_sub_total, 
                ord_tax, 
                ord_total, 
                ord_status, 
                ord_date 
            from orders 
            where ord_usr_id=?";
$result = $db_connection->execute_query($query, [$_SESSION['user']['user_id']]);
?>
<?php if (isset($_GET['order_placed']) && 'success' === $_GET['order_placed']): ?>
  <div class="alert alert-success rounded-0 my-4">
    Your order has been placed sucessfully. We will be reaching you out to confirm your order. Thanks!
  </div>
<?php endif; ?>
<p class="lead text-center text-muted">List of orders</p>

<div class="container">
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Order ID</th>
        <th>Sub total</th>
        <th>Other charges</th>
        <th>Net total</th>
        <th>Status</th>
        <th>Order Date</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result->num_rows <= 0): ?>
        <tr>
          <td colspan="6">No orders available. Start shopping now</td>
        </tr>
      <?php endif; ?>
      <?php while ($order = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $order['ord_id'] ?></td>
          <td><?= display_price($order['ord_sub_total']) ?></td>
          <td><?= display_price($order['ord_tax']) ?></td>
          <td><?= display_price($order['ord_total']) ?></td>
          <td><?= display_status($order['ord_status']) ?></td>
          <td><?= display_date($order['ord_date']) ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<?php
require_once "./include/footer.php";
?>