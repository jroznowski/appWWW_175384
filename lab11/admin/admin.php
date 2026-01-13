<?php
// Plik: admin.php
// Opis: Panel administracyjny CMS – logowanie,
//       zarządzanie podstronami (lista, edycja, dodawanie, usuwanie),
//       zarządzanie kategoriami (pokazywanie, edycja, dodawanie,usuwanie).

session_start();
include('../cfg.php');
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

// Funkcja: FormularzLogowania
// Opis: Wyświetla formularz logowania do panelu CMS.
// Parametry:
//   $error – komunikat błędu (opcjonalny)
function FormularzLogowania($error = '')
{
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

// Obsługa logowania
// Sprawdza dane z formularza i ustawia sesję.
if (isset($_POST['log_submit'])) {
    if ($_POST['login_email'] === $login && $_POST['login_pass'] === $pass) {
        $_SESSION['zalogowany'] = true;
    } else {
        $error = "Błędny login lub hasło!";
    }
}

// Funkcja: ListaPodstron
// Opis: Wyświetla listę podstron z bazy danych.
// Parametry:
//   $link – połączenie z bazą
function ListaPodstron($link)
{
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
    echo " | <a href='?akcja=kategorie'>Zarządzaj kategoriami</a>";
    echo " | <a href='?akcja=produkty'>Zarządzaj produktami</a>";
    echo " | <a href='?akcja=wyloguj'>Wyloguj</a>";
    echo " | <a href='../index.php'>Powrót do strony głównej</a>";
    echo '</div>';
}

// Funkcja: EdytujPodstrone
// Opis: Formularz edycji podstrony i zapis zmian.
// Parametry:
//   $link – połączenie z bazą
//   $id   – ID podstrony
function EdytujPodstrone($link, $id)
{
    $query = "SELECT * FROM page_list WHERE id=" . intval($id);
    $result = mysqli_query($link, $query);

    //Wyświetlanie formularza edycji podstrony
    if ($row = mysqli_fetch_assoc($result)) {
        echo '<div class="box admin">';
        echo "<h2>Edytuj podstronę</h2>";
        echo '<form method="post" action="" class="admin-form">';
        echo 'Tytuł:<br><input type="text" name="tytul" value="' . htmlspecialchars($row['page_title']) . '"><br><br>';
        echo 'Treść:<br><textarea name="tresc" rows="10" cols="50">' . htmlspecialchars($row['page_content']) . '</textarea><br><br>';
        echo 'Aktywna: <input type="checkbox" name="aktywna" ' . ($row['status'] ? 'checked' : '') . '><br><br>';
        echo '<input type="submit" name="zapisz" value="Zapisz zmiany">';
        echo '</form>';

        // Obsługa zapisu zmian
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

// Funkcja: DodajNowaPodstrone
// Opis: Formularz dodawania nowej podstrony i zapis do bazy.
// Parametry:
//   $link – połączenie z bazą
function DodajNowaPodstrone($link)
{
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

    // Obsługa dodania nowej podstrony
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

// Funkcja: UsunPodstrone
// Opis: Usuwa podstronę z bazy danych.
// Parametry:
//   $link – połączenie z bazą
//   $id   – ID podstrony
function UsunPodstrone($link, $id)
{
    $id = intval($id);
    $delete = "DELETE FROM page_list WHERE id=$id";
    mysqli_query($link, $delete);

    echo '<div class="box admin">';
    echo "<h2>Usuwanie podstrony</h2>";
    echo "<p>Podstrona o ID $id została usunięta.</p>";
    echo "<br><a class='powrot' href='admin.php'>Powrót do listy</a>";
    echo '</div>';
}

// ==========================
// Funkcje zarządzania kategoriami
// ==========================

// Funkcja: PokazKategorie
// Opis: Wyświetla drzewo kategorii w postaci listy zagnieżdżonej (<ul><li>),
//       odwzorowując relacje między podstronami na podstawie pola "matka". Rekurencją w funkcji
//       zajmuje się funkcja dodatkowa RenderujKategorie.
// Parametry:
//   $link – połączenie z bazą danych (mysqli)
function PokazKategorie($link)
{
    echo '<div class="box admin">';
    echo '<h2>Drzewo kategorii</h2>';

    $sql = "SELECT id, nazwa, matka FROM categories ORDER BY nazwa LIMIT 1000";
    $result = mysqli_query($link, $sql);

    if (!$result) {
        echo "<p class='error'>Błąd zapytania SQL.</p>";
        echo '</div>';
        return;
    }

    //Zapis do tablicy kategorii
    $kategorie = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $id = (int) $row['id'];
        $kategorie[$id] = [
            'id' => $id,
            'nazwa' => $row['nazwa'],
            'matka' => (int) $row['matka'],
            'dzieci' => []
        ];
    }

    //Budowanie drzewa kategorii
    $drzewo = [];

    foreach ($kategorie as $id => &$kat) {
        // kategorie główne
        if ($kat['matka'] === 0) {
            $drzewo[$id] = &$kat;
        } else {
            // podkategorie – tylko jeśli matka istnieje
            if (isset($kategorie[$kat['matka']]) && $kat['matka'] !== $id) {
                $kategorie[$kat['matka']]['dzieci'][$id] = &$kat;
            } else {
                // awaryjnie traktuj jako kategorię główną
                $drzewo[$id] = &$kat;
            }
        }
    }
    unset($kat); // usunięcie referencji


    // Funkcja: RenderujKategorie
    // Opis: Rekurencyjnie generuje i wyświetla drzewo kategorii w formacie listy HTML (<ul><li>),
    //       bazując na przekazanej tablicy kategorii wraz z ich dziećmi.
    // Parametry:
    //   $kategorie – tablica kategorii wykorzystywana do utworzenia odpowiedniej struktury drzewa
    function RenderujKategorie($kategorie)
    {
        echo "<ul>";

        foreach ($kategorie as $kat) {
            echo "<li>";
            echo htmlspecialchars($kat['nazwa'], ENT_QUOTES, 'UTF-8');
            echo " (ID: " . (int) $kat['id'] . ")";
            echo " [<a href='?akcja=edytuj_kat&id=" . (int) $kat['id'] . "'>Edytuj</a> | ";
            echo "<a href='?akcja=usun_kat&id=" . (int) $kat['id'] . "' ";
            echo "onclick=\"return confirm('Na pewno usunąć kategorię?');\">Usuń</a>]";

            // Rekurencyjne wyświetlenie dzieci
            if (!empty($kat['dzieci'])) {
                RenderujKategorie($kat['dzieci']);
            }

            echo "</li>";
        }

        echo "</ul>";
    }

    if (!empty($drzewo)) {
        echo '<div class="category-tree">';
        RenderujKategorie($drzewo);
        echo '</div>';
    } else {
        echo "<p>Brak kategorii do wyświetlenia.</p>";
    }

    echo "<br><a href='?akcja=dodaj_kat'>Dodaj nową kategorię</a>";
    echo " | <a href='admin.php'>Zarządzaj podstronami</a>";
    echo " | <a href='?akcja=produkty'>Zarządzaj produktami</a>";
    echo " | <a href='?akcja=dodaj_produkt'>Dodaj produkt</a>";
    echo " | <a href='?akcja=wyloguj'>Wyloguj</a>";
    echo " | <a href='../index.php'>Powrót do strony głównej</a>";
    echo '</div>';
}


// Funkcja: DodajKategorie
// Opis: Formularz dodawania nowej kategorii i zapis do bazy.
// Parametry:
//   $link – połączenie z bazą
function DodajKategorie($link)
{
    echo '<div class="box admin">';
    echo "<h2>Dodaj nową kategorię</h2>";
    echo '<form method="post" action="" class="admin-form">';
    echo 'Nazwa:<br><input type="text" name="nazwa"><br><br>';
    echo 'Matka (ID kategorii nadrzędnej, 0 = główna):<br><input type="number" name="matka" value="0"><br><br>';
    echo '<input type="submit" name="dodaj_kat" value="Dodaj kategorię">';
    echo "<br><br><a class='powrot' href='admin.php?akcja=kategorie'>Powrót do listy kategorii</a>";
    echo '</form>';

    if (isset($_POST['dodaj_kat'])) {
        $nazwa = mysqli_real_escape_string($link, $_POST['nazwa']);
        $matka = intval($_POST['matka']);
        $insert = "INSERT INTO categories (nazwa, matka) VALUES ('$nazwa', '$matka')";
        mysqli_query($link, $insert);
        echo "<p>Kategoria została dodana pomyślnie.</p>";
    }
    echo '</div>';
}

// Funkcja: EdytujKategorie
// Opis: Formularz edycji kategorii i zapis zmian.
// Parametry:
//   $link – połączenie z bazą
//   $id   – ID kategorii
function EdytujKategorie($link, $id)
{
    $query = "SELECT * FROM categories WHERE id=" . intval($id);
    $result = mysqli_query($link, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        echo '<div class="box admin">';
        echo "<h2>Edytuj kategorię</h2>";
        echo '<form method="post" action="" class="admin-form">';
        echo 'Nazwa:<br><input type="text" name="nazwa" value="' . htmlspecialchars($row['nazwa']) . '"><br><br>';
        echo 'Matka:<br><input type="number" name="matka" value="' . intval($row['matka']) . '"><br><br>';
        echo '<input type="submit" name="zapisz_kat" value="Zapisz zmiany">';
        echo '</form>';

        if (isset($_POST['zapisz_kat'])) {
            $nazwa = mysqli_real_escape_string($link, $_POST['nazwa']);
            $matka = intval($_POST['matka']);
            $update = "UPDATE categories SET nazwa='$nazwa', matka='$matka' WHERE id=" . intval($id);
            mysqli_query($link, $update);
            echo "<p>Zmiany zostały zapisane pomyślnie.</p>";
        }

        echo "<br><a class='powrot' href='admin.php?akcja=kategorie'>Powrót do listy kategorii</a>";
        echo '</div>';
    } else {
        echo '<div class="box admin"><p>Nie znaleziono kategorii.</p></div>';
    }
}

// Funkcja: UsunKategorie
// Opis: Usuwa kategorię z bazy danych.
// Parametry:
//   $link – połączenie z bazą
//   $id   – ID kategorii
function UsunKategorie($link, $id)
{
    $id = intval($id);

    // Sprawdzenie, czy kategoria ma dzieci
    $check = mysqli_query($link, "SELECT id FROM categories WHERE matka=$id LIMIT 1");
    if (mysqli_num_rows($check) > 0) {
        echo '<div class="box admin">';
        echo "<p class='error'>Nie można usunąć kategorii, która posiada podkategorie/dzieci.</p>";
        echo "<a class='powrot' href='admin.php?akcja=kategorie'>Powrót</a>";
        echo '</div>';
        return;
    }

    mysqli_query($link, "DELETE FROM categories WHERE id=$id");

    echo '<div class="box admin">';
    echo "<p>Kategoria została usunięta.</p>";
    echo "<a class='powrot' href='admin.php?akcja=kategorie'>Powrót</a>";
    echo '</div>';
}

// DodajProdukt()
// Funkcja dodaje nowy produkt do bazy danych.
// Powiązana z formularzem dodawania produktu w panelu admina.
// Parametry:
//   $link – połączenie z bazą
function DodajProdukt($link)
{
    echo '<div class="box admin">';
    echo "<h2>Dodaj nowy produkt</h2>";
    echo '<form method="post" action="" class="admin-form" enctype="multipart/form-data">';

    echo 'Tytuł:<br><input type="text" name="tytul"><br><br>';
    echo 'Opis:<br><textarea name="opis"></textarea><br><br>';
    echo 'Data wygaśnięcia:<br><input type="date" name="data_wygasniecia"><br><br>';
    echo 'Cena netto:<br><input type="number" step="0.01" name="cena_netto"><br><br>';
    echo 'Podatek VAT (%):<br><input type="number" name="podatek_vat" value="23"><br><br>';
    echo 'Ilość sztuk:<br><input type="number" name="ilosc_sztuk"><br><br>';
    echo 'Status dostępności:<br>
          <select name="status_dostepnosci">
              <option value="1">Dostępny</option>
              <option value="0">Niedostępny</option>
          </select><br><br>';
    echo 'Kategoria (ID):<br><input type="number" name="kategoria"><br><br>';
    echo 'Gabaryt (ID):<br><input type="number" name="gabaryt"><br><br>';
    echo 'Zdjęcie produktu:<br><input type="file" name="zdjecie"><br><br>';

    echo '<input type="submit" name="dodaj_produkt" value="Dodaj produkt">';
    echo "<br><br><a class='powrot' href='admin.php?akcja=produkty'>Powrót do listy produktów</a>";
    echo '</form>';

    if (isset($_POST['dodaj_produkt'])) {

        $tytul = mysqli_real_escape_string($link, $_POST['tytul']);
        $opis = mysqli_real_escape_string($link, $_POST['opis']);
        $data_utw = date('Y-m-d');
        $data_mod = date('Y-m-d');
        $data_wyg = mysqli_real_escape_string($link, $_POST['data_wygasniecia']);
        $cena_netto = floatval($_POST['cena_netto']);
        $vat = intval($_POST['podatek_vat']);
        $ilosc = intval($_POST['ilosc_sztuk']);
        $status = intval($_POST['status_dostepnosci']);
        $kategoria = intval($_POST['kategoria']);
        $gabaryt = intval($_POST['gabaryt']);

        // Zdjęcie (może być NULL)
        $zdjecie = '';
        if (!empty($_FILES['zdjecie']['tmp_name'])) {
            $zdjecie = addslashes(file_get_contents($_FILES['zdjecie']['tmp_name']));
        }

        $insert = "INSERT INTO products 
        (tytul, opis, data_utworzenia, data_modyfikacji, data_wygasniecia, cena_netto, podatek_vat, ilosc_sztuk, status_dostepnosci, kategoria, gabaryt, zdjecie)
        VALUES 
        ('$tytul', '$opis', '$data_utw', '$data_mod', '$data_wyg', '$cena_netto', '$vat', '$ilosc', '$status', '$kategoria', '$gabaryt', '$zdjecie')";

        mysqli_query($link, $insert);

        echo "<p>Produkt został dodany pomyślnie.</p>";
    }

    echo '</div>';
}


// UsunProdukt()
// Usuwa produkt na podstawie przekazanego ID.
// Wywoływana z admin.php: case 'usun_produkt'.
// Parametry:
//   $link – połączenie z bazą
//   $id - id produktu
function UsunProdukt($link, $id)
{
    $id = intval($id);

    mysqli_query($link, "DELETE FROM products WHERE id=$id");

    echo '<div class="box admin">';
    echo "<p>Produkt został usunięty.</p>";
    echo "<a class='powrot' href='admin.php?akcja=produkty'>Powrót</a>";
    echo '</div>';
}



// EdytujProdukt()
// Aktualizuje dane produktu.
// Powiązane z formularzem edycji produktu.
// Parametry:
//   $link – połączenie z bazą
//   $id - id produktu
function EdytujProdukt($link, $id)
{
    $query = "SELECT * FROM products WHERE id=" . intval($id);
    $result = mysqli_query($link, $query);

    if ($row = mysqli_fetch_assoc($result)) {

        echo '<div class="box admin">';
        echo "<h2>Edytuj produkt</h2>";
        echo '<form method="post" action="" class="admin-form" enctype="multipart/form-data">';

        echo 'Tytuł:<br><input type="text" name="tytul" value="' . htmlspecialchars($row['tytul']) . '"><br><br>';
        echo 'Opis:<br><textarea name="opis">' . htmlspecialchars($row['opis']) . '</textarea><br><br>';
        echo 'Data wygaśnięcia:<br><input type="date" name="data_wygasniecia" value="' . $row['data_wygasniecia'] . '"><br><br>';
        echo 'Cena netto:<br><input type="number" step="0.01" name="cena_netto" value="' . $row['cena_netto'] . '"><br><br>';
        echo 'Podatek VAT (%):<br><input type="number" name="podatek_vat" value="' . $row['podatek_vat'] . '"><br><br>';
        echo 'Ilość sztuk:<br><input type="number" name="ilosc_sztuk" value="' . $row['ilosc_sztuk'] . '"><br><br>';

        echo 'Status dostępności:<br>
              <select name="status_dostepnosci">
                  <option value="1" ' . ($row['status_dostepnosci'] ? "selected" : "") . '>Dostępny</option>
                  <option value="0" ' . (!$row['status_dostepnosci'] ? "selected" : "") . '>Niedostępny</option>
              </select><br><br>';

        echo 'Kategoria (ID):<br><input type="number" name="kategoria" value="' . $row['kategoria'] . '"><br><br>';
        echo 'Gabaryt (ID):<br><input type="number" name="gabaryt" value="' . $row['gabaryt'] . '"><br><br>';

        echo 'Zdjęcie (pozostaw puste, aby nie zmieniać):<br><input type="file" name="zdjecie"><br><br>';

        echo '<input type="submit" name="zapisz_produkt" value="Zapisz zmiany">';
        echo '</form>';

        if (isset($_POST['zapisz_produkt'])) {

            $tytul = mysqli_real_escape_string($link, $_POST['tytul']);
            $opis = mysqli_real_escape_string($link, $_POST['opis']);
            $data_mod = date('Y-m-d');
            $data_wyg = mysqli_real_escape_string($link, $_POST['data_wygasniecia']);
            $cena_netto = floatval($_POST['cena_netto']);
            $vat = intval($_POST['podatek_vat']);
            $ilosc = intval($_POST['ilosc_sztuk']);
            $status = intval($_POST['status_dostepnosci']);
            $kategoria = intval($_POST['kategoria']);
            $gabaryt = intval($_POST['gabaryt']);

            // Zdjęcie — tylko jeśli przesłano nowe
            $zdjecie_sql = "";
            if (!empty($_FILES['zdjecie']['tmp_name'])) {
                $zdjecie = addslashes(file_get_contents($_FILES['zdjecie']['tmp_name']));
                $zdjecie_sql = ", zdjecie='$zdjecie'";
            }

            $update = "UPDATE products SET 
                        tytul='$tytul',
                        opis='$opis',
                        data_modyfikacji='$data_mod',
                        data_wygasniecia='$data_wyg',
                        cena_netto='$cena_netto',
                        podatek_vat='$vat',
                        ilosc_sztuk='$ilosc',
                        status_dostepnosci='$status',
                        kategoria='$kategoria',
                        gabaryt='$gabaryt'
                        $zdjecie_sql
                       WHERE id=" . intval($id);

            mysqli_query($link, $update);
            echo "<p>Zmiany zostały zapisane pomyślnie.</p>";
        }

        echo "<br><a class='powrot' href='admin.php?akcja=produkty'>Powrót do listy produktów</a>";
        echo '</div>';

    } else {
        echo '<div class="box admin"><p>Nie znaleziono produktu.</p></div>';
    }
}


// PokazProdukty()
// Wyświetla listę produktów w panelu admina.
// Parametry:
//   $link – połączenie z bazą
function PokazProdukty($link)
{
    echo '<div class="box admin">';
    echo "<h2>Lista produktów</h2>";

    $sql = "SELECT id, tytul, opis, cena_netto, podatek_vat, ilosc_sztuk, status_dostepnosci,
                   kategoria, gabaryt, data_utworzenia, data_modyfikacji, data_wygasniecia, zdjecie
            FROM products
            ORDER BY id ASC
            LIMIT 100";

    $result = mysqli_query($link, $sql);

    echo "<table class='admin-table'>
            <tr>
                <th>ID</th>
                <th>Zdjęcie</th>
                <th>Tytuł</th>
                <th>Opis</th>
                <th>Cena netto</th>
                <th>VAT</th>
                <th>Ilość</th>
                <th>Status</th>
                <th>Kategoria</th>
                <th>Gabaryt</th>
                <th>Data dodania</th>
                <th>Data modyfikacji</th>
                <th>Data wygaśnięcia</th>
                <th>Akcje</th>
            </tr>";

    while ($row = mysqli_fetch_assoc($result)) {

        // Miniatura zdjęcia z BLOB
        if (!empty($row['zdjecie'])) {
            $img = base64_encode($row['zdjecie']);
            $thumb = "<img src='data:image/jpeg;base64,$img' style='width:80px;height:80px;object-fit:cover;border:1px solid #ccc;'>";
        } else {
            $thumb = "<div style='width:80px;height:80px;background:#eee;border:1px solid #ccc;display:flex;align-items:center;justify-content:center;font-size:10px;color:#666;'>brak</div>";
        }

        echo "<tr>
                <td>{$row['id']}</td>
                <td>$thumb</td>
                <td>{$row['tytul']}</td>
                <td>{$row['opis']}</td>
                <td>{$row['cena_netto']}</td>
                <td>{$row['podatek_vat']}%</td>
                <td>{$row['ilosc_sztuk']}</td>
                <td>" . ($row['status_dostepnosci'] ? "Dostępny" : "Niedostępny") . "</td>
                <td>{$row['kategoria']}</td>
                <td>{$row['gabaryt']}</td>
                <td>{$row['data_utworzenia']}</td>
                <td>{$row['data_modyfikacji']}</td>
                <td>{$row['data_wygasniecia']}</td>
                <td>
                    <a href='?akcja=edytuj_produkt&id={$row['id']}'>Edytuj</a> |
                    <a href='?akcja=usun_produkt&id={$row['id']}' onclick=\"return confirm('Na pewno usunąć?');\">Usuń</a>
                </td>
              </tr>";
    }

    echo "</table>";

    echo "<br><a href='?akcja=dodaj_produkt'>Dodaj produkt</a>";
    echo " | <a href='?akcja=kategorie'>Zarządzaj kategoriami</a>";
    echo " | <a href='admin.php'>Zarządzaj podstronami</a>";
    echo " | <a href='?akcja=wyloguj'>Wyloguj</a>";
    echo " | <a href='../index.php'>Powrót do strony głównej</a>";

    echo '</div>';
}

?>
<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <title>Panel administracyjny</title>
    <!-- ==========================
       Style CSS
       ========================== -->
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body>

    <!-- ==========================
       Nagłówek strony
       ========================== -->
    <header>
        <h1>Kuchnia japońska</h1>
    </header>

    <!-- ==========================
       Nawigacja główna
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
            <li><a href="admin.php" class="active">Panel CMS</a></li>
            <li><a href="../php/contact.php">Kontakt</a></li>
        </ul>
    </nav>

    <!-- ==========================
       Główna treść strony
       ========================== -->
    <main class="content">
        <?php
        // Jeśli użytkownik nie jest zalogowany → pokaż formularz logowania
        if (!isset($_SESSION['zalogowany']) || $_SESSION['zalogowany'] !== true) {
            echo FormularzLogowania($error ?? '');
            exit;
        }
        // Jeśli użytkownik jest zalogowany kod przechodzi do obsługi akcji
        else {
            if (isset($_GET['akcja'])) {
                switch ($_GET['akcja']) {
                    // --- PODSTRONY ---
                    case 'edytuj':
                        if (isset($_GET['id']))
                            EdytujPodstrone($link, $_GET['id']);
                        break;
                    case 'dodaj':
                        DodajNowaPodstrone($link);
                        break;
                    case 'usun':
                        if (isset($_GET['id']))
                            UsunPodstrone($link, $_GET['id']);
                        break;

                    // --- KATEGORIE ---
                    case 'kategorie':
                        PokazKategorie($link); // rekurencyjne drzewo
                        break;
                    case 'dodaj_kat':
                        DodajKategorie($link);
                        break;
                    case 'edytuj_kat':
                        if (isset($_GET['id']))
                            EdytujKategorie($link, $_GET['id']);
                        break;
                    case 'usun_kat':
                        if (isset($_GET['id']))
                            UsunKategorie($link, $_GET['id']);
                        break;

                    // --- PRODUKTY ---
                    case 'produkty':
                        PokazProdukty($link);
                        break;

                    case 'dodaj_produkt':
                        DodajProdukt($link);
                        break;

                    case 'edytuj_produkt':
                        if (isset($_GET['id']))
                            EdytujProdukt($link, $_GET['id']);
                        break;

                    case 'usun_produkt':
                        if (isset($_GET['id']))
                            UsunProdukt($link, $_GET['id']);
                        break;
                    // --- WYLOGOWANIE ---
                    case 'wyloguj':
                        session_destroy();
                        header("Location: admin.php");
                        exit;

                    // --- DOMYŚLNIE: podstrony ---
                    default:
                        ListaPodstron($link);
                }

            } else {
                // Domyślnie pokaż listę podstron
                ListaPodstron($link);
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