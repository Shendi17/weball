# Weball en Python

Nouvelle version de Weball (weball.weballone.com), l'annuaire de l'écosystème WAO.
FastAPI + templates Jinja2 rendus côté serveur, base PostgreSQL `weball`, comme Digita et INES.
Le site PHP à la racine du dépôt reste en place jusqu'à la bascule.

## Structure

- `weball/catalogue.py` : les 5 groupes × 5 types (Classeur, Collection, Registre, Sélection, Guide)
  et le type de fiche INES de chaque groupe (PROFIL, RESSOURCE, INDICATION, PRODUIT, ACTION).
- `weball/db.py` : deux tables génériques. `element` porte toutes les fiches, quel que soit le type,
  avec les champs propres au type dans `donnees` (JSON). `terme` porte la taxonomie de chaque type
  (genre > catégorie, ex. Artiste > Peintre).
- `weball/main.py` : les pages. `/` accueil, `/<groupe>`, `/<groupe>/<type>` (filtres `?genre=` et
  `?categorie=`), `/<groupe>/<type>/<slug>` fiche, `/recherche?q=`. Les anciennes URL PHP
  (`/personnalite`, `/personnalite_detail?id=6`) redirigent en 301 vers les nouvelles.
- `tools/import_mysql_dump.py` : importe un export mysqldump de l'ancienne base `weballone`
  sans serveur MySQL. Les tables de comptes (`users`, `user_services`) sont ignorées et les
  coordonnées des personnes (e-mail, téléphone) ne sont jamais publiées.

## Lancer en local

```bash
cd python
pip install -r requirements-dev.txt
createdb weball
python -m tools.import_mysql_dump ../database/weball_contenu.sql   # export sans comptes
uvicorn weball.main:app --reload
```

Synchro INES : avec `WEBALL_INES_API_URL=http://ines-backend:8080`, Weball lit au démarrage les nœuds
`weball-<slug>` de l'arborescence INES (noms et descriptions des groupes et types).
Sans INES joignable, il garde `weball/catalogue.py`.

Autre base : `WEBALL_DATABASE_URL=postgresql+psycopg://user@hote/weball` (ou `sqlite:///weball.db`).

Tests : `pytest -q`
