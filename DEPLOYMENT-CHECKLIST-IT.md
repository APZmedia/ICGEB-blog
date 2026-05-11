# Checklist Operativa Cliente (Referente Unico)

Questa checklist e pensata per **una sola persona** che gestisce il sito, anche se il progetto e finanziato da un'istituzione.

## Fase 1 - Minimo indispensabile (go-live)

- [ ] Fare deploy dell'ultimo codice corretto su **staging**.
- [ ] Verificare staging, poi fare deploy in **produzione**.
- [ ] Svuotare cache WordPress/server/CDN.
- [ ] Confermare che il plugin `doi-version-plugin` sia attivo.
- [ ] In WordPress -> **Impostazioni > Permalink** -> cliccare **Salva** una volta.

## Fase 2 - Verifiche obbligatorie (10-20 minuti)

- [ ] Aprire una pagina articolo e controllare nel sorgente HTML la presenza di:
  - [ ] `ScholarlyArticle`
  - [ ] `BreadcrumbList`
- [ ] Aprire una pagina FAQ abilitata e verificare la presenza di `FAQPage`.
- [ ] Aprire homepage e verificare che rimangano presenti `ResearchOrganization` e `WebSite`.
- [ ] Controllare che non ci siano duplicazioni anomale di schema o canonical nel `<head>`.

## Fase 3 - Contenuti da mantenere corretti

- [ ] Per ogni pagina/articolo FAQ: abilitare il flag FAQ nel box dedicato.
- [ ] Inserire FAQ in JSON valido (`question` + `answer`), senza righe vuote.
- [ ] Verificare che DOI e versione siano presenti negli articoli pubblicati.
- [ ] Tenere aggiornato ORCID nel profilo autore principale.

## Fase 4 - Controllo SEO/Schema prima della chiusura

- [ ] Testare almeno 3 URL (home, 1 post, 1 pagina FAQ) con:
  - [ ] Google Rich Results Test
  - [ ] Schema Markup Validator
- [ ] Risolvere eventuali errori bloccanti prima del rilascio definitivo.

## Fase 5 - Controllo mensile rapido (15 minuti)

- [ ] Verificare 3 URL principali e confermare schema presente.
- [ ] Controllare che breadcrumb e canonical siano ancora corretti.
- [ ] Controllare alt text immagini nuove (soprattutto featured image).
- [ ] Dopo aggiornamenti di plugin/theme, rifare il controllo della Fase 2.

---

## Ruolo e responsabilita

**Referente unico cliente:** una sola persona responsabile di:
- pubblicazione contenuti,
- gestione FAQ,
- controllo schema mensile,
- coordinamento con supporto tecnico se qualcosa si rompe.

## Sign-off finale

- [ ] Deploy completato e verificato.
- [ ] Schema validato su home + post + pagina FAQ.
- [ ] Processo mensile di controllo definito.

Firma referente unico cliente: ____________________

Data: ____________________
