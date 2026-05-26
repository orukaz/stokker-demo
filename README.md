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
