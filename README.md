# Webshoppen

En enkel webbshop byggd med PHP och MySQL.

## Installation

1. Se till att du har MAMP (eller liknande) installerat med PHP 7.4+ och MySQL.

2. Klona detta repository till din MAMP htdocs-mapp:
```bash
cd /Applications/MAMP/htdocs/
git clone [repository-url] webshoppen
```

3. Importera databasen:
- Starta MAMP och öppna phpMyAdmin
- Skapa en ny databas som heter "webshoppen"
- Importera filen `sql/schema.sql`

4. Konfigurera databasanslutningen:
- Öppna `includes/db.php`
- Uppdatera användarnamn och lösenord om det behövs (standard är root/root)

5. Öppna webbläsaren och gå till:
```
http://localhost:8888/webshoppen/
```

## Funktioner

- Användarregistrering och inloggning
- Produktvisning och kategorier
- Kundvagn
- Sökfunktion
- Responsiv design

## Mappstruktur

```
webshoppen/
├── includes/        # Återanvändbara PHP-filer
├── lib/            # Klasser och bibliotek
├── public/         # Publikt tillgängliga filer
│   ├── css/       # Stilmallar
│   └── index.php  # Startsida
├── sql/           # Databasfiler
└── README.md      # Denna fil
```

## Utveckling

För att bidra till projektet:

1. Skapa en ny branch
2. Gör dina ändringar
3. Skicka en pull request

## Licens

Detta projekt är licensierat under MIT-licensen. 