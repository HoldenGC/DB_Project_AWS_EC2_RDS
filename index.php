<!DOCTYPE html>
<html lang="en">

<?php

// Initialize session
session_start();


if( isset($_SESSION["loggedin"])) {
    if ($_SESSION["loggedin"] == "A") {
        header("location: admin/dashboard.php");
    } else { header("location: Welcome.html") ; }
    exit;
}


$email = $role = $password = $email_err = $password_err = "";

if($_SERVER["REQUEST_METHOD"]=="POST"){  // Called with data to check if it is a valid login

    require_once "admin/DB_Conn.php";


    // Check for empty user name field
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter an email.";
    } else {
        $email = (trim($_POST["email"]));
    }
    // Check for empty password field
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";
    } else {
        $password = (trim($_POST["password"]));
    }

    if(empty($email_err) && empty($password_err)){
        $sql = "SELECT FName, LName, Passwd, User_Role FROM USERS WHERE Email = ?";
        if($stmt = $mysqli->prepare($sql)){
            $stmt->bind_param("s",$param_email);
            $param_email = $email;
            if($stmt->execute()){
                $stmt->store_result();
                if($stmt->num_rows == 1){
                    $stmt->bind_result($FName, $LName, $hashed_password, $role);
                    if($stmt->fetch()){
                        if(password_verify($password , $hashed_password)){
                            session_start();
                            $_SESSION["loggedin"] = $role;
                            $_SESSION["FName"] = $FName;
                            $_SESSION["LName"] = $LName;
                            $_SESSION["email"] = $email;
                            $sql = "UPDATE USERS SET LastLogin = now() WHERE Email = '".$email."'";
                            $mysqli->query($sql);
                            if($role == "A") {
                                header("location: admin/dashboard.php");
                            } else header("location: Welcome.html");

                        } else $password_err = "Invalid Password";
                    }
                } else $email_err = "No Account Found With That Email";
            } echo "OOPS! Something Went Wrong...Try Again Later.";
        }
        $stmt->close();
    }
    $mysqli->close();
}

?>

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>MakerFun Login</title>

    <!-- Custom fonts for this template-->
    <link href="admin/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="admin/css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body class="bg-gradient-primary">

<div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center">

        <div class="col-xl-10 col-lg-12 col-md-9">

            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <!-- Nested Row within Card Body -->
                    <div class="row">
                        <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                        <div class="col-lg-6">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                                </div>

                                <form action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?> " class="user" method="post">
                                    <div class="form-group">
                                        <input type="email" class="form-control form-control-user" name="email" aria-describedby="emailHelp" placeholder="Enter Email Address...">
                                    </div>
                                    <div class="form-group">
                                        <input type="password" class="form-control form-control-user" name="password" placeholder="Password">
                                    </div>
                                    <div class="form-group">

                                        <div class="custom-control custom-checkbox small">
                                            <input type="checkbox" class="custom-control-input" id="customCheck">
                                            <label class="custom-control-label" for="customCheck">Remember Me</label>
                                        </div>
                                    </div>
                                    <input type="submit"  class="btn btn-primary btn-user btn-block" value="Login">
                                    <hr>
                                    <a href=# class="btn btn-google btn-user btn-block">
                                        <i class="fab fa-google fa-fw"></i> Login with Google
                                    </a>
                                    <a href=# class="btn btn-facebook btn-user btn-block">
                                        <i class="fab fa-facebook-f fa-fw"></i> Login with Facebook
                                    </a>
                                </form>
                                <hr>
                                <div class="text-center">
                                    <a class="small" href="admin/xforgot-password.html">Forgot Password?</a>
                                </div>
                                <div class="text-center">
                                    <a class="small" href="admin/register.php">Create an Account!</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

<!-- Bootstrap core JavaScript-->
<script src="admin/vendor/jquery/jquery.min.js"></script>
<script src="admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="admin/vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="admin/js/sb-admin-2.min.js"></script>

</body>

</html>
