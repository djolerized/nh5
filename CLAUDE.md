# Projekat: Integracija kontakt forme sa Monday.com

## Zadatak
Povezati postojeću PHP kontakt formu sa Monday.com leads tabelom tako da svaki submit forme kreira novi lead item.

## Monday.com konfiguracija

**Board ID:** `5058436837`

**API endpoint:** `https://api.monday.com/v2`

**API token:** čuva se u `config.php` kao konstanta `MONDAY_API_TOKEN` (van verzionisanja, dodat u `.gitignore`)

### Mapiranje kolona forme → Monday.com

| Polje na formi | Monday column ID     | Monday naziv kolone | Tip        |
|----------------|----------------------|---------------------|------------|
| Ime i prezime  | `name`               | Name                | item_name  |
| Telefon        | `text_mkx04g0`       | Phone               | text       |
| Email          | `lead_email`         | Email               | email      |
| Poruka         | `text_mkx7vkqx`      | Poruka              | text       |

### Automatski popuniti pri kreiranju

| Monday column ID  | Vrednost              | Napomena                        |
|-------------------|-----------------------|---------------------------------|
| `text_mkx074pr`   | `"Website"`           | Source/Platform                 |
| `lead_status`     | `{"label": "New Lead"}` | Status — proveriti tačan label |

## Struktura fajlova

```
/
├── index.html          # Landing page sa formom
├── contact.php         # Form handler — ovde ide Monday.com logika
├── config.php          # API token i konstante (u .gitignore)
├── config.example.php  # Primer config fajla (ide u git)
└── .gitignore
```

## Šta treba implementirati u `contact.php`

1. Sanitizacija i validacija inputa (`name`, `email`, `phone`, `message`)
2. GraphQL `create_item` mutation ka Monday.com API-ju
3. `column_values` JSON sa mapiranim kolonama (tabela gore)
4. cURL poziv sa `Authorization: Bearer` headerom
5. Vraćanje JSON response-a formi (`{"success": true/false}`)

### GraphQL mutation template

```graphql
mutation {
    create_item(
        board_id: 5058436837,
        item_name: "IME_I_PREZIME",
        column_values: "{\"text_mkx04g0\": \"TELEFON\", \"lead_email\": {\"email\": \"EMAIL\", \"text\": \"EMAIL\"}, \"text_mkx7vkqx\": \"PORUKA\", \"text_mkx074pr\": \"Website\"}"
    ) {
        id
    }
}
```

## Napomene

- `lead_email` kolona zahteva objekat `{"email": "...", "text": "..."}` — nije plain string
- `formula_mkxrwp7e` (Datum i vreme pristizanja) i `pulse_log_mkxrn33c` (Log) se **ne popunjavaju** — Monday ih generiše automatski
- `multiple_person_mkx0trqj` (Owner) i `button` kolone se preskačaju
- API token **ne sme ići u git** — obavezno u `config.php` koji je u `.gitignore`
