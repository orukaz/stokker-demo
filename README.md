# Stokker Demo

## Autor

Raido Orumets  
https://orumets.ee

## Töökuulutus

Taustaks: [Stokker full-stack vanemarendaja töökuulutus](stokker-full-stack-vanemarendaja.md)  
Algallikas: https://work.brandem.ee/stokker-full-stack-vanemarendaja/

## Ülesanne

**Praktiline ülesanne kandidaadile**

**Eeldatav ajakulu:** 1–1,5 tundi  
**Töövahendid:** Võid kasutada oma arvutit ning endale sobivaid tööriistu ja tehnoloogiaid.  
**Vestlusel:** Palume sul tutvustada loodud lahendust ja selgitada tehtud valikuid.

**Ülesanne**

Loo lihtne veebileht, mis kuvab etteantud Stokkeri tooteandmeid ning võimaldab kasutajal lisada tooteid lemmikutesse.

1. **Toodete andmebaasi loomine**
    - Luua andmebaas toodete salvestamiseks.
2. **Andmete import XML-ist**
    - Importida tooteandmed XML-lingilt: [Stokker API](https://api.stokker.com/feed/products?DataAreaID=SET&ig=FO01).
3. **Tootenimekirja kuvamine veebilehel** (ainult nimekirja kuvamine, kogu lehe disain pole vajalik)
    - **Kuvada järgmised komponendid:**
        - Toote pilt
        - Nimetus
        - Hind
    - **Visuaalne stiil:**
        - Nimekiri võiks sarnaneda Stokkeri veebilehe toodete kuvaga ([näide](https://www.stokker.ee/et/mootorsaed)).
    - **Piirangud:**
        - Kuvada korraga maksimaalselt 20 toodet, paigutades 5 toodet ühele reale.
4. **Lemmikute funktsionaalsus**
    - Lisada võimalus märkida tooteid "Lemmikute" nimekirja.
    - Salvestada lemmikuks lisatud tooted andmebaasi.
    - Kuvada toote nimekirjas märge, kui toode on lisatud lemmikutesse.
5. **Endless scroll**
    - Kuvada tooted osade kaupa:
        - Esialgu laadida 20 toodet.
        - Kui kasutaja kerib lehte alla, laadida iga kord juurde 10 toodet.
        - Jätkata toodete lisamist seni, kuni kõik tooted on kuvatud.

## Lahendus

Rakendus on ehitatud Laravel 13 + Inertia v3 + Svelte 5 stackiga.

Lahendus sisaldab:
- Toodete tabelit andmebaasis.
- XML feedi importi Stokker API-st.
- Toodete nimekirja vaadet (pilt, nimetus, hind).
- Lemmikute märkimist ja salvestamist andmebaasi.
- Endless scroll funktsionaalsust (alglaadimine + järkjärguline lisalaadimine).

## Kuidas käivitada

### Nõuded

- **PHP**: min 8.4, max 8.4.x (testitud: 8.4.21)
- **Composer**: min 2.0, max 2.x (testitud: 2.9.5)
- **Node.js**: min 22, max 26.x (testitud: 26.0.0)
- **npm**: min 10, max 11.x (testitud: 11.12.1)
- **Laravel Herd** (soovituslik lokaalse `.test` domeeni jaoks)
- **Andmebaas**:
  PostgreSQL min 10.0, max uusim stabiilne
  MySQL min 5.7, max uusim stabiilne
  SQLite min 3.26.0, max uusim stabiilne

### Andmebaasi seadistamine (`.env`)

Vali üks neist:

**PostgreSQL**:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=stokker_demo
DB_USERNAME=postgres
DB_PASSWORD=secret
```

**MySQL**:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=stokker_demo
DB_USERNAME=root
DB_PASSWORD=secret
```

**SQLite**:
```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite
```

SQLite puhul loo fail enne migratsioone:
```sh
touch database/database.sqlite
```

### Variant 1: kiire viis

1. Paigalda sõltuvused ja tee esmane setup:
   ```sh
   composer run setup
   ```
2. Käivita arenduskeskkond:
   ```sh
   composer run dev
   ```
3. Ava rakendus brauseris:
   `https://stokker-demo.test`

### Variant 2: tavaline käsitsi viis

1. Paigalda PHP sõltuvused:
   ```sh
   composer install
   ```
2. Paigalda JS sõltuvused:
   ```sh
   npm install
   ```
3. Loo `.env` fail:
   ```sh
   cp .env.example .env
   ```
4. Soovi korral määra demo kasutajate parool (`.env`):
   ```env
   DEMO_USER_PASSWORD=password
   ```
5. Genereeri rakenduse võti:
   ```sh
   php artisan key:generate
   ```
6. Seadista andmebaas (`.env`) ja käivita migratsioonid:
   ```sh
   php artisan migrate
   ```
7. Käivita backend:
   ```sh
   php artisan serve
   ```
8. Käivita frontend:
   ```sh
   npm run dev
   ```

Kui kasutad Laravel Herdi domeeni, ava `https://stokker-demo.test`.  
Kui kasutad `php artisan serve`, ava `http://127.0.0.1:8000`.

### Andmete ettevalmistus

Käivita seederid:
```sh
php artisan db:seed
```

`php artisan db:seed`:
- loob demo kasutajad
- käivitab `php artisan stokker:sync-products` käsu
- impordib/sünkroniseerib tooted Stokkeri XML feedist

Loodavad demo kasutajad:
- `mari@example.com` (nimi: Mari Maasikas)
- `admin@example.com` (nimi: Admin User)

Mõlema parool tuleb `.env` väärtusest `DEMO_USER_PASSWORD` (vaikimisi `password`).

### Scheduler

Scheduler käivitab regulaarselt käsu `php artisan stokker:sync-products`.

Arenduses võid scheduleri käivitada eraldi terminalis:
```sh
php artisan schedule:work
```

Ühekordseks kontrolliks:
```sh
php artisan schedule:run
```

Sama sünkroniseerimiskäsku saab käivitada ka käsitsi:
```sh
php artisan stokker:sync-products
```
