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

- PHP 8.4+
- Composer
- Node.js + npm
- Andmebaas: PostgreSQL, MySQL **või** SQLite

### Andmebaasi seadistamine (`.env`)

Vali üks neist:

PostgreSQL:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=stokker_demo
DB_USERNAME=postgres
DB_PASSWORD=secret
```

MySQL:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=stokker_demo
DB_USERNAME=root
DB_PASSWORD=secret
```

SQLite:
```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite
```

SQLite puhul loo fail enne migratsioone:
```bash
touch database/database.sqlite
```

### Variant 1: kiire viis

1. Paigalda sõltuvused ja tee esmane setup:
   ```bash
   composer run setup
   ```
2. Käivita arenduskeskkond:
   ```bash
   composer run dev
   ```
3. Ava rakendus brauseris:
   `https://stokker-demo.test`

### Variant 2: tavaline käsitsi viis

1. Paigalda PHP sõltuvused:
   ```bash
   composer install
   ```
2. Paigalda JS sõltuvused:
   ```bash
   npm install
   ```
3. Loo `.env` fail:
   ```bash
   cp .env.example .env
   ```
4. Genereeri rakenduse võti:
   ```bash
   php artisan key:generate
   ```
5. Seadista andmebaas (`.env`) ja käivita migratsioonid:
   ```bash
   php artisan migrate
   ```
6. Käivita backend:
   ```bash
   php artisan serve
   ```
7. Käivita frontend:
   ```bash
   npm run dev
   ```

Kui kasutad Herdi domeeni, ava `https://stokker-demo.test`.  
Kui kasutad `php artisan serve`, ava `http://127.0.0.1:8000`.
