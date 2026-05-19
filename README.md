# Cinema Booking App

Aplikacja webowa do rezerwacji miejsc w kinie. Projekt powstał jako pełny flow zakupowy: od wyboru kina i seansu, przez zaznaczenie miejsc na sali, podsumowanie rezerwacji, płatność, aż po historię zakupów i wiadomość e-mail z biletem.

Najważniejszym elementem technicznym projektu jest obsługa tymczasowej blokady miejsc w Redis. Po kliknięciu fotela aplikacja zakłada 5-minutowy hold na konkretne miejsce, seans i kino. Dzięki temu drugi użytkownik nie może w tym samym czasie zaznaczyć tego samego miejsca.

## Główne funkcje

- wybór kina i przeglądanie dostępnych seansów,
- interaktywny widok sali kinowej z miejscami dostępnymi, zajętymi i tymczasowo zablokowanymi,
- rezerwacja miejsc dla zalogowanych użytkowników i gości,
- podsumowanie rezerwacji z wyliczeniem ceny biletów,
- integracja z PayU dla płatności online,
- obsługa statusów płatności i potwierdzenia rezerwacji,
- historia zakupów dla zalogowanego użytkownika,
- wysyłka biletu/potwierdzenia na adres e-mail,
- testy feature i unit dla kluczowych części procesu.

## Hold miejsc w Redis

W standardowej rezerwacji kinowej największym problemem jest konflikt, w którym dwie osoby próbują wybrać ten sam fotel przed finalnym zapisem rezerwacji w bazie. W tym projekcie rozwiązuje to warstwa holdów oparta o Redis.

Mechanizm działa tak:

1. Użytkownik klika miejsce na sali.
2. Backend sprawdza, czy miejsce nie jest już zapisane jako kupione/zarezerwowane w bazie.
3. Aplikacja tworzy w Redis klucz dla kombinacji: kino, seans i miejsce.
4. Redis zapisuje hold tylko wtedy, gdy taki klucz jeszcze nie istnieje.
5. Hold wygasa automatycznie po 300 sekundach.
6. Jeśli inny użytkownik spróbuje zaznaczyć to samo miejsce w czasie aktywnego holdu, dostaje odpowiedź konfliktu i frontend oznacza miejsce jako niedostępne.

Technicznie blokada używa atomowego zapisu Redis z opcją `NX` i TTL:

```php
Redis::client()->set(
    key: $key,
    value: json_encode($payload, JSON_THROW_ON_ERROR),
    options: ['NX', 'EX' => 300]
);
```

`NX` sprawia, że Redis utworzy klucz tylko wtedy, gdy jeszcze nie istnieje. To jest kluczowe, bo operacja jest atomowa i zabezpiecza wybór miejsca nawet wtedy, gdy dwóch użytkowników kliknie ten sam fotel niemal jednocześnie.

Hold jest przypisany do identyfikatora właściciela: ID zalogowanego użytkownika albo tokenu gościa. Przy finalizacji rezerwacji backend ponownie sprawdza, czy wybrane miejsca nadal są trzymane przez tego samego użytkownika. Dopiero wtedy tworzy booking i zapisuje zajęte miejsca w bazie.

## Technologie

- Laravel 13,
- PHP 8.5,
- Inertia.js 3,
- React 19,
- TypeScript,
- Tailwind CSS 4,
- Redis,
- PHPUnit,

## Architektura

Backend jest oparty o kontrolery, komendy aplikacyjne, repozytoria i osobne klasy wspierające logikę domenową. Logika wyboru oraz zwalniania miejsc znajduje się w warstwie komend i serwisów:

- `SeatHold` sprawdza dostępność miejsca i próbuje założyć hold,
- `SeatRelease` zwalnia hold należący do aktualnego użytkownika,
- `SeatHoldService` zapisuje i usuwa blokady w Redis,
- `SeatHoldStore` odczytuje aktywne holdy dla widoku sali,
- `CreateReservationHandler` waliduje holdy przed utworzeniem rezerwacji.

Frontend jest zbudowany jako aplikacja Inertia + React. Widok rezerwacji komunikuje się z backendem przez wygenerowane funkcje Wayfinder, dzięki czemu trasy nie są wpisywane ręcznie w komponentach.

## TODO

- [ ] zwalnianie miejsc, jeżeli płatność się nie powiedzie
- [ ] refactor testów
- [ ] generowanie QR code do biletu
