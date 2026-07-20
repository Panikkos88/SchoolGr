# Τοπική ανάπτυξη με Docker και Codex

## Προϋποθέσεις

- Git
- GitHub CLI
- Docker Desktop με ενεργό Docker Compose
- Codex

Δεν απαιτείται πρόσβαση στο Google Cloud για καθημερινή ανάπτυξη.

## Πρώτη εγκατάσταση

```powershell
gh auth login
gh repo clone Panikkos88/SchoolGr
cd SchoolGr
Copy-Item .env.example .env
```

Ανοίξτε το `.env` και αντικαταστήστε όλες τις τιμές `change-me` με ισχυρές,
αποκλειστικά τοπικές τιμές. Το `.env` αγνοείται από τη Git.

Εκκινήστε το περιβάλλον:

```powershell
docker compose up --build
```

Η εφαρμογή είναι διαθέσιμη στο
[http://localhost:8080](http://localhost:8080).

Το email του τοπικού διαχειριστή ορίζεται από
`SCHOOLGR_DEV_ADMIN_EMAIL` και ο κωδικός από
`SCHOOLGR_DEV_ADMIN_PASSWORD`. Αυτές οι τιμές δεν πρέπει να αποθηκεύονται σε
commit, screenshot ή μήνυμα.

## Τι δημιουργείται

- PHP 8.3 και Apache container.
- MariaDB 10.11 container.
- Κενό schema 47 πινάκων χωρίς production εγγραφές.
- Ένας τοπικός admin από τις μεταβλητές του `.env`.
- Ξεχωριστό Docker volume για τη βάση.

Το license bypass λειτουργεί μόνο όταν το container έχει ταυτόχρονα:

```text
SCHOOLMEDIA_ENV=development
SCHOOLMEDIA_BYPASS_LICENSE=1
```

Οι μεταβλητές αυτές δεν υπάρχουν στην παραγωγή.

## Καθημερινή εργασία

Πριν από νέα αλλαγή:

```powershell
git switch main
git pull --ff-only
git switch -c feature/perigrafi-allagis
docker compose up -d
```

Χρήσιμες εντολές:

```powershell
docker compose ps
docker compose logs -f web
docker compose logs -f db
docker compose down
```

Μετά την αλλαγή:

```powershell
git status
git add <αρχεία>
git commit -m "Ελληνική περιγραφή αλλαγής"
git push -u origin HEAD
gh pr create
```

## Καθαρή επανεκκίνηση βάσης

Η παρακάτω εντολή διαγράφει μόνο την τοπική Docker βάση:

```powershell
docker compose down --volumes
docker compose up --build
```

Δεν επηρεάζει το Google Cloud.

## Χρήση με Codex

Ανοίξτε τον φάκελο `SchoolGr` στο Codex και ζητήστε:

> Δημιούργησε νέο branch, ξεκίνησε το Docker localhost περιβάλλον, υλοποίησε
> την αλλαγή, εκτέλεσε ελέγχους και άνοιξε pull request. Μην χρησιμοποιήσεις
> production δεδομένα ή credentials.

Το Codex διαβάζει αυτόματα το `AGENTS.md` και ενημερώνει το ελληνικό
`docs/SESSION_LOG.md` σε ουσιαστικές συνεδρίες.

## Αντιμετώπιση προβλημάτων

Ελέγξτε πρώτα:

```powershell
docker compose config
docker compose ps
docker compose logs init
docker compose logs web
docker compose logs db
```

Αν αλλάξει το schema initialization, απαιτείται νέα τοπική βάση με
`docker compose down --volumes`.
