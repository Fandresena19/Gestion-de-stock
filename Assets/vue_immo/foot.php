</section>

<script>
  let sidebar = document.querySelector(".sidebar");
  let sidebarBtn = document.querySelector(".sidebarBtn");
  let linksNames = document.querySelectorAll(".links_name");
  let logoName = document.querySelector(".logo_name");

  sidebarBtn.onclick = function () {
    sidebar.classList.toggle("active");
    
    if (sidebar.classList.contains("active")) {
      // When sidebar is active (collapsed)
      sidebarBtn.classList.replace("bx-menu", "bx-menu-alt-right");
      
      // Hide text elements with a small delay for smooth transition
      setTimeout(() => {
        linksNames.forEach(link => {
          link.style.display = "none";
        });
        logoName.style.display = "none";
      }, 100);
    } else {
      // When sidebar is inactive (expanded)
      sidebarBtn.classList.replace("bx-menu-alt-right", "bx-menu");
      
      // Show text elements
      linksNames.forEach(link => {
        link.style.display = "block";
      });
      logoName.style.display = "block";
    }
  };
  
  // Script pour gérer la classe active dans la navigation
  document.addEventListener("DOMContentLoaded", function() {
    // Récupérer tous les liens de navigation
    const navLinks = document.querySelectorAll(".nav-links li a");
    
    // Récupérer le chemin de la page actuelle
    const currentPath = window.location.pathname;
    
    // Retirer la classe active de tous les liens
    navLinks.forEach(link => {
      link.classList.remove("active");
    });
    
    // Ajouter la classe active au lien correspondant à la page actuelle
    navLinks.forEach(link => {
      const linkPath = link.getAttribute("href");
      // Vérifier si le chemin du lien est dans l'URL actuelle
      if (currentPath.includes(linkPath) && linkPath !== "./dashboard.php") {
        link.classList.add("active");
      } else if (currentPath.endsWith("dashboard.php") && linkPath === "./dashboard.php") {
        link.classList.add("active");
      }
    });
  });
</script>
</body>
</html>