<?php
session_start();
include 'db_Conn.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
     

    $stmt = $conn->prepare("SELECT id, username, password,has_voted FROM voters WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $username, $hashed_password, $has_voted);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
             if ($has_voted) {
                $error1= "You have already cast your vote.";
            } 
            else
            {
            $_SESSION['voter_id'] = $id; // Use 'voter_id' as the session key
            $_SESSION['username'] = $username;
            $_SESSION['has_voted'] = $has_voted;
            header("Location: ballot.php");
            exit();
        }
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }

    $stmt->close();
    mysqli_close($conn);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Online Voting System</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/Styles/Login.css">
</head>
<body>
 
   <nav class="navbar navbar-expand-lg navbar-light ">
        <div class="container mt-3">
            <a class="navbar-brand mr-5" href="#">
              <div class="row justify-content-center"><b class="d1">Online Voting System</b></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse mt-2" id="navbarNav">
                <ul class="navbar-nav ">

                <li class="nav-item">
                        <a class="nav-link ml-5 " href="register.php">Register</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link ml-2 mr-2" href="logout.php">Signout</a>
                       
                    </li>

                </ul>
              </div>
            </div>
       </div>
    
</nav>
 <div class="container  pb-5 pt-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h1 class="text-center">Login</h1>
                <form id="loginForm" action="Login.php" method="post">
                    <div class="form-group">
                        <label for="loginUsername">Username:</label>
                        <input type="text" class="form-control" id="loginUsername" name="username" autocomplete="user" required>
                    </div>
                    <div class="form-group">
                        <label for="loginPassword">Password:</label>
                        <input type="password" class="form-control" id="loginPassword" name="password" autocomplete="pass" required>
                        <!-- <a href="forgot_password.php">Forgot password?</a> -->
                    </div>
                    
                    <a href=""><button type="submit" class="btn btn-primary btn-block" id="okButton">Login</button></a>
                </form>
                <p class="text-center mt-4">Don't have an account? <a href="register.php">Register here</a>.</p>
</div>
        </div>
    </div>
    <?php if (!empty($error)): ?>
      <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="errorModalLabel">Login Error</h5>
        </div>
        <div class="modal-body text-center">
          <p id="errorMessage" style="font-size: 20px;">Invalid credentials.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="registerButton" onclick="window.location.href='register.php'">Register</button>
          <button type="button" class="btn btn-primary" id="registerButton" onclick="window.location.href='Login.php'">Try Again</button>
        </div>
      </div>
    </div>
  </div>
   <?php endif; ?>

    <div class="modal" id="errorModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      
                    </button>
                </div>
                <div class="modal-body">
                    <p id="errorMessage" style="font-size:33px"><?php echo $error1; ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Exit</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="js/scripts.js"></script>
</body>
</html>
<script>
    
    $(document).ready(function() {
        
        if ("<?php echo !empty($error); ?>") {
            $('.modal').modal('show');
        }
    });


     $(document).ready(function() {
            var error = "<?php echo $error1; ?>";
            if (error) {
                $('#errorModal').modal('show');
            }
        });
</script>

     
