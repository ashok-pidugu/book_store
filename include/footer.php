<hr>
<footer class="fixed-bottom bg-light bg-gradient border py-3 px-2">
    <div class="container">
        <div class="d-flex justify-content-between">
            <div class="">
                <a href="index.php" target="_blank" class="text-decoration-none text-muted fw-bold"> Online Book Store
                    &copy; <?= date('Y') ?> </a>
            </div>
        </div>
    </div>
</footer>
<div class="clear-fix py-4"></div>
</div>

</body>

</html>
<?php
if (isset($db_connection)) {
    $db_connection->close();
}
?>