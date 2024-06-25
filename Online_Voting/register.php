<?php
include 'db_Conn.php';
$error = ''; 
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
 if (isset($_POST['username'], $_POST['password'], $_POST['email'], $_POST['dob'])) {
$username = $_POST['username'];
$password =password_hash($_POST['password'], PASSWORD_BCRYPT);
$email = $_POST['email'];
$dob = $_POST['dob'];


$sql1 = "SELECT * FROM voters WHERE password='$password'";
$result1 = $conn->query($sql1);
if ($result1->num_rows>0) {
    echo "<script>
            alert('password is already taken');
            window.location.href='register.php';
          </script>";
} 


$sql = "SELECT * FROM voters WHERE email='$email'";
$result = $conn->query($sql);

if ($result->num_rows>0) {
    echo "<script>
            alert('You are already registered.');
            window.location.href='register.php';
          </script>";
} 

    $today = new DateTime();
    try {
        $dob = new DateTime($dob);
        $age = $today->diff($dob)->y;

        if ($age >= 18) {
             $dob = $dob->format('Y-m-d');
            // Insert data into database
            $stmt = $conn->prepare("INSERT INTO voters (username, password, email, dob) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $password, $email, $dob);

            if ($stmt->execute()) {
                $success = true;
            } else {
                $error = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
         else {
            $error = "You must be at least 18 years old to register.";
        }
    } 
    catch (Exception $e) {
        $error = "Invalid birthdate format.";
    }
}
    else {
        $error = "All fields are required.";
    }


    mysqli_close($conn);
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="assets/Styles/Login.css">
</head>
<body>

    <div class="container mt-3">
           <?php if (!empty($error)): ?>
    <div class="alert alert-danger ">
        <?php echo $error; ?>
    </div>
    <?php endif; ?>

        <div class="row justify-content-center">

            <div class="col-md-6 mt-1">
            <h1 class="text-center">Register</h1>
                <form id="registerForm" action="register.php" method="post">
                    <div class="form-group">
                        <label for="registerUsername">Username:</label>
                        <input type="text" class="form-control" id="registerUsername" name="username" autocomplete="user" required>
                    </div>
                    <div class="form-group">
                        <label for="registerPassword">Password:</label>
                     <input type="password" class="form-control" id="registerPassword" name="password" autocomplete="pass" onchange="validateform()" required >
                     <input type="checkbox" onclick="Showpassword()">Show Password

                        <div id="p1" class="text-danger mt-2"></div>
                    </div>
                    <div class="form-group">
                        <label for="registerEmail">Email:</label>
                        <input type="email" class="form-control" id="registerEmail" name="email" autocomplete="email" required >
                    </div>
                    <div class="form-group">
                <label for="dob">Date of Birth</label>
                <input type="date" class="form-control" id="dob" name="dob" autocomplete="dob" required>
              </div>

                    <button type="submit" class="btn btn-primary btn-block">Register</button>
                </form>
                <p class="text-center ">Already have an account? <a href="Login.php">Login here</a>.
                    <br> <a href="logout.php" class="text-center ml-5" >Go to Homepage</a></p>

            </div>
        </div>
    </div>
    <?php if ($success): ?>
    <div class="modal" tabindex="-1" role="dialog" style="display:block;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" style="font-size:30px">SUCCESS</h5>
                </div><br>
                <div class="modal-body">
                    <p style="font-size: 40px;">Registration successful</p>
                </div><br>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="window.location.href='login.php'">Go to Login page</button>
                    <button type="button" class="btn btn-primary" onclick="window.location.href='index.php'">Exit</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
<script type="text/javascript">
     function validateform(){
            let password = document.getElementById("registerPassword").value;
            let error = " ";

            if (password.length < 6) {
                error = "Password must be at least 6 characters long.";
            } 
            else if (!/[A-Z]/.test(password)) {
                error = "Password must contain at least one uppercase letter.";
            }
            else if(!/[a-z]/.test(password))
            {
                error = "Password must contain at least one lowercase letter.";
            }
            else if (!/[0-9]/.test(password)) {
                error = "Password must contain at least one number.";
            } 

           
if (error) {
    document.getElementById("p1").innerHTML = error;
    return false;
} else {
    document.getElementById("p1").innerHTML = " "; 
    return true;
}
}     
function Showpassword() {
       var ps=document.getElementById("registerPassword")
        if (ps.type === "password") {
                            ps.type = "text";
                        }
          else {
                 ps.type = "password";
               }
           }   
    </script>


  
