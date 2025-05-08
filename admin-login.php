<?php
session_start();

if(isset($_SESSION['admin'])) {
    header('Location: admin.php');
    exit();
}

require_once 'db_connection/db.php';
$db = Database::getInstance();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $stmt = $conn->prepare("SELECT adminID, name, email, password FROM admin WHERE email = :email LIMIT 1");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($password === $admin['password']) {
            $_SESSION['admin'] = 'admin';
            $_SESSION['admin_id'] = $admin['adminID'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['admin_email'] = $admin['email']; 
            
            header('Location: admin.php');
            exit();
        } else {
          echo '<script>
              alert("Invalid email or password. Please try again.");
              window.location.href = "admin-login.php";
          </script>';
          exit();
        }
    } else {
      echo '<script>
          alert("Invalid email or password. Please try again.");
          window.location.href = "admin-login.php";
      </script>';
      exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Login</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    :root {
      --primary: #7272CE;
      --secondary: #ADC9B8;
      --white: #f4fff8;
      --black: #171d1c;
      --danger: #dc3545;
    }

    * {
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
      margin: 0;
      padding: 0;
    }

    body {
      background-color: var(--white);
      color: var(--black);
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .screen {
      background-color: var(--secondary);
      padding: 3em 2em 4em;
      border-radius: 20px;
      width: 100%;
      max-width: 450px;
      min-height: 550px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .logo {
      width: 100px;
      height: 100px;
      background-color: var(--primary);
      border-radius: 50%;
      display: flex;
      justify-content: center;
      align-items: center;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      margin-bottom: 2em;
    }

    .logo i {
      color: white;
      font-size: 2rem;
    }

    .input-group {
      width: 100%;
      margin-bottom: 2em;
    }

    .input-group label {
      display: block;
      margin-bottom: 0.5em;
      font-weight: 500;
    }

    .input-group input {
      width: 100%;
      padding: 0.85em;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 1em;
    }

    button.login {
      width: 100%;
      padding: 1em;
      background-color: var(--primary);
      color: white;
      border: none;
      border-radius: 30px;
      font-weight: 600;
      cursor: pointer;
    }

    button.login:hover {
      background-color: var(--black);
    }
    
    .alert-error {
      width: 100%;
      padding: 0.75rem;
      margin-bottom: 1rem;
      background-color: var(--danger);
      color: white;
      border-radius: 8px;
      text-align: center;
    }
    
    .admin-title {
      color: var(--primary);
      font-weight: bold;
      margin-bottom: 1rem;
      text-align: center;
    }
    
    .back-link {
      margin-top: 1.5rem;
      color: var(--primary);
      text-decoration: none;
      font-weight: 500;
    }
    
    .back-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="screen">
    <div class="logo">
      <i class="bi bi-person-workspace"></i>
    </div>
    
    <h2 class="admin-title">Admin Login</h2>

    <form method="POST" action="" style="width: 100%;">
      <div class="input-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" placeholder="" required>
      </div>

      <div class="input-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required>
      </div>

      <button type="submit" class="login">Login</button>
    </form>
    
  </div>
</body>
</html>