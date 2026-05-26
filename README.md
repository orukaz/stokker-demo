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

- **Laravel + Inertia + Svelte** annab kiire arenduse ja lihtsama arhitektuuri:
  serveripoolne loogika jääb Laravelisse, frontend on siiski SPA-kogemusega.
- **Inertia v3** sobib hästi endless scroll jaoks (`Inertia::scroll` + `InfiniteScroll` komponent).

Valiku peamine eesmärk oli teha lahendus, mida on lihtne edasi arendada ja hooldada.
Selle asemel, et ehitada eraldi backend API + eraldi frontend rakendus, kasutasin
ühtset Laraveli koodibaasi. See vähendab oluliselt tehnilist keerukust:
- vähem dubleeritud loogikat (validatsioon, autentimine, route'id)
- kiirem arendus (üks deploy- ja arendusvoog)
- lihtsam testimine (backend ja leheandmed samas rakenduses)

**Laravel 13** oli hea valik, sest see annab stabiilse ja modernse põhja:
Eloquent, scheduler, queue/readiness, Artisan käsud ja migratsioonid on kõik
tootmisküpsed ning hoiavad arenduse struktureerituna.

**Inertia.js 3** võimaldas teha SPA-laadse kasutuskogemuse ilma REST API kihita.
Server tagastab otse lehe jaoks vajalikud propsid, mis tähendab, et lehe loogika
on väga läbipaistev: route -> controller -> Inertia page. See tegi eriti lihtsaks
endless scrolli lahenduse.

**Svelte 5** sai valitud, kuna see on kerge runtime'iga ja selge komponentmudeliga.
Tootegridi ning lemmikute interaktsioonid on lihtsad hallata väikese boilerplate'iga.

**Tailwind CSS 4** andis kiire viisi ehitada Stokkeri stiiliga sarnane tootenimekiri
ja hoida klassid komponentide lähedal. See kiirendas UI iteratsioone.

**shadcn-svelte** ja **Lucide** vähendasid UI komponentide nullist ehitamise vajadust:
ikoonid, sisendite baaskomponendid ja väiksemad UI osad said teha kiiremini ning
ühtlasema kvaliteediga.

### Kuidas lahendus töötab

1. **Toodete andmemudel**
   - `products` tabel salvestab feedist tulnud tooted (`code`, `title`, `price`, `image_url`, `synced_at` jne).
   - `product_favorites` tabel salvestab lemmikud.
   - Lemmikud on seotud kas `user_id` (sisselogitud kasutaja) või `visitor_id` (külaline cookie kaudu).

2. **Andmete import XML-ist**
   - Käsk `php artisan stokker:sync-products` loeb Stokkeri XML feedi.
   - Parser (`StokkerProductsParser`) normaliseerib hinnad/valuuta ja mapib väljad.
   - Service (`StokkerService`) valideerib iga toote ja teeb `upsert`-i `code` alusel.
   - Tulemuseks on idempotentne sync (olemasolevad tooted uuendatakse, uued lisatakse).
   - See lähenemine on turvaline ka korduvatel käivitustel: sama käsk ei tekita duplikaate.

3. **Tootenimekiri**
   - Route: `/et/mootorsaed`.
   - Controller tagastab Inertia lehe `products/Index`.
   - Iga toote kohta kuvatakse pilt, nimetus ja hind.

4. **Lemmikute funktsionaalsus**
   - `POST /products/{product}/favorite` lisab lemmiku.
   - `DELETE /products/{product}/favorite` eemaldab lemmiku.
   - UI uuendab olekut koheselt ja näitab favorited seisu tootenimekirjas.
   - Külaliskasutaja puhul kasutatakse `visitor_id` cookie't, et lemmikud töötaksid ka ilma sisselogimiseta.

5. **Endless scroll**
   - Esimene laadimine: 20 toodet.
   - Järgmised laadimised cursor-paginatsiooniga: 10 toodet korraga.
   - Frontendis kasutatakse Inertia `InfiniteScroll` komponenti.

6. **Automaatne sünkroniseerimine**
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
