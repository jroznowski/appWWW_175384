// ==========================
// Plik: kolorujtlo.js
// Opis: Skrypty obsługujące konwersję jednostek,
//       kalkulator wejściowy oraz zmianę tła.
// ==========================

// Flagi kontrolne
var computed = false; // Czy ostatnia operacja została obliczona
var decimal = 0; // Czy wprowadzono już znak dziesiętny

// ==========================
// Funkcja: convert
// Opis: Przelicza wartość z jednej jednostki na drugą
// Parametry:
//   entryform - formularz z polami input/display
//   from      - lista rozwijana jednostki źródłowej
//   to        - lista rozwijana jednostki docelowej
// ==========================
function convert(entryform, from, to) {
  convertfrom = from.selectedIndex;
  convertto = to.selectedIndex;
  entryform.display.value =
    (entryform.input.value * from[convertfrom].value) / to[convertto].value;
}

// ==========================
// Funkcja: addChar
// Opis: Dodaje znak (cyfrę lub kropkę) do pola input.
//       Obsługuje wstawianie znaku dziesiętnego.
// Parametry:
//   input     - pole tekstowe formularza
//   character - znak do dodania
// ==========================
function addChar(input, character) {
  if ((character == "." && decimal == 0) || character != ".") {
    input.value == "" || input.value == "0"
      ? (input.value = character)
      : (input.value += character);
    convert(input.form, input.form.measure1, input.form.measure2);
    computed = true;
    if (character == ".") {
      decimal = 1;
    }
  }
}

// ==========================
// Funkcja: openVothcon
// Opis: Otwiera nowe okno przeglądarki
//       (puste, bez pasków narzędzi).
// ==========================
function openVothcon() {
  window.open("", "Display window", "toolbar=no,directories=no,menubar=no");
}

// ==========================
// Funkcja: clear
// Opis: Resetuje wartości pól formularza
//       oraz flagę decimal.
// Parametry:
//   form - formularz z polami input/display
// ==========================
function clear(form) {
  form.input.value = 0;
  form.display.value = 0;
  decimal = 0;
}

// ==========================
// Funkcja: changeBackground
// Opis: Zmienia kolor tła wszystkich elementów
//       o klasie "box" na podany kolor HEX.
// Parametry:
//   hexNumber - kolor w formacie HEX (#RRGGBB)
// ==========================
function changeBackground(hexNumber) {
  Array.from(document.getElementsByClassName("box")).forEach(
    (box) => (box.style.backgroundColor = hexNumber)
  );
}
