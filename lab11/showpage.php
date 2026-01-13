<?php
include 'cfg.php';

/* Plik showpage.php
   Funkcja: Wyświetlanie treści podstron z bazy danych na podstawie ID przekazywanego z index.php
*/

/**
 * Funkcja: PokazPodstrone
 * Pobiera treść podstrony z bazy danych na podstawie ID prezkazywanego przez wybrane idp z index.php.
 * Zabezpieczenie: SQL Injection ograniczenie.
 *
 * @param string $id identyfikator podstrony
 * @return string treść strony lub komunikat o błędzie
 */
function PokazPodstrone($id)
{
    global $link;

    // Oczyszczenie parametru
    $id_clear = htmlspecialchars($id);

    // Przygotowanie zapytania SQL z parametrem zastępczym `?`
    // Zabezpieczenie przeciwko SQL injection
    $stmt = mysqli_prepare($link, "SELECT page_content FROM page_list WHERE id=? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $id_clear);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_array($result);

    if (empty($row['page_content'])) {
        $web = '[nie_znaleziono_strony]';
    } else {
        $web = $row['page_content'];
    }

    return $web;
}

// Wywołanie funkcji
echo PokazPodstrone($strona);
?>