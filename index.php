<?php
session_start();
include 'db-conn.php';
// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['USERNAME']) && !isset($_COOKIE['USERNAME'])) {
    header("Location: unauthorized.php");
    exit();
}
// Get admin info
$username = isset($_SESSION['USERNAME']) ? $_SESSION['USERNAME'] : $_COOKIE['USERNAME'];
$nom = isset($_SESSION['NOM']) ? $_SESSION['NOM'] : $_COOKIE['NOM'];
$role = isset($_SESSION['ROLE']) ? $_SESSION['ROLE'] : $_COOKIE['ROLE'];
// Fetch wagons needing inspection
$today = date("Y-m-d");
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : "";

if (!empty($search)) {
    $sql = "SELECT * FROM wagon WHERE NUM_WAGON LIKE '%$search%' AND EST_SUPPRIME = 0";
} else {
    $sql = "SELECT * FROM wagon 
            WHERE (DERNIER_DATE_VG <= DATE_SUB('$today', INTERVAL 1 YEAR)
               OR DERNIER_DATE_RL <= DATE_SUB('$today', INTERVAL 7 YEAR))
            AND EST_SUPPRIME = 0";
}
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ONCF - Gestion Wagons</title>
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
    
    .top-center {
      flex: 1;
      display: flex;
      justify-content: center;
    }
    
    .top-center form {
      width: 100%;
      max-width: 400px;
    }
    
    #search {
      width: 100%;
      color: white;
      padding: 8px 35px 8px 15px;
      border-radius: 20px;
      border: 1px solid #ddd;
      background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="gray" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/></svg>') no-repeat right 12px center;
    }
    
    /* Container */
    .container {
      padding: 0 2rem 2rem;
    }
    
    .action-buttons {
      display: flex;
      justify-content: flex-end;
      gap: 15px;
      margin-bottom: 25px;
    }
    
    .btn {
      padding: 12px 20px;
      background: var(--primary);
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 8px;
      transition: all 0.3s;
      font-weight: 500;
    }
    
    .btn:hover {
      background: var(--dark);
      transform: translateY(-2px);
    }
    
    .btn-delete {
      background: var(--danger);
    }
    
    .btn-delete:hover {
      background: #c1121f;
    }
    
    .btn i {
      font-size: 1.1rem;
    }
    
    /* Delete Bar */
    .delete-bar {
      background: #fff3f3;
      border: 1px solid #ffcccc;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 25px;
      display: none;
      align-items: center;
      gap: 15px;
    }
    
    .delete-bar.visible {
      display: flex;
      animation: fadeIn 0.3s ease;
    }
    
    .delete-input {
      flex: 1;
      padding: 10px 15px;
      border: 1px solid #ffb3b3;
      border-radius: 5px;
      font-size: 1rem;
    }
    
    .delete-input:focus {
      outline: none;
      border-color: var(--danger);
      box-shadow: 0 0 0 3px rgba(230, 57, 70, 0.1);
    }
    
    .btn-confirm {
      background: var(--danger);
      padding: 10px 20px;
    }
    
    .btn-cancel {
      background: var(--gray);
      padding: 10px 20px;
    }
    
    /* Table */
    .table-container {
      background: white;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      overflow: hidden;
      margin-bottom: 2rem;
    }
    
    table {
      width: 100%;
      border-collapse: collapse;
    }
    
    thead {
      background: var(--primary);
      color: white;
    }
    
    th, td {
      padding: 1rem;
      text-align: left;
      border-bottom: 1px solid #eee;
    }
    
    tbody tr {
      transition: background 0.2s;
    }
    
    tbody tr:hover {
      background: #f9f9f9;
    }
    
    .etat-valide {
      color: var(--success);
      font-weight: 600;
    }
    
    .etat-expire {
      color: var(--danger);
      font-weight: 600;
    }
    
    /* Modal */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0,0,0,0.6);
    }
    
    .modal-content {
      background-color: #fff;
      margin: 10% auto;
      padding: 20px;
      border-radius: 8px;
      width: 500px;
      max-width: 90%;
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
      animation: fadeIn 0.3s ease;
    }
    
    .close {
      color: #aaa;
      float: right;
      font-size: 24px;
      font-weight: bold;
      cursor: pointer;
    }
    
    .close:hover {
      color: #000;
    }
    
    .modal-content h2 {
      margin-bottom: 20px;
      color: var(--primary);
    }
    
    .modal-content label {
      display: block;
      margin-bottom: 5px;
      font-weight: 500;
    }
    
    .modal-content input {
      width: 100%;
      padding: 8px;
      margin-bottom: 15px;
      border: 1px solid #ddd;
      border-radius: 4px;
    }
    
    /* Styles pour la modale de suppression */
    .modal-delete .modal-content {
      background: linear-gradient(to bottom, #fff, #fff8f8);
      border-top: 5px solid var(--danger);
      text-align: center;
    }
    
    .modal-icon {
      font-size: 4rem;
      color: var(--danger);
      margin-bottom: 20px;
    }
    
    .modal-title {
      font-size: 1.5rem;
      margin-bottom: 15px;
      color: var(--dark);
    }
    
    .modal-message {
      margin-bottom: 25px;
      color: var(--gray);
      line-height: 1.6;
    }
    
    .modal-buttons {
      display: flex;
      justify-content: center;
      gap: 15px;
    }
    
    @keyframes fadeIn {
      from {opacity: 0; transform: translateY(-20px);}
      to {opacity: 1; transform: translateY(0);}
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
      .top-bar {
        flex-direction: column;
        gap: 10px;
        padding: 1rem;
      }
      
      .action-buttons {
        flex-direction: column;
      }
      
      .table-container {
        overflow-x: auto;
      }
      
      table {
        min-width: 700px;
      }
      
      header {
        flex-direction: column;
        gap: 15px;
        text-align: center;
      }
      
      .delete-bar {
        flex-direction: column;
        align-items: stretch;
      }
      
      .modal-buttons {
        flex-direction: column;
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
          <a href="index.php" class="active">
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
          <a href="historique.php">
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
        <h1>Gestion des Wagons</h1>
      </div>
    </header>

    <!-- Top Bar -->
    <div class="top-bar">
      <div class="top-left">
        <?php
        $nom = "<span style='color:#f8f4e1'>" . strtoupper($nom) . "</span>";
        echo '<span>Utilisateur : <b>' . $nom . '</b></span>'
        ?>
      </div>
      <div class="top-center">
        <form method="GET" action="index.php">
          <input id="search" type="text" name="search" placeholder="Rechercher par ID Wagon..." 
                value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>">
        </form>
      </div>
      <div class="top-right">
        <?php
        $role = "<span style='color:#f8f4e1'>" . strtoupper($role) . "</span>";
        echo '<span>Statut : <b>' . $role . '</b></span>'
        ?>
      </div>
    </div>

    <div class="container">
      <!-- Barre de suppression (cachée par défaut) -->
      <div class="delete-bar" id="deleteBar">
        <div>
          <i class="fas fa-exclamation-triangle" style="color: var(--danger); font-size: 1.5rem;"></i>
        </div>
        <div style="flex: 1;">
          <p style="margin-bottom: 5px; font-weight: 500;">Entrez le numéro du wagon à supprimer</p>
          <input type="text" id="wagonToDelete" class="delete-input" placeholder="Numéro de wagon">
        </div>
        <div style="display: flex; gap: 10px;">
          <button class="btn btn-confirm" onclick="confirmDelete()">
            <i class="fas fa-check"></i> Confirmer
          </button>
          <button class="btn btn-cancel" onclick="hideDeleteBar()">
            <i class="fas fa-times"></i> Annuler
          </button>
        </div>
      </div>

      <div class="action-buttons">
        <button class="btn" onclick="location.href='index.php'">
          <i class="fas fa-search"></i> Scanner les Wagons
        </button>
        <button class="btn" onclick="openForm()">
          <i class="fas fa-plus"></i> Ajouter Nouveau Wagon
        </button>
        <button class="btn btn-delete" onclick="showDeleteBar()">
          <i class="fas fa-trash"></i> Supprimer Wagon
        </button>
      </div>

      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>ID Wagon</th>
              <th>Numéro de série</th>
              <th>Dernier VG</th>
              <th>Dernier RL</th>
              <th>État VG</th>
              <th>État RL</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php while($row = mysqli_fetch_assoc($result)) { 
              // Déterminer l'état du VG
              $etat_vg = (strtotime($row['DERNIER_DATE_VG']) >= strtotime("-1 year")) ? "Valide" : "Expiré";
              // Déterminer l'état du RL
              $etat_rl = (strtotime($row['DERNIER_DATE_RL']) >= strtotime("-7 years")) ? "Valide" : "Expiré";
            ?>
            <?php
            $id_wagon = $row['NUM_WAGON'];
            $serie = $row['SERIE'];
            $dernier_date_VG = $row['DERNIER_DATE_VG'];
            $dernier_date_RL = $row['DERNIER_DATE_RL'];
            ?>
            <tr>
              <td data-label="ID Wagon"><?= $id_wagon ?></td>
              <td data-label="Numéro de série"><?= $serie ?></td>
              <td data-label="Dernier VG"><?= $dernier_date_VG?></td>
              <td data-label="Dernier RL"><?= $dernier_date_RL?></td>
              <?php if ($etat_vg == "Valide") { $etat_vg_class = "etat-valide"; } else { $etat_vg_class = "etat-expire"; } ?>
              <?php if ($etat_rl == "Valide") { $etat_rl_class = "etat-valide"; } else { $etat_rl_class = "etat-expire"; } ?>
              <td data-label="État VG" class="<?= $etat_vg_class ?>"><?= $etat_vg ?></td>
              <td data-label="État RL" class="<?= $etat_rl_class ?>"><?= $etat_rl ?></td>
              <?php 
              $id_wagon = $row['NUM_WAGON'];
              $serie = $row['SERIE'];
              $dernier_date_VG = $row['DERNIER_DATE_VG'];
              $dernier_date_RL = $row['DERNIER_DATE_RL'];
              echo "<td data-label=\"Action\"><button class=\"btn\" style=\"margin-bottom: 5px; width: 100%;\" onclick= \"openModel('$id_wagon', '$serie', '$dernier_date_VG', '$dernier_date_RL')\" ><i class='fas fa-edit'></i> Editer</button></td>"; ?>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal pour l'édition -->
  <div id="modal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal('modal')">&times;</span>
      <h2><i class="fas fa-edit"></i> Modifier le wagon</h2>
      <form method="POST" action="modifier.php">
        <label for="idWagon">ID:</label>
        <input type="text" id="idWagon" name="id" readonly>

        <label for="numeroSerie">Numéro Série:</label>
        <input type="text" id="numeroSerie" name="numero_serie" readonly>

        <label for="dernierVG">Dernier VG:</label>
        <input type="date" id="dernierVG" name="dernier_vg">

        <label for="dernierRL">Dernier RL:</label>
        <input type="date" id="dernierRL" name="dernier_rl">

        <button class="btn" type="submit" style="width: 100%; margin-top: 10px;">
          <i class="fas fa-save"></i> Enregistrer
        </button>
      </form>
    </div>
  </div>

  <!-- Modal pour l'ajout -->
  <div id="form-container" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal('form-container')">&times;</span>
      <h2><i class="fas fa-plus"></i> Ajouter un Wagon</h2>
      <form action="ajouter.php" method="POST">
        <label for="newIdWagon">NUM WAGON:</label>
        <input type="text" id="newIdWagon" name="id" required>

        <label for="newNumeroSerie">Série:</label>
        <input type="text" id="newNumeroSerie" name="serie" required>

        <label for="newDernierVG">Dernier VG:</label>
        <input type="date" id="newDernierVG" name="dernierVG" required>

        <label for="newDernierRL">Dernier RL:</label>
        <input type="date" id="newDernierRL" name="dernierRL" required>

        <button class="btn" type="submit" style="width: 100%; margin-top: 10px;">
          <i class="fas fa-plus"></i> Ajouter Wagon
        </button>
      </form>
    </div>
  </div>

  <!-- Modal pour la confirmation de suppression -->
  <div id="modal-delete" class="modal modal-delete">
    <div class="modal-content">
      <span class="close" onclick="closeModal('modal-delete')">&times;</span>
      <div class="modal-icon">
        <i class="fas fa-exclamation-triangle"></i>
      </div>
      <h2 class="modal-title">Confirmer la suppression</h2>
      <p class="modal-message">Êtes-vous sûr de vouloir supprimer le wagon <strong id="wagonId"></strong>? Cette action est irréversible.</p>
      <div class="modal-buttons">
        <button class="btn btn-delete" onclick="executeDelete()">
          <i class="fas fa-trash"></i> Supprimer
        </button>
        <button class="btn btn-cancel" onclick="closeModal('modal-delete')">
          <i class="fas fa-times"></i> Annuler
        </button>
      </div>
    </div>
  </div>

  <script>
    // Fonctions pour la barre de suppression
    function showDeleteBar() {
      document.getElementById('deleteBar').classList.add('visible');
    }
    
    function hideDeleteBar() {
      document.getElementById('deleteBar').classList.remove('visible');
      document.getElementById('wagonToDelete').value = '';
    }
    
    function confirmDelete() {
      const wagonId = document.getElementById('wagonToDelete').value.trim();
      
      if (!wagonId) {
        alert('Veuillez entrer un numéro de wagon');
        return;
      }
      
      // Afficher la popup de confirmation
      document.getElementById('wagonId').textContent = wagonId;
      document.getElementById('modal-delete').style.display = 'block';
    }
    
    function executeDelete() {
      const wagonId = document.getElementById('wagonToDelete').value.trim();
      
      // Rediriger vers la page de suppression
      window.location.href = `delet.php?id=${encodeURIComponent(wagonId)}`;
    }
    
    function openModel(id, numero, vg, rl) {
      document.getElementById('idWagon').value = id;
      document.getElementById('numeroSerie').value = numero;
      document.getElementById('dernierVG').value = vg;
      document.getElementById('dernierRL').value = rl;
      document.getElementById('modal').style.display = 'block';
    }
    
    function openForm() {
      document.getElementById('form-container').style.display = 'block';
    }
    
    function closeModal(modalId) {
      document.getElementById(modalId).style.display = 'none';
    }
    
    // Fermer la modale en cliquant à l'extérieur
    window.onclick = function(event) {
      if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
      }
    }
  </script>
</body>
</html>