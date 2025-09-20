<?php
session_start();
include 'db-conn.php';
// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['USERNAME']) && !isset($_COOKIE['USERNAME'])) {
    header("Location: unauthorized.php");
    exit();
}
$username = isset($_SESSION['USERNAME']) ? $_SESSION['USERNAME'] : $_COOKIE['USERNAME'];
$nom = isset($_SESSION['NOM']) ? $_SESSION['NOM'] : $_COOKIE['NOM'];
$role = isset($_SESSION['ROLE']) ? $_SESSION['ROLE'] : $_COOKIE['ROLE'];
$sql = "SELECT * FROM historique ORDER BY DATE_ACTION DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Historique - ONCF</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    body {
      background-color: #f5f7fa;
      color: #333;
      display: flex;
      min-height: 100vh;
    }
    
    /* Sidebar */
    .sidebar {
      width: 250px;
      background: #1d3557;
      color: white;
      position: fixed;
      height: 100vh;
    }
    
    .sidebar-brand {
      padding: 1.5rem 1rem;
      text-align: center;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .sidebar-brand h2 {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
    }
    
    .sidebar-menu {
      padding: 2rem 0;
    }
    
    .sidebar-menu ul {
      list-style: none;
    }
    
    .sidebar-menu li {
      margin-bottom: 0.5rem;
    }
    
    .sidebar-menu a {
      color: rgba(255, 255, 255, 0.8);
      text-decoration: none;
      padding: 0.8rem 1.5rem;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .sidebar-menu a:hover, .sidebar-menu a.active {
      background: rgba(255, 255, 255, 0.1);
      color: white;
      border-left: 4px solid #e63946;
    }
    
    /* Main Content */
    .main-content {
      flex: 1;
      margin-left: 250px;
    }
    
    /* Header */
    header {
      background: #1d3557;
      color: white;
      padding: 1rem 2rem;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .header-left {
      display: flex;
      align-items: center;
      gap: 20px;
    }
    
    .header-left img {
      height: 60px;
      margin-right: 60px;
    }
     /* Top Bar */
    .top-bar {
      background: #1d3557;
      color: white;
      padding: 0.8rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
      margin-bottom: 20px;
    }
    
    .top-left, .top-right {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    /* Container */
    .container {
      padding: 2rem;
    }
    
    /* Table */
    .table-container {
      background: white;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      overflow: hidden;
    }
    
    table {
      width: 100%;
      border-collapse: collapse;
    }
    
    thead {
      background: #1a4774;
      color: white;
    }
    
    th, td {
      padding: 1rem;
      text-align: left;
      border-bottom: 1px solid #eee;
    }
    
    tbody tr:hover {
      background: #f9f9f9;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
      .sidebar {
        width: 70px;
      }
      
      .sidebar-brand h2 span, .sidebar-menu a span {
        display: none;
      }
      
      .sidebar-menu a {
        justify-content: center;
        padding: 1rem;
      }
      
      .main-content {
        margin-left: 70px;
      }
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="sidebar-brand">
      <h2><i class="fas fa-train"></i> <span>ONCF</span></h2>
    </div>
    
    <nav class="sidebar-menu">
      <ul>
        <li>
          <a href="index.php">
            <i class="fas fa-home"></i>
            <span>Accueil</span>
          </a>
        </li>
        <li>
          <a href="stats.php">
            <i class="fas fa-chart-bar"></i>
            <span>Statistiques</span>
          </a>
        </li>
        <li>
          <a href="historique.php" class="active">
            <i class="fas fa-history"></i>
            <span>Historique</span>
          </a>
        </li>
        <li>
          <a href="logout.php" id="log-out">
            <i class="fas fa-sign-out-alt"></i>
            <span>Déconnexion</span>
          </a>
        </li>
      </ul>
    </nav>
  </aside>

  <!-- Main Content -->
  <div class="main-content">
    <header>
      <div class="header-left">
        <img src="logo-oncf.png" alt="ONCF Logo">
        <h1>Historique des opérations</h1>
      </div>
    </header>
    <div class="top-bar">
      <div class="top-left">
        <?php
        $nom = "<span style='color:#f8f4e1'>" . strtoupper($nom) . "</span>";
        echo '<span>Utilisateur : <b>' . $nom . '</b></span>'
        ?>
      </div>
      <div class="top-right">
        <?php
        $role = "<span style='color:#f8f4e1'>" . strtoupper($role) . "</span>";
        echo '<span?>Statue : <b>' . $role . '</b></span>'
        ?>
      </div>
    </div>
    <div class="container">
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Utilisateur</th>
              <th>ID Wagon</th>
              <th>Action</th>
              <th>Détails</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            <?php while($row = mysqli_fetch_assoc($result)) { ?>
              <tr>
                <td><?= $row['ID_HISTORIQUE'] ?></td>
                <td><?= $row['USERNAME'] ?></td>
                <td><?= $row['NUM_WAGON'] ?></td>
                <td><?= $row['ACTION'] ?></td>
                <td><?= $row['DETAILS'] ?></td>
                <td><?= $row['DATE_ACTION'] ?></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <script>
    // Confirmation de déconnexion
    document.getElementById('log-out').addEventListener('click', function(e) {
      e.preventDefault();
      if (confirm('Êtes-vous sûr de vouloir vous déconnecter ?')) {
        window.location.href = 'logout.php';
      }
    });
  </script>
</body>
</html>