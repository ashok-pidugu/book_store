<?php
session_start();
require_once "./include/functions.php";
require_once "./include/db_config.php";
require_once "./include/header.php";
?>

<div class="container">
    <div class="row">
        <div class="col-lg-12 error-container">
            <h1 class="error-code">403</h1>
            <p class="error-message">Forbidden - You don't have permission to access this page.</p>
            <a href="index.php" class="btn btn-primary">Go to Home</a>
        </div>
    </div>
</div>

<?php
require_once "./include/footer.php";