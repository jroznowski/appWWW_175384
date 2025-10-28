<?php
    $nr_indeksu='175384';
    $nr_grupy = '2';

    echo 'Jakub Rożnowski '.$nr_indeksu.' grupa '.$nr_grupy.'<br><br>';

    echo 'Zastosowanie metody include() - plik include_me.php i test zmiennych <br>';
    include 'include_me.php';
    echo 'Zaczytano kolor '.$kolor.'. Znane japońskie danie z pliku zewnętrznego: '.$danie.'.<br><br>';

    echo 'Zastosowanie metody require_once() - plik require_me.php i test zmiennej. <br>';
    require_once 'require_me.php';
    echo 'Często stosowany japoński sos: '.$sos.'.<br><br>';

    echo 'Zastosowanie metod if i else - test zmiennych wczytanych z plików zewnętrznych. <br>';
    if(isset($kolor))
        echo 'Zmienna $kolor załadowana poprawnie.<br><br>';
    else
        echo 'Zmienna $kolor nie została załadowana poprawnie<br><br>';

    echo 'Test instrukcji warunkowej dla zmiennej która nie została wczytana. <br>';
    if(isset($przepis))
        echo 'Zmienna $przepis załadowana poprawnie.<br><br>';
    else
        echo 'Zmienna $przepis nie została załadowana poprawnie<br><br>';

    echo 'Zastosowanie instrukcji elseif - porównanie zmiennych liczbowych <br>';
    if($liczba_1 > $liczba_2)
        echo $liczba_1. ' jest większe od '.$liczba_2.'<br><br>';
    elseif($liczba_2 > $liczba_1)
        echo $liczba_2. ' jest większe od '.$liczba_1.'<br><br>';
    else
        echo $liczba_1.' i '.$liczba_2.' są równe.<br><br>';

    echo 'Zastosowanie instrukcji warunkowej switch - zmienna $kraj. <br>';
    $kraj = 'Japonia';
    switch ($kraj){
        case 'Niemcy':
            echo 'Niemcy leżą w Europie.<br><br>';
            break;
        case 'Japonia':
            echo 'Japonia leży w Azji.<br><br>';
            break;
        case 'Argentyna':
            echo 'Argentyna leży w Ameryce Południowej.<br><br>';
            break;
    }

    echo 'Zastosowanie pętli for - zliczanie cyfr parzystych z numeru albumu <br>';
    $licznik_parzyste = 0;
    for($i=0; $i < strlen($nr_indeksu); $i++){
        if($nr_indeksu[$i] % 2 == 0)
            $licznik_parzyste++;
    }
    echo 'W numerze albumu '.$nr_indeksu.' znajdują się '.$licznik_parzyste.' cyfry parzyste.<br><br>';

    echo 'Zastosowanie pętli while - odliczanie od 3.<br>';
    $licznik=3;
    while($licznik>=0){
        echo 'Do startu pozostało '.$licznik.' sekund.<br>';
        $licznik--;
    }

    echo '<br>Zastosowanie $_GET - formularz wyświetlający przesłaną zmienną w adresie URL.<br>';
    echo '<form method="GET">
        Wiek: <input type="text" name="wiek"><br>
        <input type="submit">
        </form>';
    if (isset($_GET['wiek'])){
        $wiek = $_GET['wiek'];
        echo "W formularzu GET wprowadzono wiek: $wiek<br /><br>";
    } 
    else {
      echo "Brak wartości 'wiek' w zmiennej GET (formularz nie został jeszcze przetworzony - sprawdź URL).<br><br />";
    }


    echo 'Zastosowanie $_POST - formularz';
    echo '<form method="POST">
            Imie: <input type="text" name="imie"><br>
            <input type="submit">
            </form>';
    if (isset($_POST['imie'])) {
    $imie = $_POST['imie'];
    echo "Otrzymano z POST imię: $imie<br /><br>";
    } 
    else {
      echo "Brak wartości 'imie' w zmiennej POST (formularz nie został jeszcze przetworzony).<br /><br>";
    }

    echo 'Zastosowanie $_SESSION - wprowadzenie zmiennej sesji<br>';
    $_SESSION['przedmiot'] = "ProgramowanieAplikacjiWWW";
    echo 'Test zmiennej sesji nazwanej "przedmiot": '.$_SESSION['przedmiot']."<br />";

?>