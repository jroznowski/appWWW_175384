<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Projekt 1">
    <meta name="keywords" content="HTML, CSS3, JavaScript">
    <meta name="author" content="Jakub Rożnowski">

    <?php
    /*
    Plik: index.php
    Funkcja: Obsługa szkieletu strony, wyświetlanie treści strony i poszczególnych podstron
    */
    // Konfiguracja obsługi błędów
    error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

    $idp = htmlspecialchars($_GET['idp'] ?? '', ENT_QUOTES, 'UTF-8');


    // Mapowanie podstron na ID
    $pages = [
        '' => 1,
        'glowna' => 1,
        'skladniki' => 2,
        'przepisy' => 3,
        'filmy' => 4,
        'kultura' => 5,
        'polecane' => 6,
        'poligon_js' => 7
    ];
    $strona = $pages[$idp] ?? 1;

    // Podstawowy arkusz CSS
    echo '<link rel="stylesheet" href="css/style.css">';

    // Dodatkowe style/skrypty dla wybranych podstron
    switch ($idp) {
        case 'kultura':
            echo '<link rel="stylesheet" href="css/kultura.css">';
            break;
        case 'polecane':
            echo '<link rel="stylesheet" href="css/lokale.css">';
            break;
        case 'poligon_js':
            echo '<script src="js/kolorujtlo.js"></script>';
            echo '<script src="js/timedate.js"></script>';
            echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>';
            break;
    }
    ?>
    <title>Kuchnia japońska</title>
</head>

<body onload="<?= ($idp == 'poligon_js' || $idp == '') ? 'startClock()' : '' ?>">
    <header>
        <h1>Kuchnia japońska</h1>
    </header>

    <!-- ==========================
      Menu nawigacyjne
    ========================== -->
    <nav>
        <ul class="navbar">
            <li><a href="index.php?idp=glowna" class="<?= ($idp == 'glowna' || $idp == '') ? 'active' : '' ?>">Strona
                    główna</a></li>
            <li><a href="index.php?idp=skladniki" class="<?= ($idp == 'skladniki') ? 'active' : '' ?>">Składniki i
                    techniki</a></li>
            <li><a href="index.php?idp=przepisy" class="<?= ($idp == 'przepisy') ? 'active' : '' ?>">Proste przepisy</a>
            </li>
            <li><a href="index.php?idp=filmy" class="<?= ($idp == 'filmy') ? 'active' : '' ?>">Filmy</a></li>
            <li><a href="index.php?idp=kultura" class="<?= ($idp == 'kultura') ? 'active' : '' ?>">Kultura</a></li>
            <li><a href="index.php?idp=polecane" class="<?= ($idp == 'polecane') ? 'active' : '' ?>">Polecane lokale</a>
            </li>
            <li><a href="index.php?idp=poligon_js" class="<?= ($idp == 'poligon_js') ? 'active' : '' ?>">Poligon
                    JavaScript</a></li>
            <li><a href="admin/admin.php">Panel CMS</a></li>
            <li><a href="php/contact.php">Kontakt</a></li>
        </ul>
    </nav>

    <!-- ==========================
         Główna treść strony z wykorzystaniem mechanizmu odczytu treści z bazy danych
    ========================== -->
    <main class="content">
        <?php
        include 'showpage.php';
        // Wyświetlenie treści podstrony z bazy danych z wykorzystaniem funkcji z pliku showpage.php
        PokazPodstrone($strona);
        ?>
    </main>

    <!-- ==========================
         Stopka strony
    ========================== -->
    <footer>
        <?php
        $nr_indeksu = '175384';
        $nrGrupy = 'ISI 2';
        echo 'Autor: Jakub Rożnowski ' . $nr_indeksu . ' grupa ' . $nrGrupy . '<br /><br />';
        ?>
    </footer>
</body>

</html>