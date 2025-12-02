<?php
session_start();
include('../cfg.php');
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

function PokazKontakt($error = '') {
    echo '<div class="box kontakt">';
    echo '<h2>Formularz kontaktowy</h2>';

    if (!empty($error)) {
        echo "<p class='error'>$error</p>";
    }

    echo '
    <form method="post" action="">
      <table class="kontakt">
        <tr><td>Twój e-mail:</td><td><input type="email" name="email"></td></tr>
        <tr><td>Temat:</td><td><input type="text" name="temat"></td></tr>
        <tr><td>Treść:</td><td><textarea name="tresc" rows="6" cols="40"></textarea></td></tr>
        <tr><td>&nbsp;</td><td><input type="submit" name="kontakt_submit" value="Wyślij wiadomość"></td></tr>
      </table>
    </form>
    <br><a href="contact.php?tryb=reset">Przypomnij hasło</a> | 
    <a href="../index.php">Powrót do strony głównej</a>';
    echo '</div>';
}

function WyslijMailKontakt($odbiorca) {
    if (empty($_POST['temat']) || empty($_POST['tresc']) || empty($_POST['email'])) {
        PokazKontakt('[nie_wypelniles_pola]');
    } else {
        $mail['subject']   = $_POST['temat'];
        $mail['body']      = $_POST['tresc'];
        $mail['sender']    = $_POST['email'];
        $mail['recipient'] = $odbiorca;

        $header = "From: Formularz kontaktowy <" . $mail['sender'] . ">\n";
        $header .= "MIME-Version: 1.0\nContent-Type: text/plain; charset=utf-8\nContent-Transfer-Encoding: 8bit\n";
        $header .= "X-Sender: <" . $mail['sender'] . ">\n";
        $header .= "X-Mailer: PHP\n";
        $header .= "X-Priority: 3\n";
        $header .= "Return-Path: <" . $mail['sender'] . ">\n";

        mail($mail['recipient'], $mail['subject'], $mail['body'], $header);

        echo '<div class="box kontakt"><p>Wiadomość została przesłana prawidłowo.</p><br>
              <a href="contact.php">Powrót do formularza kontaktowego</a></div>';
    }
}

function PokazPrzypomnijHaslo($error = '') {
    echo '<div class="box kontakt">';
    echo '<h2>Przypomnienie hasła</h2>';

    if (!empty($error)) {
        echo "<p class='error'>$error</p>";
    }

    echo '
    <form method="post" action="">
      <table class="kontakt">
        <tr><td>Twój e-mail:</td><td><input type="email" name="email"></td></tr>
        <tr><td>&nbsp;</td><td><input type="submit" name="przypomnij_submit" value="Przypomnij hasło"></td></tr>
      </table>
    </form>
    <br><a href="contact.php?tryb=kontakt">Formularz kontaktowy</a> | 
    <a href="../index.php">Powrót do strony głównej</a>';
    echo '</div>';
}

function PrzypomnijHaslo($odbiorca, $login, $pass) {
    if (empty($_POST['email'])) {
        PokazPrzypomnijHaslo('Podaj adres e-mail, aby przypomnieć hasło.');
        return;
    }

    $mail['subject']   = "Przypomnienie hasła do panelu CMS";
    $mail['body']      = "Login: $login\nHasło: $pass\n\nUWAGA: To przypomnienie jest przesyłane w formie niezaszyfrowanej – używaj tylko w celach testowych!";
    $mail['sender']    = "no-reply@japgotowanie.pl";
    $mail['recipient'] = $_POST['email'];

    $header = "From: System CMS <" . $mail['sender'] . ">\n";
    $header .= "MIME-Version: 1.0\nContent-Type: text/plain; charset=utf-8\nContent-Transfer-Encoding: 8bit\n";
    $header .= "X-Sender: <" . $mail['sender'] . ">\n";
    $header .= "X-Mailer: PHP\n";
    $header .= "X-Priority: 3\n";
    $header .= "Return-Path: <" . $mail['sender'] . ">\n";

    mail($mail['recipient'], $mail['subject'], $mail['body'], $header);

    echo '<div class="box kontakt"><p>Wiadomość z przypomnieniem hasła została wysłana na podany adres e-mail.</p>
          <br><a href="contact.php?tryb=kontakt">Powrót do formularza kontaktowego</a></div>';
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <title>Kontakt</title>
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/kontakt.css">
</head>
<body>
<header>
  <h1>Skontaktuj się z nami</h1>
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
    <li><a href="../admin/admin.php">Panel CMS</a></li>
    <li><a href="contact.php" class="active">Kontakt</a></li>
  </ul>
</nav>

<main class="content">
<?php
$odbiorca = "kontakt@japgotowanie.pl";

if (isset($_GET['tryb'])) {
    $_SESSION['tryb_kontaktu'] = $_GET['tryb'];
}
$tryb = $_SESSION['tryb_kontaktu'] ?? 'kontakt';

if (isset($_POST['kontakt_submit'])) {
    WyslijMailKontakt($odbiorca);
} elseif (isset($_POST['przypomnij_submit'])) {
    PrzypomnijHaslo($odbiorca, $login, $pass);
} else {
    if ($tryb === 'reset') {
        PokazPrzypomnijHaslo();
    } else {
        PokazKontakt();
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
