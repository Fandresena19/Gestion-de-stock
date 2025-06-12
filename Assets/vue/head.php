<?php
session_start();

include_once("../traitement/function.php");
include_once("../auth.php");
?>

<!DOCTYPE html>
<html lang="fr" dir="ltr">

<head>
  <meta charset="UTF-8" />
  <title>Gestion Stock</title>
  <link rel="icon" type="image/png" href="../icone/Logo.png">
  <!-- <link rel="stylesheet" href="../Css/style.css" /> -->
  <link href="../bootstrap4/boxicons-2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="../Css/style.css">
  <link rel="stylesheet" href="../Css/dashboard.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>

<body>
  <div class="sidebar">
    <div class="logo-details">
      <img src="../icone/Logo.png" alt="" width="80px">
      <span class="logo_name">S RAYs</span>
    </div>
    <ul class="nav-links">
      <li>
        <a href="./dashboard.php" class="active">
          <i class="bx bx-grid-alt"></i>
          <span class="links_name">Dashboard</span>
        </a>
      </li>

      <li>
        <a href="./article.php">
          <i class="bx bx-box"></i>
          <span class="links_name">Article</span>
        </a>
      </li>

      <li>
        <a href="./achat.php">
          <i class="bx bx-list-ul"></i>
          <span class="links_name">Achat</span>
        </a>
      </li>
      <li>
        <a href="./sortie.php">
          <i class="bx bx-pie-chart-alt-2"></i>
          <span class="links_name">Sortie</span>
        </a>
      </li>
      <li>
        <a href="./stock.php">
          <i class="bx bx-coin-stack"></i>
          <span class="links_name">Stock</span>
        </a>
      </li>
      <li>
        <a href="./mouvement_stock.php">
          <i class="bx bx-book-alt"></i>
          <span class="links_name">Mouvement de stock</span>
        </a>
      </li>
      <li>
        <a href="./utilisateur.php">
          <i class="bx bx-user"></i>
          <span class="links_name">Utilisateur</span>
        </a>
      </li>
      <!-- <li>
          <a href="#">
            <i class="bx bx-message" ></i>
            <span class="links_name">Messages</span>
          </a>
        </li>
        <li>
          <a href="#">
            <i class="bx bx-heart" ></i>
            <span class="links_name">Favrorites</span>
          </a>
        </li> -->
      <!-- <li>
        <a href="#">
          <i class="bx bx-cog"></i>
          <span class="links_name">Configuration</span>
        </a>
      </li> -->
      <li class="log_out">
        <a href="../logout.php" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?');">
          <i class="bx bx-log-out"></i>
          <span class="links_name">Déconnexion</span>
        </a>
      </li>
    </ul>
  </div>
  <section class="home-section">
    <nav>
      <div class="sidebar-button">
        <i class="bx bx-menu sidebarBtn"></i>
        <span class="dashboard">Stock</span>
      </div>

      <div class="autre_nav">
        <ul>
          <li>
            <a href="./dashboard.php">
            <i class='bx bx-collapse-horizontal'></i>
              <span>Stock</span>
            </a>
          </li>
          <li>
            <a href="../vue_immo/dashboard .php">
              <i class="bx bx-box"></i>
              <span>Immobilisation</span>
            </a>
          </li>
        </ul>
      </div>
    </nav>