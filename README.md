# Stokker Demo

Demo URL: https://stokker-demo.orumets.ee/

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

Rakendus on ehitatud Laraveli full-stack lähenemisega, kus backend ja frontend on samas koodibaasis.

### Tehnoloogiad

- **PHP 8.4** - Laraveli jooksutamiseks. - https://www.php.net/releases/8.4/en.php
- **Laravel 13** - Populaarne full-stack PHP framework kiireks arenduseks ja paljude sisseehitatud funktsioonidega. - https://laravel.com/docs
- **Inertia.js 3** - Sarnaneb veidi Next.js kasutuskogemusega, aga töötab Laraveli backend-first mudeliga ilma eraldi REST API kihita. - https://inertiajs.com
- **Svelte 5** (`@inertiajs/svelte` v3) - JavaScripti SPA Framework, mis on Stokkeris juba kasutusel. - https://svelte.dev
- **Tailwind CSS 4** - Kiire ja praktiline viis responsive kasutajaliideste loomiseks. - https://tailwindcss.com
- **shadcn-svelte** - Valmis UI komponendid, tagavad ligipääsetavuse ja on kiiresti edasi arendatavad. - https://www.shadcn-svelte.com
- **Lucide** (`lucide-svelte`) - Lihtne viis lisada ühtse stiiliga ikoone. - https://lucide.dev
- **MySQL** - Relatsiooniline andmebaas toodete ja lemmikute salvestamiseks. - https://www.mysql.com

### Miks selline stack

Selle ülesande eesmärk oli teha toimiv lahendus kiiresti, puhtalt ja nii, et seda oleks lihtne edasi arendada.

**Laravel 13** sobis hästi, sest see on populaarne full-stack framework, mis katab suure osa arendusprotsessist out of the box: routing, migratsioonid, käsurea käsud, scheduler, queue, auth, ORM ja erinevad andmebaasiühendused.  
Selle ülesande kontekstis andis see kohe valmis tööriistad:
- scheduler (taustal automaatsete tegevuste käivitamiseks)
- custom käsurea käsud (nt `php artisan stokker:sync-products`)
- hea rakenduse ülesehitus (route, controller, service, model)
- API võimekus (vajadusel lihtne lisada API endpointe)
- frontend/backend starter-kit tugi kiireks käivituseks
- erinevate andmebaaside tugi
- kasutajate süsteem (auth)

**Inertia.js 3** toimib sillana Laraveli ja Svelte vahel ning kattis selle ülesande raames kohe eesmärgi.  
Andmed liiguvad otse route -> controller -> Svelte vaade, ilma eraldi REST API kihita.  
Tulemus on kiirem arendus, vähem dubleerimist ja lihtsam ülesehitus, eriti nimekirja ning endless scroll vaadete puhul.

**Svelte 5** oli loogiline valik, sest see on JavaScripti SPA framework, mis on Stokkeri stackis juba kasutusel.  
See aitas hoida frontend koodi lihtsa ja kiirelt muudetavana.

**Tailwind CSS 4** on üks parimaid responsive-first veebikujunduse raamistikke, millega sain tootenimekirja kiiresti ja ühtlase stiiliga ellu viia.  

**shadcn-svelte** (ehk **shadcn**) on üks parimaid UI-komponentide raamistikke: komponendid on väga hästi kohandatavad ning järgivad **ligipääsetavuse** nõudeid.

### Kuidas lahendus töötab

1. **Andmemudel**
   **Toodete andmemudel (`products`)**

   | Väli | Tüüp | Selgitus |
   |---|---|---|
   | `id` | `bigint unsigned` | Kirje ID |
   | `code` | `varchar(255)` | Unikaalne tootekood |
   | `title` | `varchar(255)` | Toote nimetus |
   | `description` | `text` | Kirjeldus |
   | `link` | `text` | Toote lehe URL |
   | `image_url` | `text` | Pildi URL |
   | `brand` | `varchar(255)` | Bränd |
   | `condition` | `varchar(255)` | Seisukord |
   | `availability` | `varchar(255)` | Saadavuse staatus |
   | `quantity` | `decimal(14,4)` | Kogus |
   | `price` | `decimal(14,4)` | Hind |
   | `currency` | `char(3)` | Valuuta (nt EUR) |
   | `synced_at` | `timestamp` | Viimase sünkroniseerimise aeg |
   | `created_at` | `timestamp` | Loomise aeg |
   | `updated_at` | `timestamp` | Uuendamise aeg |

   **Lemmikud (`product_favorites`)**

   | Väli | Tüüp | Selgitus |
   |---|---|---|
   | `id` | `bigint unsigned` | Kirje ID |
   | `user_id` | `bigint unsigned` | Sisselogitud kasutaja ID |
   | `visitor_id` | `char(36)` | Külalise ID cookie kaudu |
   | `product_id` | `bigint unsigned` | Lemmikuks märgitud toote ID |
   | `created_at` | `timestamp` | Loomise aeg |
   | `updated_at` | `timestamp` | Uuendamise aeg |

2. **Andmete import XML-ist**
   ```text
   app/
   ├── Console/
   │   └── Commands/
   │       └── SyncStokkerProductsCommand.php
   └── Services/
       └── Stokker/
           ├── StokkerApi.php
           ├── StokkerProductData.php
           ├── StokkerProductsParser.php
           └── StokkerService.php
   ```

   **Klasside rollid**
   - `StokkerApi`: teeb HTTP päringu Stokkeri feedi (`/feed/products`) ja tagastab XML sisu.
   - `StokkerProductsParser`: loeb XML-i, parsib tooteread ja normaliseerib väärtused (`price`, `currency`, `quantity`).
   - `StokkerProductData`: DTO, mis hoiab ühe toote andmeid tüübiturvaliselt ja teisendab need DB jaoks massiiviks.
   - `StokkerService`: orkestreerib kogu sync voo (fetch -> parse -> validate -> upsert).

   **Kuidas sync töötab**
   - Käsk `php artisan stokker:sync-products` kutsub `StokkerService::syncProducts('SET', 'FO01')`.
   - `StokkerService` küsib XML-i `StokkerApi` kaudu.
   - XML teisendatakse toodete kogumikuks `StokkerProductsParser` abil.
   - Iga kirje valideeritakse; vigased kirjed jäetakse vahele ja logitakse.
   - Kehtivad kirjed salvestatakse `products` tabelisse `upsert`-iga unikaalse `code` alusel.

   **Commands**
   - `php artisan stokker:sync-products` - käivitab toodete XML impordi/sünkroniseerimise käsitsi; scheduleris jookseb sama töö iga tund (`hourly()`, cron `0 * * * *`).
   - `php artisan schedule:work` - käivitab scheduleri lokaalarenduses.
   - `php artisan schedule:run` - käivitab scheduleri ühe korra (kiireks kontrolliks).

3. **Tootenimekiri**
   - Route: `/et/mootorsaed`.
   - Controller tagastab Inertia lehe `products/Index`.
   - Iga toote kohta kuvatakse pilt, nimetus ja hind.
   - Kasutatakse endless scroll'i: esimene laadimine 20 toodet, järgmised laadimised cursor-paginatsiooniga 10 toodet korraga.
   - Frontendis kasutatakse Inertia `InfiniteScroll` komponenti.

   **Frontendi ülesehitus**
   ```text
   resources/js/
   ├── pages/
   │   └── products/
   │       └── Index.svelte                 # tootenimekirja leht + InfiniteScroll
   ├── layouts/
   │   └── SiteLayout.svelte                # avaliku vaate layout
   └── components/
       └── site/
           ├── ProductCard.svelte           # toote kaart (pilt, nimetus, hind, saadavus)
           ├── ProductFavoriteToggle.svelte # lemmiku lisamine/eemaldamine
           └── FavoritesHeaderButton.svelte # lemmikute loendur päises
   ```

4. **Lemmikute funktsionaalsus**
   - `POST /products/{product}/favorite` lisab lemmiku.
   - `DELETE /products/{product}/favorite` eemaldab lemmiku.
   - UI uuendab olekut koheselt ja näitab favorited seisu tootenimekirjas.
   - Sisselogitud kasutaja puhul seotakse lemmikud `user_id` alusel.
   - Külaliskasutaja puhul kasutatakse `visitor_id` cookie't (UUID), et lemmikud töötaksid ka ilma sisselogimiseta.

5. **Automaatne sünkroniseerimine**
   - Scheduler käivitab käsu `php artisan stokker:sync-products` iga tund (`hourly()`).
   - `withoutOverlapping()` väldib sama käsu paralleelset käivitust.
   - Nii püsivad tooted ajakohased ilma käsitsi sekkumiseta, kuid vajadusel saab sama käsu alati käsitsi käivitada.

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
