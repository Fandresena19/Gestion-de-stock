<?php
include('head.php');

// Initialisation des variables
$message = '';
$messageType = '';
$user = ['nom_utilisateur' => '']; // Initialisation par défaut pour éviter les erreurs

// Connexion à la base de données
try {
    $bdd = include("../traitement/db.php");
    
    // Récupération des informations de l'utilisateur
    if (isset($_SESSION['id_utilisateur'])) {
        $userId = $_SESSION['id_utilisateur'];
        $stmt = $bdd->prepare("SELECT nom_utilisateur FROM utilisateur WHERE id_utilisateur = ?");
        $stmt->execute([$userId]);
        $userResult = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($userResult) {
            $user = $userResult;
        }
    }
    
    // Traitement du formulaire de modification
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!isset($_SESSION['id_utilisateur'])) {
            $message = "Vous devez être connecté pour modifier votre profil.";
            $messageType = "error";
        } else {
            $currentPassword = $_POST['current_password'] ?? '';
            $newName = $_POST['new_name'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            // Vérification du mot de passe actuel
            $stmt = $bdd->prepare("SELECT id_utilisateur, mot_de_passe FROM utilisateur WHERE id_utilisateur = ?");
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($currentPassword == $result['mot_de_passe']){
                $updateFields = [];
                $updateParams = [];
                
                // Modification du nom si demandé
                if (!empty($newName)) {
                    $updateFields[] = "nom_utilisateur = ?";
                    $updateParams[] = $newName;
                }
                
                // Modification du mot de passe si demandé
                if (!empty($newPassword)) {
                    if ($newPassword === $confirmPassword) {
                        $updateFields[] = "mot_de_passe = ?";
                        $updateParams[] = password_hash($newPassword, PASSWORD_DEFAULT);
                    } else {
                        $message = "Les nouveaux mots de passe ne correspondent pas.";
                        $messageType = "error";
                    }
                }
                
                // Mise à jour si des modifications sont demandées
                if (!empty($updateFields) && empty($message)) {
                    $updateQuery = "UPDATE utilisateur SET " . implode(", ", $updateFields) . " WHERE id_utilisateur = ?";
                    $updateParams[] = $userId;
                    
                    $updateStmt = $bdd->prepare($updateQuery);
                    
                    if ($updateStmt->execute($updateParams)) {
                        $message = "Profil mis à jour avec succès!";
                        $messageType = "success";
                        
                        // Mise à jour des informations en session si le nom a été modifié
                        if (!empty($newName)) {
                            $_SESSION['nom_utilisateur'] = $newName;
                            $user['nom_utilisateur'] = $newName;
                        }
                    } else {
                        $message = "Erreur lors de la mise à jour du profil.";
                        $messageType = "error";
                    }
                } elseif (empty($updateFields)) {
                    $message = "Aucune modification demandée.";
                    $messageType = "error";
                }
            } else {
                $message = "Mot de passe actuel incorrect.";
                $messageType = "error";
            }
        }
    }
} catch (PDOException $e) {
    $message = "Erreur de connexion à la base de données: " . $e->getMessage();
    $messageType = "error";
}
?>

<div class="home-content">
  <h3>Utilisateur</h3>
  
  <?php if(!empty($message)): ?>
  <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?>">
    <?php echo $message; ?>
  </div>
  <?php endif; ?>
  
  <div class="overview-boxes">
    <!-- Box 1: Profil Utilisateur -->
    <div class="box">
      <div class="box-topic">Profil Utilisateur</div>
      <div class="profile-info">
        <div class="info-item">
          <span class="label">Nom d'utilisateur:</span>
          <span class="value"><?php echo htmlspecialchars($user['nom_utilisateur'] ?? ''); ?></span>
        </div>
      </div>
    </div>
    
    <!-- Box 2: Modification du profil -->
    <div class="box">
      <div class="box-topic">Modifier le profil</div>
      <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="form-group">
          <label for="current_password">Mot de passe actuel*</label>
          <input type="password" id="current_password" name="current_password" required>
        </div>
        
        <div class="form-group">
          <label for="new_name">Nouveau nom d'utilisateur</label>
          <input type="text" id="new_name" placeholder="<?php echo htmlspecialchars($user['nom_utilisateur'] ?? ''); ?>" name="new_name">
        </div>
        
        <div class="form-group">
          <label for="new_password">Nouveau mot de passe</label>
          <input type="password" id="new_password" name="new_password">
        </div>
        
        <div class="form-group">
          <label for="confirm_password">Confirmer le nouveau mot de passe</label>
          <input type="password" id="confirm_password" name="confirm_password">
        </div>
        
        <div class="form-group">
          <button type="submit" class="btn-update">Mettre à jour</button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
.overview-boxes {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
  margin-bottom: 26px;
}

.box {
  flex: 1;
  min-width: 300px;
  background: #fff;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
}

.box-topic {
  font-size: 20px;
  font-weight: 500;
  margin-bottom: 20px;
  color: #0A2558;
}

.profile-info .info-item {
  margin-bottom: 15px;
  display: flex;
  flex-direction: column;
}

.profile-info .label {
  font-weight: 500;
  margin-bottom: 5px;
  color: #555;
}

.profile-info .value {
  font-size: 16px;
}

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  font-weight: 500;
  margin-bottom: 5px;
  color: #555;
}

.form-group input {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 5px;
  font-size: 16px;
}

.btn-update {
  background: #0A2558;
  color: white;
  border: none;
  padding: 10px 20px;
  height: auto;
  border-radius: 5px;
  cursor: pointer;
  font-size: 16px;
  transition: background 0.3s;
}

.btn-update:hover {
  background: #1D3C78;
}

.alert {
  padding: 15px;
  margin-bottom: 20px;
  border-radius: 5px;
}

.alert-success {
  background-color: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}

.alert-danger {
  background-color: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}
</style>

<?php
include('foot.php')
?>