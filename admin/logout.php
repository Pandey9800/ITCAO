<?php  ob_start(); session_start(); unset($_SESSION["admin"]); unset($_SESSION['timess']); echo "<center><b>LOGOUT SUCESSFULLY</b></center>"; header('location: ./index'); exit(); ob_end_flush(); ?>