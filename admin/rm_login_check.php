<?php
define(DOC_ROOT,dirname(__FILE__)); // To properly get the config.php file
$username = $_POST['username']; //Set UserName
$password = $_POST['password']; //Set Password
$msg ='';
if(isset($username, $password)) {
    ob_start();
    include(DOC_ROOT.'/function/db_connect.php.php'); //Initiate the MySQL connection
    // To protect MySQL injection (more detail about MySQL injection)
    $myusername = stripslashes($username);
    $mypassword = stripslashes($password);
    $myusername = mysqli_real_escape_string($dbC, $myusername);
    $mypassword = mysqli_real_escape_string($dbC, $mypassword);
    $sql="SELECT * FROM ecounter WHERE 	Email='$myusername' and Password='$mypassword'";
    $result=mysqli_query($con, $sql);
    // Mysql_num_row is counting table row
    $count=mysqli_num_rows($result);
    // If result matched $myusername and $mypassword, table row must be 1 row
    if($count==1){
        // Register $myusername, $mypassword and redirect to file "admin.php"
        session_register("admin");
        session_register("password");
        $_SESSION['name']= $myusername;
        header("location:../userpanel/dashboard.php.php");
    }
    else {
        $msg = "Wrong Username or Password. Please retry";
        header("location:rm_login_check.php?msg=$msg");
    }
    ob_end_flush();
}
else {
    header("location:rm_login_check.php?msg=Please enter some username and password");
}
?>