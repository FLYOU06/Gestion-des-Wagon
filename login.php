<?php
session_start(); // Start the session

// Check if the user is already logged in
if (isset($_SESSION['USERNAME']) || (isset($_COOKIE['USERNAME']) && isset($_COOKIE['NOM']) && isset($_COOKIE['ROLE']))) {
    header("Location: index.php");
    exit();
}

include 'db-conn.php'; // Include your database connection file

$login_error = false;

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $remember = isset($_POST['remember']) ? true : false;
    
    // Check user accounts 
    $query = "SELECT * FROM utilisateur WHERE USERNAME = ? AND MDP = ?";
    $stmt = mysqli_prepare($conn, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ss", $username, $password);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            if ($remember) {
                // Set cookies for 30 days
                setcookie('USERNAME', $username, time() + (86400 * 30), "/");
                setcookie('NOM', $row['NOM'], time() + (86400 * 30), "/");
                setcookie('ROLE', $row['ROLE'], time() + (86400 * 30), "/");
            } else {
                // Set session variables
                $_SESSION['USERNAME'] = $username;
                $_SESSION['NOM'] = $row['NOM'];
                $_SESSION['ROLE'] = $row['ROLE'];
            }
            
            header("Location: index.php");
            exit();
        } else {
            // Invalid credentials
            $login_error = true;
        }
    } else {
        $login_error = true;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion - ONCF</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary: #1a4774;
      --primary-dark: #0d2d4e;
      --secondary: #e63946;
      --accent: #457b9d;
      --light: #f1faee;
      --dark: #1d3557;
      --success: #2a9d8f;
      --warning: #e9c46a;
      --danger: #e63946;
      --gray: #6c757d;
      --light-gray: #e9ecef;
      --transition: all 0.3s ease;
    }
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    body {
      background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.7)), url('2023-Maintenance.jpg') no-repeat center center fixed;
      background-size: cover;
      color: white;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      line-height: 1.6;
      padding: 20px;
    }
    
    .login-container {
      display: flex;
      width: 100%;
      max-width: 1000px;
      min-height: 550px;
      background: rgba(255, 255, 255, 0.95);
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
    }
    
    .login-left {
      flex: 1;
      background: linear-gradient(to bottom right, var(--primary), var(--primary-dark));
      color: white;
      padding: 40px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      position: relative;
      overflow: hidden;
    }
    
    .login-left::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: url('2023-Maintenance.png') no-repeat center center;
      background-size: cover;
      opacity: 0.15;
    }
    
    .login-brand {
      text-align: center;
      margin-bottom: 40px;
      position: relative;
      z-index: 1;
    }
    
    .login-brand h1 {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 15px;
      font-size: 2.2rem;
      margin-bottom: 10px;
    }
    
    .login-brand p {
      font-size: 1.1rem;
      opacity: 0.9;
    }
    
    .login-features {
      position: relative;
      z-index: 1;
    }
    
    .error-toast {
      position: relative;
      color: var(--danger);
      background-color: #fee;
      border: 1px solid #fcc;
      border-radius: 6px;
      padding: 15px 20px 15px 50px;
      margin: 10px 0 20px 0;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      display: <?php echo $login_error ? 'block' : 'none'; ?>;
    }

    .error-toast i {
      position: absolute;
      left: 20px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 1.2rem;
    }
    
    .feature {
      display: flex;
      align-items: center;
      gap: 15px;
      margin-bottom: 25px;
    }
    
    .feature-icon {
      width: 50px;
      height: 50px;
      background: rgba(255, 255, 255, 0.15);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.3rem;
    }
    
    .feature-text h3 {
      font-size: 1.2rem;
      margin-bottom: 5px;
    }
    
    .feature-text p {
      opacity: 0.8;
      font-size: 0.95rem;
    }
    
    .login-right {
      flex: 1;
      padding: 40px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    
    .login-form {
      width: 100%;
      max-width: 400px;
      margin: 0 auto;
    }
    
    .login-form h2 {
      color: var(--primary);
      font-size: 1.8rem;
      margin-bottom: 20px;
      text-align: center;
    }
    
    .form-group {
      margin-bottom: 20px;
    }
    
    .form-group label {
      display: block;
      margin-bottom: 8px;
      color: var(--dark);
      font-weight: 500;
    }
    
    .input-with-icon {
      position: relative;
    }
    
    .input-with-icon i {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--gray);
    }
    
    .input-with-icon input {
      width: 100%;
      padding: 15px 15px 15px 45px;
      border: 1px solid var(--light-gray);
      border-radius: 8px;
      font-size: 1rem;
      transition: var(--transition);
    }
    
    .input-with-icon input:focus {
      outline: none;
      border-color: var(--accent);
      box-shadow: 0 0 0 3px rgba(69, 123, 157, 0.1);
    }
    
    .remember-forgot {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }
    
    .remember {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    
    .remember input {
      width: 16px;
      height: 16px;
      cursor: pointer;
    }
    
    .remember label {
      color: var(--dark);
      font-size: 0.9rem;
      cursor: pointer;
    }
    
    .forgot {
      color: var(--primary);
      text-decoration: none;
      font-size: 0.9rem;
      transition: var(--transition);
    }
    
    .forgot:hover {
      color: var(--secondary);
      text-decoration: underline;
    }
    
    .login-btn {
      width: 100%;
      padding: 15px;
      background: var(--primary);
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 1.1rem;
      font-weight: 600;
      cursor: pointer;
      transition: var(--transition);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
    }
    
    .login-btn:hover {
      background: var(--primary-dark);
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(26, 71, 116, 0.3);
    }
    
    /* Responsive Design */
    @media (max-width: 900px) {
      .login-container {
        flex-direction: column;
        max-width: 500px;
        min-height: auto;
      }
      
      .login-left {
        padding: 30px;
      }
      
      .login-right {
        padding: 30px;
      }
    }
    
    @media (max-width: 480px) {
      .login-container {
        width: 100%;
      }
      
      .login-left, .login-right {
        padding: 25px;
      }
      
      .remember-forgot {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
      }
      
      .feature {
        flex-direction: column;
        text-align: center;
      }
      
      .login-brand h1 {
        font-size: 1.8rem;
      }
    }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="login-left">
      <div class="login-brand">
        <h1><i class="fas fa-train"></i> ONCF</h1>
        <p>Plateforme de gestion des wagons</p>
      </div>
      
      <div class="login-features">
        <div class="feature">
          <div class="feature-icon">
            <i class="fas fa-shield-alt"></i>
          </div>
          <div class="feature-text">
            <h3>Accès sécurisé</h3>
            <p>Plateforme sécurisée avec authentification</p>
          </div>
        </div>
        
        <div class="feature">
          <div class="feature-icon">
            <i class="fas fa-chart-line"></i>
          </div>
          <div class="feature-text">
            <h3>Suivi en temps réel</h3>
            <p>Surveillance des wagons et maintenance</p>
          </div>
        </div>
        
        <div class="feature">
          <div class="feature-icon">
            <i class="fas fa-history"></i>
          </div>
          <div class="feature-text">
            <h3>Historique complet</h3>
            <p>Consultation de l'historique des opérations</p>
          </div>
        </div>
      </div>
    </div>
    
    <div class="login-right">
      <form class="login-form" action="login.php" method="POST">
        <h2>Connexion</h2>
        
        <div class="error-toast">
          <i class="fas fa-exclamation-circle"></i>
          Nom d'utilisateur ou mot de passe incorrect. Veuillez réessayer.
        </div>
        
        <div class="form-group">
          <label for="username">Nom d'utilisateur</label>
          <div class="input-with-icon">
            <i class="fas fa-user"></i>
            <input type="text" id="username" name="username" placeholder="Entrez votre nom d'utilisateur" required autocomplete="username">
          </div>
        </div>
        
        <div class="form-group">
          <label for="password">Mot de passe</label>
          <div class="input-with-icon">
            <i class="fas fa-lock"></i>
            <input type="password" id="password" name="password" placeholder="Entrez votre mot de passe" required autocomplete="current-password">
          </div>
        </div>
        
        <div class="remember-forgot">
          <div class="remember">
            <input type="checkbox" id="remember" name="remember">
            <label for="remember">Se souvenir de moi</label>
          </div>
        </div>
        
        <button type="submit" class="login-btn">
          <i class="fas fa-sign-in-alt"></i> Se connecter
        </button>
      </form>
    </div>
  </div>

  <script>
    // Animation simple pour les inputs au focus
    document.querySelectorAll('input').forEach(input => {
      input.addEventListener('focus', function() {
        this.parentElement.style.transform = 'scale(1.02)';
      });
      
      input.addEventListener('blur', function() {
        this.parentElement.style.transform = 'scale(1)';
      });
    });
    
    // Masquer le message d'erreur si pas d'erreur
    <?php if (!$login_error): ?>
    document.querySelector('.error-toast').style.display = 'none';
    <?php endif; ?>
  </script>
</body>
</html>