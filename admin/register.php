<!DOCTYPE html>
<html lang="en">


<?php

// Initialize session
session_start();

if( isset($_SESSION["loggedin"])) {
    if ($_SESSION["loggedin"] == "A") {
        header("location: dashboard.php");
    } else { header("location: /Welcome.html") ; }
    exit;
}


$email = $password = $email_err = $password_err = "";
$confirm_password = $confirm_password_err = "";

if($_SERVER["REQUEST_METHOD"]=="POST") {  // Called with data to check if it is a valid login
    require_once "DB_Conn.php";

    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email.";
    } else {
        $sql = "SELECT Email FROM USERS WHERE Email = ?";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("s", $param_email);
            $param_email = (trim($_POST["email"]));
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows == 1) {
                    $email_err = "Email Address Already in Use";
                } else {
                    $email = (trim($_POST["email"]));
                }
            } else echo "OOPS! Something Went Wrong.";
        }
        $stmt->close();
    }

    if (empty(trim($_POST["password"]))) {
        $password_err = "Please Enter a Password";
    } else $password = (trim($_POST["password"]));

    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please Confirm Password";
    } else {
        $confirm_password = (trim($_POST["confirm_password"]));
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Passwords Did Not Match";
        }
    }

    if(empty($email_err) && empty($password_err) && empty($Confirm_password_err)) {
        $sql = "INSERT INTO USERS (CustId, FName, LName, Email, Passwd) values (0,?,?,?,?)";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("ssss", $param_FName,$param_LName, $param_email, $param_password);
            $param_FName = (trim($_POST["FName"]));
            $param_LName = (trim($_POST["LName"]));
            $param_email = ($email);
            $param_password = password_hash($password,PASSWORD_DEFAULT);  // Currently BCRYPT returning 60 char
            if ($stmt->execute()) {
                session_start();
                $_SESSION["loggedin"] = "U";
                $_SESSION["FName"] = $param_FName;
                $_SESSION["LName"] = $param_LName;
                $_SESSION["email"] = $param_email;
                header("location: ../Welcome.html");


                //header("location: dashboard.php");


            } else echo "OOPS! Something Went Wrong.";
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

    <title>MakerFun Register</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body class="bg-gradient-primary">

<div class="container">

    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
                <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>
                <div class="col-lg-7">
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Create an Account!</h1>
                        </div>
                        <! // Use special character replacement to guard against SQL injection >
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="user" method="post">
                            <div class="form-group row">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <input type="text" name="FName" class="form-control form-control-user" id="exampleFirstName" placeholder="First Name">
                                </div>
                                <div class="col-sm-6">
                                    <input type="text" name="LName" class="form-control form-control-user" id="exampleLastName" placeholder="Last Name">
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="email" name="email"  class="form-control form-control-user" id="exampleInputEmail" placeholder="Email Address">
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <input type="password" name="password" class="form-control form-control-user" id="exampleInputPassword" placeholder="Password">
                                </div>
                                <div class="col-sm-6">
                                    <input type="password" name="confirm_password" class="form-control form-control-user" id="exampleRepeatPassword" placeholder="Repeat Password">
                                </div>
                            </div>
                            <input type="submit" class="btn btn-primary btn-user btn-block" value="Register Account">


                            <hr>
                            <a href="#" class="btn btn-google btn-user btn-block">
                                <i class="fab fa-google fa-fw"></i> Register with Google
                            </a>
                            <a href="#" class="btn btn-facebook btn-user btn-block">
                                <i class="fab fa-facebook-f fa-fw"></i> Register with Facebook
                            </a>
                        </form>
                        <hr>
                        <div class="text-center">
                            <a class="small" href="xforgot-password.html">Forgot Password?</a>
                        </div>
                        <div class="text-center">
                            <a class="small" href="/login.php">Already have an account? Login!</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Bootstrap core JavaScript-->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="js/sb-admin-2.min.js"></script>

</body>

</html>
