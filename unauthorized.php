<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Accès Non Autorisé - ONCF</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    /* Utilisez les mêmes styles que la page 404 */
    :root {
      --primary: #1a4774;
      --secondary: #e63946;
      --dark: #1d3557;
      --light: #f8f9fa;
      --success: #2a9d8f;
      --warning: #e9c46a;
      --danger: #e63946;
      --gray: #6c757d;
    }
    
    body {
      background-color: #1d3557;
      color: #333;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      margin: 0;
      padding: 20px;
    }
    
    .container {
      background: #f8f9fa;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      padding: 40px;
      text-align: center;
      max-width: 600px;
      width: 100%;
    }
    
    .icon {
      font-size: 4rem;
      color: var(--warning);
      margin-bottom: 20px;
    }
    
    h1 {
      color: var(--dark);
      margin-bottom: 15px;
    }
    
    p {
      color: var(--gray);
      margin-bottom: 25px;
      line-height: 1.6;
    }
    
    .btn {
      display: inline-block;
      padding: 12px 24px;
      background: var(--primary);
      color: white;
      text-decoration: none;
      border-radius: 6px;
      margin: 5px;
      transition: all 0.3s;
    }
    
    .btn:hover {
      background: var(--dark);
      transform: translateY(-2px);
    }
    .btn-login {
      background: var(--success);
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="icon">
      <i class="fas fa-ban"></i>
    </div>
    
    <h1>Accès Non Autorisé</h1>
    
    <p>Vous n'avez pas les permissions nécessaires pour accéder à cette page.</p>
    
    <div>
      <a href="login.php" class="btn btn-login">
        <i class="fas fa-sign-in-alt"></i> Se connecter
      </a>
    </div>
  </div>
</body>
</html>