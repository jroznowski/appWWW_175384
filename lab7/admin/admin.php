<?php
session_start();
include('../cfg.php');
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

function FormularzLogowania($error = '') {
    $wynik = '<div class="box logowanie">';
    $wynik .= '<h2>Panel CMS</h2>';

    if (!empty($error)) {
        $wynik .= "<p class='error'>$error</p>";
    }

    $wynik .= '
      <form method="post" action="">
        <table class="logowanie">
          <tr><td>Login:</td><td><input type="text" name="login_email"></td></tr>
          <tr><td>Hasło:</td><td><input type="password" name="login_pass"></td></tr>
          <tr><td>&nbsp;</td><td><input type="submit" name="log_submit" value="Zaloguj"></td></tr>
          <tr><td><a href="../index.php">Powrót do strony głównej</a></td></tr>
        </table>
      </form>
    </div>';
    return $wynik;
}

if (isset($_POST['log_submit'])) {
    if ($_POST['login_email'] === $login && $_POST['login_pass'] === $pass) {
        $_SESSION['zalogowany'] = true;
    } else {
        $error = "Błędny login lub hasło!";
    }
}


function ListaPodstron($link) {
    echo '<div class="box admin">';
    echo "<h2>Lista podstron</h2>";
    $query = "SELECT * FROM page_list ORDER BY id LIMIT 100";
    $result = mysqli_query($link, $query);

    echo "<table class='admin-table'>";
    echo "<tr><th>ID</th><th>Tytuł</th><th>Akcje</th></tr>";

    while ($row = mysqli_fetch_array($result)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['page_title']) . "</td>";
        echo "<td>
                <a href='?akcja=edytuj&id=" . $row['id'] . "'>Edytuj</a> | 
                <a href='?akcja=usun&id=" . $row['id'] . "' onclick=\"return confirm(\'Na pewno usunąć?\');\">Usuń</a>
              </td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<br><a href='?akcja=dodaj'>Dodaj nową podstronę</a>";
    echo " | <a href='?akcja=wyloguj'>Wyloguj</a>";
    echo " | <a href='../index.php'>Powrót do strony głównej</a>";
    echo '</div>';
}


function EdytujPodstrone($link, $id) {
    $query = "SELECT * FROM page_list WHERE id=" . intval($id);
    $result = mysqli_query($link, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        echo '<div class="box admin">';
        echo "<h2>Edytuj podstronę</h2>";
        echo '<form method="post" action="" class="admin-form">';
        echo 'Tytuł:<br><input type="text" name="tytul" value="' . htmlspecialchars($row['page_title']) . '"><br><br>';
        echo 'Treść:<br><textarea name="tresc" rows="10" cols="50">' . htmlspecialchars($row['page_content']) . '</textarea><br><br>';
        echo 'Aktywna: <input type="checkbox" name="aktywna" ' . ($row['status'] ? 'checked' : '') . '><br><br>';
        echo '<input type="submit" name="zapisz" value="Zapisz zmiany">';
        echo '</form>';

        if (isset($_POST['zapisz'])) {
            $tytul = mysqli_real_escape_string($link, $_POST['tytul']);
            $tresc = mysqli_real_escape_string($link, $_POST['tresc']);
            $aktywna = isset($_POST['aktywna']) ? 1 : 0;

            $update = "UPDATE page_list 
                       SET page_title='$tytul', page_content='$tresc', status='$aktywna' 
                       WHERE id=" . intval($id);
            mysqli_query($link, $update);

            echo "<p>Zmiany zostały zapisane pomyślnie.</p>";
        }

        echo "<br><a class='powrot' href='admin.php'>Powrót do listy</a>";
        echo '</div>';
    } else {
        echo '<div class="box admin"><p>Nie znaleziono podstrony.</p></div>';
    }
}


function DodajNowaPodstrone($link) {
    echo '<div class="box admin">';
    echo "<h2>Dodaj nową podstronę</h2>";
    echo '<form method="post" action="" class="admin-form">';
    echo 'Tytuł:<br><input type="text" name="tytul"><br><br>';
    echo 'Alias:<br><input type="text" name="alias"><br><br>';
    echo 'Treść:<br><textarea name="tresc" rows="10" cols="50"></textarea><br><br>';
    echo 'Aktywna: <input type="checkbox" name="aktywna"><br><br>';
    echo '<input type="submit" name="dodaj" value="Dodaj podstronę">';
    echo "<br><br><a class='powrot' href='admin.php'>Powrót do listy</a>";
    echo '</form>';

    if (isset($_POST['dodaj'])) {
        $tytul = mysqli_real_escape_string($link, $_POST['tytul']);
        $alias = mysqli_real_escape_string($link, $_POST['alias']);
        $tresc = mysqli_real_escape_string($link, $_POST['tresc']);
        $aktywna = isset($_POST['aktywna']) ? 1 : 0;

        $insert = "INSERT INTO page_list (page_title,page_content,status,alias) 
                   VALUES ('$tytul', '$tresc', '$aktywna', '$alias')";
        mysqli_query($link, $insert);

        echo "<br><p>Podstrona została dodana pomyślnie.</p>";
    }
    echo '</div>';
}

function UsunPodstrone($link, $id) {
    $id = intval($id);
    $delete = "DELETE FROM page_list WHERE id=$id";
    mysqli_query($link, $delete);

    echo '<div class="box admin">';
    echo "<h2>Usuwanie podstrony</h2>";
    echo "<p>Podstrona o ID $id została usunięta.</p>";
    echo "<br><a class='powrot' href='admin.php'>Powrót do listy</a>";
    echo '</div>';
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <title>Panel administracyjny</title>
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
<header>
  <h1>Kuchnia japońska</h1>
</header>
<nav>
  <ul class="navbar">
    <li><a href="../index.php?idp=glowna">Strona główna</a></li>
    <li><a href="../index.php?idp=skladniki">Składniki i techniki</a></li>
    <li><a href="../index.php?idp=przepisy">Proste przepisy</a></li>
    <li><a href="../index.php?idp=filmy">Filmy</a></li>
    <li><a href="../index.php?idp=kultura">Kultura</a></li>
    <li><a href="../index.php?idp=polecane">Polecane lokale</a></li>
    <li><a href="../index.php?idp=poligon_js">Poligon JavaScript</a></li>
    <li><a href="admin.php" class="active">Panel CMS</a></li>
  </ul>
</nav>

<main class="content">
<?php
if (!isset($_SESSION['zalogowany']) || $_SESSION['zalogowany'] !== true) {
    echo FormularzLogowania($error ?? '');
    exit;
}
else {
    if (isset($_GET['akcja'])) {
        switch ($_GET['akcja']) {
            case 'edytuj':
                if (isset($_GET['id'])) EdytujPodstrone($link, $_GET['id']);
                break;
            case 'dodaj':
                DodajNowaPodstrone($link);
                break;
            case 'usun':
                if (isset($_GET['id'])) UsunPodstrone($link, $_GET['id']);
                break;
            case 'wyloguj':
                session_destroy();
                header("Location: admin.php");
                exit;
            default:
                ListaPodstron($link);
        }
    } else {
        ListaPodstron($link);
    }
}
?>
</main>

<footer>
  <?php
  $nr_indeksu = '175384';
  $nrGrupy = 'ISI 2';
  echo 'Autor: Jakub Rożnowski '.$nr_indeksu.' grupa '.$nrGrupy.'<br /><br />';
  ?>
</footer>
</body>
</html>
