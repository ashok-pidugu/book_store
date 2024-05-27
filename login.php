<?php
session_start();
require_once "./include/functions.php";
require_once "./include/db_config.php";
require_once "./include/header.php";

if (isset($_SESSION["user"])) {
    header("Location:index.php");
}
if ($_POST) {
    try {
        verify_csrf_token($_POST['form_token']);
        $email = clean_input($_POST["email"]);
        $password = clean_input($_POST["password"]);
        if ("" == $email || "" == $password) {
            throw new Exception("Email and password cannot be empty");
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }
        $email = $db_connection->real_escape_string($email);
        $password = $db_connection->real_escape_string($password);
        $encrypted_password = sha1($password);

        $query = "Select 
                    usr_id as user_id,
                    usr_fname as user_first_name, 
                    usr_lname as user_last_name, 
                    concat(usr_fname, ' ', usr_lname) as full_name,
                    usr_email as user_email, 
                    usr_mobile as user_mobile,
                    usr_status as user_status
                from users 
                where usr_email = ?
                and usr_password = ?
                and usr_role = '2'";
        $result = $db_connection->execute_query($query, [$email, $encrypted_password]);
        if ($result->num_rows <= 0) {
            throw new Exception("Invalid credentials");
        } else {
            $user = $result->fetch_assoc();
            if ("Active" !== $user['user_status']) {
                throw new Exception("Your account deactivated, please contact administrator.");
            }
            $_SESSION["user"] = $user;
            if (isset($_SESSION['login_referrer'])) {
                $referrer = $_SESSION['login_referrer'];
                unset($_SESSION['login_referrer']);
                header('Location:' . $referrer . '.php');
            } else {
                header('Location:index.php');
            }
        }

    } catch (Exception $e) {
        $_SESSION['err_login'] = $e->getMessage();
    }
}
?>
<?php if (isset($_GET['register']) && 'success' === $_GET['register']) :?>
<div class="alert alert-success rounded-0 my-4">
    Congratulations! You can login with your registered email address to continue our services.
</div>
<?php endif; ?>
<div class="row justify-content-center my-5">
    <div class="col-lg-4 col-md-6 col-sm-10 col-xs-12">
        <div class="card rounded-0 shadow">
            <div class="card-header">
                <div class="card-title text-center h4 fw-bolder">Login</div>
            </div>
            <div class="card-body">
                <div class="container-fluid">
                    <?php if (isset($_SESSION['err_login'])): ?>
                        <div class="alert alert-danger rounded-0">
                            <?= $_SESSION['err_login'] ?>
                        </div>
                        <?php
                        unset($_SESSION['err_login']);
                    endif;
                    ?>
                    <form class="form-horizontal" method="post" action="login.php">
                        <input type="hidden" name="form_token"value="<?= form_token() ?>">
                        <div class="mb-3">
                            <label for="name" class="control-label ">Email</label>
                            <input type="email" name="email" class="form-control rounded-0" value="<?= $email ?? '' ?>">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="control-label ">Password</label>
                            <input type="password" name="password" class="form-control rounded-0">
                        </div>
                        <div class="mb-3 d-grid">
                            <input type="submit" name="submit" value="Login" class="btn btn-primary rounded-0">
                        </div>
                        <div class="mb-3">
                            <p>New user ? <a href="register.php">Register</a> with us.</p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once "./include/footer.php";
?>