<?php
/*
  Plik: contact.php
  Funkcja: Obsługa formularza kontaktowego i przypomnienie hasła
*/

//Rozpoczęcie obsługi sesji na potrzeby zapisu stanu logowania
session_start();
include('../cfg.php');

//Obsługa błędów
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

/**
 * Funkcja: PokazKontakt
 * Wyświetla formularz kontaktowy.
 */
function PokazKontakt($error = '')
{
  echo '<div class="box kontakt">';
  echo '<h2>Formularz kontaktowy</h2>';

  if (!empty($error)) {
    echo "<p class='error'>$error</p>";
  }

  echo '
    <form method="post" action="">
      <table class="kontakt">
        <tr><td>Twój e-mail:</td><td><input type="email" name="email" required></td></tr>
        <tr><td>Temat:</td><td><input type="text" name="temat" required></td></tr>
        <tr><td>Treść:</td><td><textarea name="tresc" rows="6" cols="40" required></textarea></td></tr>
        <tr><td>&nbsp;</td><td><input type="submit" name="kontakt_submit" value="Wyślij wiadomość"></td></tr>
      </table>
    </form>
    <br><a href="contact.php?tryb=reset">Przypomnij hasło</a> | 
    <a href="../index.php">Powrót do strony głównej</a>';
  echo '</div>';
}
/**
 * Funkcja: WyslijMailKontakt
 * Wysyła wiadomość z formularza kontaktowego.
 */
function WyslijMailKontakt($odbiorca)
{
  if (empty($_POST['temat']) || empty($_POST['tresc']) || empty($_POST['email'])) {
    PokazKontakt('[nie_wypelniles_pola]');
  } else {
    $mail['subject'] = $_POST['temat'];
    $mail['body'] = $_POST['tresc'];
    $mail['sender'] = $_POST['email'];
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
/**
 * Funkcja: PokazPrzypomnijHaslo
 * Wyświetla formularz przypomnienia hasła.
 */
function PokazPrzypomnijHaslo($error = '')
{
  echo '<div class="box kontakt">';
  echo '<h2>Przypomnienie hasła</h2>';

  if (!empty($error)) {
    echo "<p class='error'>$error</p>";
  }

  echo '
    <form method="post" action="">
      <table class="kontakt">
        <tr><td>Twój e-mail:</td><td><input type="email" name="email" required></td></tr>
        <tr><td>&nbsp;</td><td><input type="submit" name="przypomnij_submit" value="Przypomnij hasło"></td></tr>
      </table>
    </form>
    <br><a href="contact.php?tryb=kontakt">Formularz kontaktowy</a> | 
    <a href="../index.php">Powrót do strony głównej</a>';
  echo '</div>';
}

/**
 * Funkcja: PrzypomnijHaslo
 * Wysyła wiadomość z przypomnieniem hasła do panelu CMS.
 * UWAGA: hasło przesyłane w formie jawnej – zalecane wykorzystanie wyłącznie w celach testowych!
 *  */
function PrzypomnijHaslo($odbiorca, $login, $pass)
{
  $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

  if (empty($email)) {
    PokazPrzypomnijHaslo('Podaj poprawny adres e-mail, aby przypomnieć hasło.');
    return;
  }

  $subject = "Przypomnienie hasła do panelu CMS";
  $body = "Login: $login\nHasło: $pass\n\nUWAGA: To przypomnienie jest przesyłane w formie niezaszyfrowanej – tylko do celów testowych!";
  $sender = "no-reply@japgotowanie.pl";

  $header = "From: System CMS <" . $sender . ">\n";
  $header .= "MIME-Version: 1.0\nContent-Type: text/plain; charset=utf-8\n";

  mail($email, $subject, $body, $header);

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

  <!-- ==========================
         Menu nawigacyjne
    ========================== -->
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

  <!-- ==========================
         Główna treść strony z mechanizmem obsługi formularza kontaktowego
    ========================== -->
  <main class="content">
    <?php
    $odbiorca = "kontakt@japgotowanie.pl";

    // Obsługa trybu kontakt/reset
    if (isset($_GET['tryb'])) {
      $_SESSION['tryb_kontaktu'] = $_GET['tryb'];
    }
    $tryb = $_SESSION['tryb_kontaktu'] ?? 'kontakt';

    // Obsługa formularzy
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