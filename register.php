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
        
        $fname = clean_input($_POST["fname"]);
        $lname = clean_input($_POST["lname"]);
        $mobile = clean_input($_POST["mobile"]);
        $email = clean_input($_POST["email"]);
        $password = clean_input($_POST["password"]);
        $confirm_password = clean_input($_POST["confirm_password"]);
        if ("" == $email || "" == $password) {
            throw new Exception("Email and password cannot be empty");
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }
        if ($password != $confirm_password) {
            throw new Exception("Passwords did not match");
        }
        if (!isset($_POST['terms'])) {
            throw new Exception('Please accept the terms and conditions.');
        }
        $fname = $db_connection->real_escape_string($fname);
        $lname = $db_connection->real_escape_string($lname);
        $mobile = $db_connection->real_escape_string($mobile);
        $email = $db_connection->real_escape_string($email);
        $password = $db_connection->real_escape_string($password);
        $encrypted_password = sha1($password);

        $query = "Select 
                    usr_id as user_id
                from users 
                where usr_email = ?";
        $result = $db_connection->execute_query($query, [$email]);
        if ($result->num_rows <= 0) {
            $sql = "INSERT INTO users (
                usr_fname, 
                usr_lname, 
                usr_email,
                usr_password,
                usr_mobile,
                usr_role,
                usr_status
            ) VALUES (?,?,?,?,?,'2','Active')";
            $db_connection->execute_query($sql, [
                $fname,
                $lname,
                $email,
                $encrypted_password,
                $mobile
            ]);
            header("Location:login.php?register=success");
        } else {
            throw new Exception("We have an account already registered with this email");
        }

    } catch (Exception $e) {
        $_SESSION['err_register'] = $e->getMessage();
    }
}
?>

<div class="row justify-content-center my-5">
    <div class="col-lg-4 col-md-6 col-sm-10 col-xs-12">
        <div class="card rounded-0 shadow">
            <div class="card-header">
                <div class="card-title text-center h4 fw-bolder">Register</div>
            </div>
            <div class="card-body">
                <div class="container-fluid">
                    <?php if (isset($_SESSION['err_register'])): ?>
                        <div class="alert alert-danger rounded-0">
                            <?= $_SESSION['err_register'] ?>
                        </div>
                        <?php
                        unset($_SESSION['err_register']);
                    endif;
                    ?>
                    <form class="form-horizontal" method="post" action="register.php">
                        <input type="hidden" name="form_token"value="<?= form_token() ?>">
                        <div class="mb-3">
                            <label for="fname" class="control-label ">First Name</label>
                            <input required pattern="[A-Za-z ]{1,32}" title="Alphabets and spaces only allowed"
                                type="text" name="fname" class="form-control rounded-0" value="<?= $fname ?? '' ?>">
                        </div>
                        <div class="mb-3">
                            <label for="lname" class="control-label ">Last Name</label>
                            <input required pattern="[A-Za-z ]{1,32}" title="Alphabets and spaces only allowed"
                                type="text" name="lname" class="form-control rounded-0" value="<?= $lname ?? '' ?>">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="control-label ">Email</label>
                            <input type="email" name="email" class="form-control rounded-0" value="<?= $email ?? '' ?>">
                        </div>
                        <div class="mb-3">
                            <label for="mobile" class="control-label ">Mobile</label>
                            <input required pattern="[0-9]{10}" title="Ten digit mobile number" type="text"
                                name="mobile" class="form-control rounded-0" value="<?= $mobile ?? '' ?>">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="control-label ">Password</label>
                            <input type="password" name="password" class="form-control rounded-0">
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="control-label ">Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control rounded-0">
                        </div>
                        <div class="mb-3 d-grid">
                            <input type="submit" name="submit" value="Register" class="btn btn-primary rounded-0">
                        </div>
                        <div class="mb-3">
                            <input name="terms" id="checkbox" type="checkbox" />
                            <label for="terms"> I agree to the terms and conditions.</label>
                        </div>
                        <div class="mb-3">
                            <p><a href="login.php">Login</a> if you already have an account with us.</p>
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