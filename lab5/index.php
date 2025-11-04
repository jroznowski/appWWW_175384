<!DOCTYPE html>
<html lang="pl">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" >
    <meta name="description" content="Projekt 1" >
    <meta name="keywords" content="HTML, CSS3, JavaScript" >
    <meta name="author" content="Jakub Rożnowski" >
    <?php
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
    $idp = $_GET['idp'] ?? '';

    if ($idp == '' || $idp == 'glowna') $strona = 'html/glowna.html';
    elseif ($idp == 'skladniki') $strona = 'html/skladniki.html';
    elseif ($idp == 'przepisy') $strona = 'html/przepisy.html';
    elseif ($idp == 'filmy') $strona = 'html/filmy.html';
    elseif ($idp == 'kultura') $strona = 'html/kultura.html';
    elseif ($idp == 'polecane') $strona = 'html/polecane.html';
    elseif ($idp == 'poligon_js') $strona = 'html/poligon_js.html';
    ?>
    <?php
    echo '<link rel="stylesheet" href="css/style.css">';
    if ($_GET['idp'] == 'kultura') {
        echo '<link rel="stylesheet" href="css/kultura.css">';
    } elseif (($_GET['idp'] == 'polecane')) {   
        echo '<link rel="stylesheet" href="css/lokale.css">';
    } elseif(($_GET['idp'] == 'poligon_js')){
        echo '<script src="js/kolorujtlo.js" type="text/javascript"></script>';
        echo '<script src="js/timedate.js" type="text/javascript"></script>';
        echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>';
    }
    ?>
    <title>Kuchnia japońska</title>
  </head>
  <body onload="<?= ($idp=='poligon_js' || $idp=='') ? 'startClock()' : '' ?>">
    <header>
      <h1>Kuchnia japońska</h1>
    </header>
    <nav>
      <ul class="navbar">
        <li><a href="index.php?idp=glowna" class="<?= ($idp=='glowna' || $idp=='') ? 'active' : '' ?>">Strona główna</a></li>
        <li><a href="index.php?idp=skladniki" class="<?= ($idp=='skladniki') ? 'active' : '' ?>">Składniki i techniki</a></li>
        <li><a href="index.php?idp=przepisy" class="<?= ($idp=='przepisy') ? 'active' : '' ?>">Proste przepisy</a></li>
        <li><a href="index.php?idp=filmy" class="<?= ($idp=='filmy') ? 'active' : '' ?>">Filmy</a></li>
        <li><a href="index.php?idp=kultura" class="<?= ($idp=='kultura') ? 'active' : '' ?>">Kultura</a></li>
        <li><a href="index.php?idp=polecane" class="<?= ($idp=='polecane') ? 'active' : '' ?>">Polecane lokale</a></li>
        <li><a href="index.php?idp=poligon_js" class="<?= ($idp=='poligon_js') ? 'active' : '' ?>">Poligon JavaScript</a></li>
      </ul>
    </nav>

<main class="content">
    <?php
    if(file_exists($strona)){
        include($strona);
    } else {
        echo '<p class="error">Plik podstrony nie istnieje w katalogu /html.</p>';
    }
    ?>
</main>
    <footer>
      <?php
      $nr_indeksu = '175384';
      $nrGrupy = 'ISI 2';

      echo 'Autor: Jakub Rożnowski '.$nr_indeksu. ' grupa '.$nrGrupy.'<br /><br />';
      ?>
    </footer>
  </body>
</html>
