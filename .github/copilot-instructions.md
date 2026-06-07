# Copilot Instructions — KinoWita Symfony Project

## Cel projektu

Projekt to aplikacja webowa w Symfony do zarządzania siecią kin oraz rezerwacji seansów filmowych przez klientów.

Aplikacja musi realizować temat w pełnym zakresie:

- publiczne przeglądanie repertuaru,
- filtrowanie seansów po placówce, gatunku i dacie,
- wyszukiwanie filmów po tytule,
- szczegóły filmu z listą najbliższych seansów,
- rezerwacja miejsc przez zalogowanego użytkownika,
- anulowanie własnych rezerwacji,
- panel pracownika placówki,
- panel administratora,
- statystyki placówki i statystyki całej sieci.

Projekt ma być wykonany tak, aby spełniał kryteria oceny na maksymalną liczbę punktów.

---

## Architektura projektu

Używaj klasycznego wzorca MVC zgodnego z Symfony:

- **Model**: encje Doctrine w `src/Entity`
- **View**: szablony Twig w `templates`
- **Controller**: kontrolery w `src/Controller`

Dodatkowo stosuj:

- **Service Layer** dla logiki biznesowej,
- **Repository Pattern** dla zapytań Doctrine,
- **Form Classes** dla formularzy Symfony,
- **Security Voters** dla kontroli dostępu do danych użytkownika i placówki.

Nie umieszczaj logiki biznesowej bezpośrednio w kontrolerach ani w szablonach Twig.

Kontrolery mają być cienkie. Powinny tylko:

- odbierać request,
- wywoływać serwis,
- przekazywać dane do widoku,
- wykonywać redirect.

---

## Struktura katalogów

Stosuj następującą strukturę:

```txt
src/
  Controller/
    Public/
    User/
    Staff/
    Admin/
  Entity/
  Repository/
  Form/
  Service/
  Security/
    Voter/
  DataFixtures/

templates/
  public/
  user/
  staff/
  admin/
  movie/
  screening/
  reservation/
  base.html.twig

assets/
  styles/
    app.css
```

Role użytkowników

System musi obsługiwać minimum trzy role:

ROLE_USER
ROLE_STAFF
ROLE_ADMIN

Znaczenie ról:

ROLE_USER — zwykły zalogowany klient,
ROLE_STAFF — pracownik przypisany do konkretnej placówki,
ROLE_ADMIN — administrator całej sieci.

Każdy użytkownik może mieć tylko dostęp do danych zgodnych ze swoją rolą.

Wymagania bezpieczeństwa:

użytkownik widzi tylko swoje rezerwacje,
użytkownik może anulować tylko swoje rezerwacje,
pracownik widzi i edytuje tylko dane swojej placówki,
administrator ma dostęp do całej aplikacji.

Do kontroli dostępu stosuj:

#[IsGranted(...)],
security.yaml,
Votery, np.:
ReservationVoter,
CinemaVoter,
ScreeningVoter.
Główne encje Doctrine

Projekt musi mieć minimum trzy modele będące w różnych relacjach. Docelowo zastosuj poniższe encje.

User

Reprezentuje konto użytkownika.

Pola:

id
email
password
roles
firstName
lastName
assignedCinema — opcjonalna relacja do Cinema dla pracownika

Relacje:

User many-to-one Cinema
User one-to-many Reservation
Cinema

Reprezentuje placówkę kina.

Pola:

id
name
city
address
description

Relacje:

Cinema one-to-many Hall
Cinema one-to-many User
Hall

Reprezentuje salę kinową.

Pola:

id
name
rowsCount
seatsPerRow

Relacje:

Hall many-to-one Cinema
Hall one-to-many Seat
Hall one-to-many Screening
Seat

Reprezentuje pojedyncze miejsce w sali.

Pola:

id
rowNumber
seatNumber

Relacje:

Seat many-to-one Hall

Dodaj unikalność:

jedno miejsce o danym rowNumber i seatNumber może istnieć tylko raz w jednej sali.
Movie

Reprezentuje film.

Pola:

id
title
description
durationMinutes
ageRestriction
poster
releaseYear

Relacje:

Movie many-to-many Genre
Movie one-to-many Screening
Genre

Reprezentuje gatunek filmu.

Pola:

id
name

Relacje:

Genre many-to-many Movie
Screening

Reprezentuje konkretny seans.

Pola:

id
startsAt
basePrice

Relacje:

Screening many-to-one Movie
Screening many-to-one Hall
Screening one-to-many Reservation
Reservation

Reprezentuje rezerwację użytkownika.

Pola:

id
createdAt
status
totalPrice

Statusy:

active
cancelled

Relacje:

Reservation many-to-one User
Reservation many-to-one Screening
Reservation one-to-many ReservationSeat
ReservationSeat

Reprezentuje zarezerwowane miejsce w ramach rezerwacji.

Pola:

id

Relacje:

ReservationSeat many-to-one Reservation
ReservationSeat many-to-one Seat
ReservationSeat many-to-one Screening

Dodaj zabezpieczenie przed podwójną rezerwacją:

jedno miejsce nie może być zarezerwowane dwa razy na ten sam seans.
ORM i zapytania Doctrine

Projekt musi używać Doctrine ORM nie tylko do prostego CRUD, ale też do zaawansowanych operacji.

W repository stosuj QueryBuilder.

Wymagane metody:

ScreeningRepository
findRepertoire(?Cinema $cinema, ?Genre $genre, ?DateTimeInterface $date, ?string $search): array

Ma obsługiwać:

filtrowanie po placówce,
filtrowanie po gatunku,
filtrowanie po dacie,
wyszukiwanie filmu po tytule,
sortowanie po dacie seansu.
ReservationRepository
findUserReservations(User $user): array
findCinemaReservations(Cinema $cinema): array
countReservationsForScreening(Screening $screening): int
sumRevenueForCinema(Cinema $cinema): float
StatisticsRepository albo osobny StatisticsService

Dla pracownika:

liczba seansów w placówce,
liczba aktywnych rezerwacji,
przychód z rezerwacji,
najpopularniejsze filmy w placówce.

Dla administratora:

liczba placówek,
liczba użytkowników,
liczba wszystkich rezerwacji,
przychód całej sieci,
najpopularniejsze filmy globalnie.

Stosuj:

JOIN,
COUNT,
SUM,
GROUP BY,
filtrowanie po dacie.
Serwisy

Logikę biznesową umieszczaj w serwisach.

Wymagane serwisy:

ReservationService
ScreeningService
CinemaStatisticsService
NetworkStatisticsService
SeatMapService
ReservationService

Odpowiada za:

tworzenie rezerwacji,
sprawdzenie dostępności miejsc,
blokowanie podwójnej rezerwacji miejsca,
wyliczenie ceny,
anulowanie rezerwacji.

Nie pozwalaj rezerwować:

miejsc już zajętych,
seansów z przeszłości,
miejsc spoza sali danego seansu.
SeatMapService

Odpowiada za:

wygenerowanie mapy miejsc dla sali,
oznaczenie miejsc wolnych,
oznaczenie miejsc zajętych,
zwrócenie danych do widoku Twig.
Walidacja

Stosuj walidatory Symfony w encjach oraz formularzach.

Walidacja musi wynikać z logiki biznesowej aplikacji.

Przykłady wymaganych walidatorów:

Movie
title: NotBlank, Length
description: NotBlank, Length
durationMinutes: NotBlank, Positive
releaseYear: Range
ageRestriction: PositiveOrZero
Genre
name: NotBlank, Length
Cinema
name: NotBlank, Length
city: NotBlank, Length
address: NotBlank, Length
Hall
name: NotBlank
rowsCount: Positive
seatsPerRow: Positive
Seat
rowNumber: Positive
seatNumber: Positive
Screening
startsAt: NotBlank
basePrice: Positive
movie: NotNull
hall: NotNull

Nie pozwalaj tworzyć seansów w przeszłości.

Reservation
user: NotNull
screening: NotNull
status: Choice
totalPrice: PositiveOrZero
Widoki publiczne

W części publicznej utwórz:

/public/repertoire
/public/movies
/public/movies/{id}

Funkcjonalności:

lista repertuaru,
filtrowanie po placówce,
filtrowanie po gatunku,
filtrowanie po dacie,
wyszukiwarka po tytule,
szczegóły filmu,
lista najbliższych seansów filmu.
Panel użytkownika

Ścieżki:

/user/reservations
/user/reservations/{id}

Funkcjonalności:

lista własnych rezerwacji,
szczegóły rezerwacji,
anulowanie rezerwacji.

Użytkownik nie może zobaczyć cudzej rezerwacji.

Rezerwacja miejsc

Ścieżka:

/screenings/{id}/reserve

Funkcjonalności:

widok mapy sali,
wybór jednego lub kilku miejsc,
pokazanie miejsc zajętych,
utworzenie rezerwacji,
zapisanie rezerwacji w bazie.

Ważne:

miejsce może być zajęte tylko raz na konkretny seans,
po anulowaniu rezerwacji miejsce może wrócić jako dostępne.
Panel pracownika placówki

Ścieżki:

/staff/screenings
/staff/halls
/staff/reservations
/staff/statistics

Pracownik może zarządzać tylko swoją placówką.

Funkcjonalności:

CRUD seansów w przypisanej placówce,
CRUD sal w przypisanej placówce,
generowanie miejsc dla sali,
podgląd rezerwacji w swojej placówce,
statystyki swojej placówki.

Pracownik nie może edytować danych innej placówki.

Panel administratora

Ścieżki:

/admin/movies
/admin/genres
/admin/cinemas
/admin/users
/admin/statistics

Funkcjonalności:

CRUD filmów,
CRUD gatunków,
CRUD placówek,
zarządzanie użytkownikami,
nadawanie ról,
przypisywanie pracownika do placówki,
statystyki całej sieci.
Frontend

Projekt korzysta z Tailwind CSS.

Stosuj Twig + Tailwind.

Wymagania:

layout bazowy w base.html.twig,
osobne widoki dla public, user, staff i admin,
responsywne tabele,
formularze ostylowane Tailwindem,
czytelna mapa miejsc,
proste dashboardy statystyk.

Nie używaj Bootstrap.

Fixtures

Dodaj przykładowe dane testowe:

minimum 3 placówki,
minimum 2 sale w każdej placówce,
minimum 20 miejsc w każdej sali,
minimum 8 filmów,
minimum 4 gatunki,
minimum 10 seansów,
minimum 3 użytkowników:
klient,
pracownik,
administrator.
Kryteria jakości

Projekt ma spełniać następujące warunki:

jasna architektura MVC,
minimum trzy modele w różnych relacjach,
Doctrine ORM z zaawansowanymi zapytaniami,
system użytkowników i ról,
zabezpieczenie dostępu do danych,
kompletna walidacja,
pełna realizacja tematu,
brak logiki biznesowej w kontrolerach,
brak logiki biznesowej w Twig,
brak dostępu pracownika do obcej placówki,
brak dostępu użytkownika do cudzych rezerwacji.
Kolejność implementacji

Implementuj projekt w tej kolejności:

Encje Doctrine i relacje.
Migracje.
Fixtures.
System logowania i rejestracji.
Role użytkowników.
Widoki publiczne repertuaru i filmów.
Rezerwacja miejsc.
Panel użytkownika.
Panel pracownika.
Panel administratora.
Statystyki.
Walidacja.
Finalne zabezpieczenia dostępu.
Stylowanie Tailwindem.

Najważniejsze: **nie rób czystego MVC z całą logiką w kontrolerach**. Dla prowadzącego lepiej będzie wyglądać: **Symfony MVC + serwisy + repozytoria + votery**, bo wtedy architektura jest faktycznie „jasna i właściwa”.
