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
// Total wagons
$sql_total = "SELECT COUNT(*) as total FROM wagon WHERE EST_SUPPRIME = 0";
$total = mysqli_fetch_assoc(mysqli_query($conn, $sql_total))['total'];

// VG valide
$sql_vg_valide = "SELECT COUNT(*) as c FROM wagon WHERE dernier_date_VG >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR) AND EST_SUPPRIME = 0";
$vg_valide = mysqli_fetch_assoc(mysqli_query($conn, $sql_vg_valide))['c'];

// VG expiré
$vg_expire = $total - $vg_valide;

// RL valide
$sql_rl_valide = "SELECT COUNT(*) as c FROM wagon WHERE dernier_date_RL >= DATE_SUB(CURDATE(), INTERVAL 7 YEAR) AND EST_SUPPRIME = 0";
$rl_valide = mysqli_fetch_assoc(mysqli_query($conn, $sql_rl_valide))['c'];

// RL expiré
$rl_expire = $total - $rl_valide;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Statistiques - ONCF</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    :root {
      --primary: #1a4774;
      --secondary: #e63946;
      --dark: #1d3557;
      --light: #f8f9fa;
      --success: #2a9d8f;
      --warning: #e9c46a;
      --danger: #e63946;
      --gray: #6c757d;
      --sidebar-width: 250px;
    }
    
    body {
      background-color: #f5f7fa;
      color: #333;
      display: flex;
      min-height: 100vh;
    }
    
    /* Sidebar */
    .sidebar {
      width: var(--sidebar-width);
      background: var(--dark);
      color: white;
      position: fixed;
      height: 100vh;
      z-index: 1000;
      box-shadow: 3px 0 10px rgba(0, 0, 0, 0.1);
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
      font-size: 1.3rem;
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
      transition: all 0.3s;
    }
    
    .sidebar-menu a:hover, .sidebar-menu a.active {
      background: rgba(255, 255, 255, 0.1);
      color: white;
      border-left: 4px solid var(--secondary);
    }
    
    /* Main Content */
    .main-content {
      flex: 1;
      margin-left: var(--sidebar-width);
      width: calc(100% - var(--sidebar-width));
    }
    
    /* Header */
    header {
      background: var(--dark);
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
      background: var(--dark);
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
    
    .page-title {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 2rem;
      color: var(--dark);
    }
    
    /* Stats Cards */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin-bottom: 3rem;
    }
    
    .stat-card {
      background: white;
      border-radius: 8px;
      padding: 1.5rem;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      text-align: center;
    }
    
    .stat-icon {
      font-size: 2.5rem;
      margin-bottom: 1rem;
    }
    
    .stat-value {
      font-size: 2rem;
      font-weight: bold;
      margin-bottom: 0.5rem;
    }
    
    .stat-label {
      color: var(--gray);
      font-size: 0.9rem;
    }
    
    /* Charts */
    .charts-row {
      display: flex;
      justify-content: center;
      gap: 30px;
      flex-wrap: wrap;
      margin-top: 20px;
    }
    
    .chart-container {
      background: white;
      border-radius: 8px;
      padding: 1.5rem;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      width: 400px;
      height: 400px;
      display: flex;
      flex-direction: column;
    }
    
    .chart-title {
      text-align: center;
      margin-bottom: 1rem;
      font-weight: 600;
      color: var(--dark);
    }
    
    .chart-canvas {
      flex: 1;
    }
    /* Responsive */
    @media (max-width: 992px) {
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
        width: calc(100% - 70px);
      }
    }
    
    @media (max-width: 768px) {
      .charts-row {
        flex-direction: column;
        align-items: center;
      }
      
      .chart-container {
        width: 100%;
        max-width: 400px;
      }
      
      header {
        flex-direction: column;
        gap: 15px;
        text-align: center;
      }
      
      .container {
        padding: 1.5rem;
      }
    }
    
    @media (max-width: 480px) {
      .stats-grid {
        grid-template-columns: 1fr;
      }
      
      .chart-container {
        height: 300px;
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
          <a href="stats.php" class="active">
            <i class="fas fa-chart-bar"></i>
            <span>Statistiques</span>
          </a>
        </li>
        <li>
          <a href="historique.php">
            <i class="fas fa-history"></i>
            <span>Historique</span>
          </a>
        </li>
        <li>
          <a href="#" id="log-out">
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
        <h1>Statistiques - Wagons</h1>
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
      <h2 class="page-title"><i class="fas fa-chart-bar"></i> Statistiques Générales</h2>
      
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon" style="color: var(--primary);">
            <i class="fas fa-train"></i>
          </div>
          <div class="stat-value"><?= $total ?></div>
          <div class="stat-label">Total Wagons</div>
        </div>
        
        <div class="stat-card">
          <div class="stat-icon" style="color: var(--success);">
            <i class="fas fa-check-circle"></i>
          </div>
          <div class="stat-value"><?= $vg_valide ?></div>
          <div class="stat-label">VG Valides</div>
        </div>
        
        <div class="stat-card">
          <div class="stat-icon" style="color: var(--danger);">
            <i class="fas fa-exclamation-circle"></i>
          </div>
          <div class="stat-value"><?= $vg_expire ?></div>
          <div class="stat-label">VG Expirés</div>
        </div>
        
        <div class="stat-card">
          <div class="stat-icon" style="color: var(--success);">
            <i class="fas fa-check-circle"></i>
          </div>
          <div class="stat-value"><?= $rl_valide ?></div>
          <div class="stat-label">RL Valides</div>
        </div>
        
        <div class="stat-card">
          <div class="stat-icon" style="color: var(--danger);">
            <i class="fas fa-exclamation-circle"></i>
          </div>
          <div class="stat-value"><?= $rl_expire ?></div>
          <div class="stat-label">RL Expirés</div>
        </div>
      </div>

      <div class="charts-row">
        <div class="chart-container">
          <div class="chart-title">État des VG</div>
          <div class="chart-canvas">
            <canvas id="chartVG"></canvas>
          </div>
        </div>
        
        <div class="chart-container">
          <div class="chart-title">État des RL</div>
          <div class="chart-canvas">
            <canvas id="chartRL"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // VG Chart
    new Chart(document.getElementById('chartVG'), {
      type: 'doughnut',
      data: {
        labels: ['VG Valide', 'VG Expiré'],
        datasets: [{
          data: [<?= $vg_valide ?>, <?= $vg_expire ?>],
          backgroundColor: ['#4CAF50', '#F44336']
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom'
          }
        }
      }
    });

    // RL Chart
    new Chart(document.getElementById('chartRL'), {
      type: 'doughnut',
      data: {
        labels: ['RL Valide', 'RL Expiré'],
        datasets: [{
          data: [<?= $rl_valide ?>, <?= $rl_expire ?>],
          backgroundColor: ['#2196F3', '#FF9800']
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom'
          }
        }
      }
    });
    
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