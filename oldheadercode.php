<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

$mail = new PHPMailer;

$mail->SMTPDebug = SMTP::DEBUG_SERVER;
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'pandey.ravi9800@gmail.com'; 
$mail->Password = 'xwca yijq kogb tbjv';
$mail->SMTPSecure = 'ssl';
$mail->Port = 465;
$mail->setFrom('pandey.ravi9800@gmail.com', 'Ravi Pandey');
$mail->addReplyTo('pandey.ravi9800@gmail.com','Ravi Pandey');
$mail->addAddress('pandey.ravi9800@gmail.com');
$mail->isHTML(true);
$mail->Subject = 'Random Subject';
$mail->Body    = 'Random Body Text YOLO Hare Krishna';
$mail->AltBody = 'Hare Krishna Hare Krishna Krishna Krishna Hare Hare Hare Ram Hare Ram Ram Ram Hare Hare';
 	if(!$mail->Send()){
 		echo $mail->ErrorInfo;
 	}else{
 		return 'Sent';
 	}

// include('smtp/PHPMailerAutoload.php');

// echo smtp_mailer('pandey.ravi9800@gmail.com', 'Test Subject', 'Mail From the ITCAO');
// function smtp_mailer($to, $subject, $msg){
//     $mail = new PHPMailer();
//     $mail -> IsSMTP();
//     $mail -> SMTPAuth = true;
//     $mail -> SMTPSecure = 'tls';
//     $mail -> Host = "smtp.gmail.com";
//     $mail -> Port = 587;
//     $mail -> isHTML(true);
// 	$mail->CharSet = 'UTF-8';
// 	//$mail->SMTPDebug = 2; 
// 	$mail->Username = "email";
// 	$mail->Password = "password";
// 	$mail->SetFrom("email");
// 	$mail->Subject = $subject;
// 	$mail->Body =$msg;
// 	$mail->AddAddress($to);
// 	$mail->SMTPOptions=array('ssl'=>array(
// 		'verify_peer'=>false,
// 		'verify_peer_name'=>false,
// 		'allow_self_signed'=>false
// 	));
// 	if(!$mail->Send()){
// 		echo $mail->ErrorInfo;
// 	}else{
// 		return 'Sent';
// 	}
// }


//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\SMTP;
// use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
// require 'vendor/autoload.php';

//Create an instance; passing `true` enables exceptions
// $mail = new PHPMailer(true);

// try {
    //Server settings
//     $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
//     $mail->isSMTP();                                            //Send using SMTP
//     $mail->Host       = 'smtp.example.com';                     //Set the SMTP server to send through
//     $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
//     $mail->Username   = 'user@example.com';                     //SMTP username
//     $mail->Password   = 'secret';                               //SMTP password
//     $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
//     $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = 

//     //Recipients
//     $mail->setFrom('from@example.com', 'Mailer');
//     $mail->addAddress('joe@example.net', 'Joe User');     //Add a recipient
//     $mail->addAddress('ellen@example.com');               //Name is optional
//     $mail->addReplyTo('info@example.com', 'Information');
//     $mail->addCC('cc@example.com');
//     $mail->addBCC('bcc@example.com');

//     //Attachments
//     $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
//     $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

//     //Content
//     $mail->isHTML(true);                                  //Set email format to HTML
//     $mail->Subject = 'Here is the subject';
//     $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
//     $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

//     $mail->send();
//     echo 'Message has been sent';
// } catch (Exception $e) {
//     echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
// }

ob_start();
session_start();
include_once 'admin/function/db_connect.php';
include_once 'admin/function/function.php';

// Check if user is logged in
if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
} else {
    $user = null;
}

// Handle user logout
if (isset($_GET['logout'])) {
    unset($_SESSION['user']);
    header("Location: index.php");
    exit;
}

// Function to update user data
function updateUser($con, $name, $contact, $email, $profilePic) {
    $update_query = "UPDATE customer SET name='$name', email='$email', photo='$profilePic' WHERE contact='$contact'";
    if (mysqli_query($con, $update_query)) {
        // Update user data in session
        $_SESSION['user']['name'] = $name;
        $_SESSION['user']['email'] = $email;
        $_SESSION['user']['profile_pic'] = $profilePic;
        echo '<script>alert("User data updated successfully.");</script>';
        header("Location: index.php");
        exit;
    } else {
        echo "Error updating user data: " . mysqli_error($con);
    }
}

// Function to login user
function loginUser($con, $contact, $randomPassword) {
    $query = "SELECT * FROM customer WHERE contact='$contact'";
    $result = mysqli_query($con, $query);
    if (mysqli_num_rows($result) == 1) {
        // User found, no need to verify password
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user'] = $user;
        header("Location: index.php");
        exit;
    } else {
        echo '<script>alert("User not found.");</script>';
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['register'])) {
        // Handle registration form submission
        $name = $_POST['name'];
        $contact = $_POST['contact'];
        $email = $_POST['email'];
        $randomPassword = randPWD();
        $profilePic = null;

        if (isset($_FILES['photo'])) {
            $targetDir = "images/";
            $targetFile = $targetDir . basename($_FILES["photo"]["name"]);
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
                $profilePic = $targetFile;
            } else {
                echo "Error uploading file.";
            }
        }
        $query = "SELECT * FROM customer WHERE contact='$contact'";
        $result = mysqli_query($con, $query);
        if (mysqli_num_rows($result) > 0) {
            echo '<script>alert("User already exists. Please sign in.");</script>';
        } else {
            $hashed_password = password_hash($randomPassword, PASSWORD_DEFAULT);
           $insert_query = "INSERT INTO customer (name, contact, email, password, photo) VALUES ('$name', '$contact', '$email', '$hashed_password', '$profilePic')";
           if (mysqli_query($con, $insert_query)) {
            // User created successfully
            // Retrieve user's email from the registration form
            $user_email = $_POST['email'];

            // Add your email sending logic here
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'pandey.ravi9800@gmail.com'; // Your Gmail email address
    $mail->Password = 'xwca yijq kogb tbjv'; // Your Gmail password
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;
    $mail->setFrom('pandey.ravi9800@gmail.com', 'Your Name');
    $mail->addAddress($user_email); // User's email address from the registration form
    $mail->isHTML(true);
    $mail->Subject = 'Registration Confirmation';
    $mail->Body    = 'Thank you for registering on our website!';

    if (!$mail->send()) {
        echo 'Message could not be sent.';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        echo 'Registration confirmation email sent successfully.';
    }
} else {
    echo "Error: " . $insert_query . "<br>" . mysqli_error($con);
}
    }
}

    } elseif (isset($_POST['update_profile'])) {
        // Handle profile update form submission
        $name = $_POST['name']; 
        $contact = $user['contact'];
        $email = $_POST['email'];
        $profilePic = null;
        if (isset($_FILES['photo'])) {
            $targetDir = "images/";
            $targetFile = $targetDir . basename($_FILES["photo"]["name"]);
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
                $profilePic = $targetFile;
            } else {
                echo "Error uploading file.";
            }
        } else {
            $profilePic = $_SESSION['user']['profile_pic'];
        }
        updateUser($con, $name, $contact, $email, $profilePic);
    } elseif (isset($_POST['login'])) {
        $contact = $_POST['contact'];
        $randomPassword = $_POST['password'];
        loginUser($con, $contact, $randomPassword);
    }

function randPWD($length = 8) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $password;
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>itaxca EASIEST WAY OF E-FILING YOUR INCOME TAX RETURN IN INDIA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700&family=Open+Sans&display=swap" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-icons.css" rel="stylesheet">
    <link href="css/templatemo-topic-listing.css" rel="stylesheet">
</head>

<body id="top">

    <main>

        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand" href="./">
                    <i class="bi bi-shield-check"></i>
                    <span>I-taxCA</span>
                </a>

                <div class="d-lg-none ms-auto me-4">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                </div>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-lg-5 me-lg-auto">
                        <li class="nav-item">
                            <a class="nav-link click-scroll" href="#section_1">Home</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link click-scroll" href="#section_3">How it works</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link click-scroll" href="#section_5">Contact</a>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarLightDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="true">Learn</a>

                            <ul class="dropdown-menu dropdown-menu-light" aria-labelledby="navbarLightDropdownMenuLink">
                                <li><a class="dropdown-item" href="#">FAQ</a></li>
                                <li><a class="dropdown-item" href="#">Tax Book</a></li>
                                <li><a class="dropdown-item" href="#">Blog</a></li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link click-scroll" href="#section_10">Become Partner</a>
                        </li>
                    </ul>
                    <div>
                        <a href="track_application.php" class="navbar-icon bi-box-arrow-in-up smoothscroll" title="Track Your Application"></a>
                    </div>&nbsp;&nbsp;&nbsp;
                    <div class="d-none d-lg-block">
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php if ($user && isset($user['profile_pic'])) : ?>
                                    <img src="<?php echo $user['profile_pic']; ?>" alt="Profile Picture" class="rounded-circle" width="40" height="40">
                                <?php else : ?>
                                    <span class="bi bi-person"></span>
                                <?php endif; ?>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="userDropdown">
                                <?php if ($user) : ?>
                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#registrationModal" onclick="populateProfileFields()">Edit Profile</a></li>
                                    <li><a class="dropdown-item" href="?logout=true">Logout</a></li>
                                <?php else : ?>
                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#registrationModal">Login / Register</a></li>
                                <?php endif; ?>
                            </div>
                        </div>
                    </ul>
                </div>
            </div>
        </nav>

  <!-- Modal -->
<div class="modal fade" id="registrationModal" tabindex="-1" aria-labelledby="registrationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registrationModalLabel">User Registration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Initially empty, will be populated by login or registration form -->
            </div>
        </div>
    </div>
</div>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>

    <script>
    <?php if ($user) : ?>
        document.getElementById('registerBtn').style.display = 'none';
        document.getElementById('updateBtn').style.display = 'block';
    <?php endif; ?>
</script>
    
    <script>
        $(document).ready(function() {
            $('#loginBtn').click(function() {
                $('#registrationModal').modal('show');
            });

            <?php if ($user && isset($user['profile_pic'])) : ?>
                $('#userDropdown > button').html('<img src="<?php echo $user['profile_pic']; ?>" alt="Profile Picture" class="rounded-circle" width="40" height="40">');
            <?php endif; ?>
        });
    </script>

        <script>
            function populateProfileFields() {
    var user = <?php echo json_encode($user); ?>;
    if (user) {
        document.getElementById('name').value = user.name;
        document.getElementById('contact').value = user.contact;
        document.getElementById('email').value = user.email;
        document.getElementById('profileImg').src = user.profile_pic;
        $('#registrationModal').modal('show');
    } }
        </script>


<script>
    $(document).ready(function () {
        showLoginForm();
    });

    function showLoginForm() {
        $('#modalBody').html(`
            <!-- Login Form -->
            <form id="loginForm" action="index.php" method="POST">
                <div class="mb-3">
                    <label for="login_contact" class="form-label">contact</label>
                    <input type="text" class="form-control" id="login_contact" name="contact" required>
                </div>
                <div class="mb-3">
                    <label for="login_password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="login_password" name="login_password" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary">Login</button>
            </form>
            <hr>
            <p class="text-center">Not Registered? <a href="#" onclick="showRegistrationForm()">Register now!</a></p>
        `);
    }

    function showRegistrationForm() {
        $('#modalBody').html(`
            <!-- Registration Form -->
            <form id="registrationForm" action="index.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="contact" class="form-label">Mobile Number</label>
                    <input type="text" maxlength="10" class="form-control" id="contact" name="contact" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="photo" class="form-label">Profile Picture</label>
                    <input type="file" class="form-control" id="photo" name="photo" onchange="readURL(this)">
                    <img id="profileImg" src="#" alt="Your image" style="display:none; width: 100px; height: 100px;"/>
                </div>
                <button type="submit" name="register" class="btn btn-primary">Register</button>
            </form>
            <hr>
            <p class="text-center">Already Registered? <a href="#" onclick="showLoginForm()">Login now!</a></p>
        `);
    }
</script>


</body>
</html>
