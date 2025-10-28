<?php
/**
 * @title: Proyecto integrador Ev01 - Página principal
 * @description:  Bienvenida a la aplicación
 *
 * @version    0.1
 *
 * @author ander_frago@cuatrovientos.org
 */

require_once 'templates/header.php';
require_once 'templates/menu.php';
require_once 'utils/SessionHelper.php';
?>
<!-- Bootstrap core CSS
* TODO REVISE Este es el aspecto negativo de esta estructura ya que el código esta duplicado
Y además no está en ASSETS
================================================== -->
<link rel="stylesheet" href=".\assets\css\bootstrap.css">

<div class="container-fluid py-5 my-5 bg-light">
  <div id="bienvenida" class="container">
    <h1 class='display-3'>Liga de futbol</h1>
    <?php
    if (SessionHelper::loggedIn()) echo "<p class='display-6'> Has iniciado sesión: ".$user."</p>";
    else           echo "<p class='display-6'> por favor, regístrate o inicia sesión.</p>";
    ?>
  </div>
</div>



<!-- Bootstrap core JavaScript
* TODO REVISE Este es el aspecto negativo de esta estructura ya que el código esta duplicado
================================================== -->

<script src=".\assets\js\bootstrap.js"></script>

</body>

</html>