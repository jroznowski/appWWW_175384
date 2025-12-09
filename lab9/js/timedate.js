// ==========================
// Plik: timedate.js
// Opis: Skrypt obsługujący wyświetlanie daty i zegara w czasie rzeczywistym.
// ==========================

// ==========================
// Funkcja: getTheDate
// Opis: Pobiera bieżącą datę i wyświetla ją w elemencie o ID "data".
// Format: MM / DD / YY (rok skrócony o 100 lat).
// ==========================
function getTheDate() {
  Todays = new Date();
  TheDate =
    "" +
    (Todays.getMonth() + 1) +
    " / " +
    Todays.getDate() +
    " / " +
    (Todays.getYear() - 100);
  document.getElementById("data").innerHTML = TheDate;
}

// ==========================
// Zmienne globalne do obsługi zegara
// timerID       → identyfikator timera (setTimeout)
// timerRunning  → flaga informująca, czy zegar działa
// ==========================
var timerID = null;
var timerRunning = false;

// ==========================
// Funkcja: stopClock
// Opis: Zatrzymuje zegar, jeśli jest uruchomiony.
// Czyści timer i ustawia flagę timerRunning na false.
// ==========================
function stopClock() {
  if (timerRunning) clearTimeout(timerID);
  timerRunning = false;
}

// ==========================
// Funkcja: startClock
// Opis: Uruchamia zegar.
// Najpierw zatrzymuje poprzedni timer,
// następnie wyświetla datę i uruchamia funkcję showtime().
// ==========================
function startClock() {
  stopClock();
  getTheDate();
  showtime();
}

// ==========================
// Funkcja: showtime
// Opis: Pobiera aktualny czas i wyświetla go w elemencie o ID "zegarek".
// Obsługuje system 24-godzinny (aktualnie wdrożony).
// Automatycznie odświeża się co 1 sekundę.
// ==========================
function showtime() {
  var now = new Date();
  var hours = now.getHours();
  var minutes = now.getMinutes();
  var seconds = now.getSeconds();

  /* 
    System 12-godzinny (przykład – obecnie nieaktywny):
    var timeValue = "" + ((hours > 12) ? hours - 12 : hours)
    */

  // System 24-godzinny – aktualnie wdrożony
  var timeValue = "" + hours;
  timeValue += (minutes < 10 ? ":0" : ":") + minutes;
  timeValue += (seconds < 10 ? ":0" : ":") + seconds;

  // Dodanie AM/PM (dla systemu 12-godzinnego – obecnie nieaktywne)
  // timeValue += (hours >= 12) ? " P.M." : " A.M.";

  document.getElementById("zegarek").innerHTML = timeValue;

  // Ustawienie timera na kolejne wywołanie showtime() po 1 sekundzie
  timerID = setTimeout("showtime()", 1000);
  timerRunning = true;
}
