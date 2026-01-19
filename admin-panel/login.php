<?php
session_start(); // Start session at the very beginning

// If user is already logged in, redirect to dashboard
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Include functions
require_once '../components/functions.php'; // Adjust path as needed

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Basic validation
    if (empty($username) || empty($password)) {
        $error_message = 'Please fill in all fields';
    } else {
        // Authenticate user
        $result = authenticateUser($username, $password);

        if ($result['success']) {
            // Set session variables
            $_SESSION['user_id'] = $result['user']['id'];
            $_SESSION['username'] = $result['user']['username'];

            // Redirect to dashboard
            header("Location: index.php");
            exit();
        } else {
            $error_message = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>
  <link rel="shortcut icon" href="assets/images/favicon.png" />
  <link rel="stylesheet" href="css/login.css" />
  <style>
    .msg-box {
      max-width: 500px;
      margin: auto;
      color: red;
      background: #ebebeb;
      padding: 10px;
      margin-bottom: 10px;
      border-radius: 5px;
      display: none;
    }

    .msg-box.success {
      color: green;
    }

    .msg-box.show {
      display: block;
    }
  </style>
</head>

<body>
  <div class="login_form">
    <form action="" method="post">
      <h3>Login</h3>

      <!-- Message display -->
      <?php if (isset($error_message)): ?>
        <div class="msg-box show">
          <?php echo htmlspecialchars($error_message); ?>
        </div>
      <?php endif; ?>

      <!-- Username input -->
      <div class="input_box">
        <label for="username">Username</label>
        <input name="username" type="text" id="username" placeholder="Enter username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" />
      </div>

      <!-- Password input -->
      <div class="input_box">
        <label for="password">Password</label>
        <input name="password" type="password" id="password" placeholder="Enter password" required />
      </div>

      <!-- Login button -->
      <button type="submit" name="login">Log In</button>

      <!-- Optional: Registration link -->
      <div style="text-align: center; margin-top: 20px;">
        <p>Don't have an account? <a href="register.php">Register here</a></p>
      </div>
    </form>
  </div>
</body>

</html>