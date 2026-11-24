"""Importe un export mysqldump de l'ancienne base PHP (weballone) dans la base `weball`.

Usage : python -m tools.import_mysql_dump chemin/vers/export.sql [--url postgresql+psycopg://...]

Lit le fichier .sql directement (pas besoin de serveur MySQL). Les tables de comptes
(`users`, `user_services`) sont ignorées. L'import vide puis remplit `element`, `terme`
et `element_terme` : il peut être relancé autant de fois que nécessaire.
"""
import argparse
import json
import re
import sys
from pathlib import Path

from slugify import slugify
from sqlalchemy import delete

sys.path.insert(0, str(Path(__file__).resolve().parents[1]))
from weball.db import DATABASE_URL, Base, Element, Terme, element_terme, make_session_factory  # noqa: E402

TABLES_IGNOREES = {"users", "user_services"}
# Seuls Personnalité et Autorité ont du vrai contenu ; les autres tables de modules ne
# contiennent que les 3-4 lignes d'exemple du prototype PHP (TechCorp, Bio Market...).
TYPES_REELS = {"personnalite", "autorite"}
# Personnalités fictives du prototype (avatar générique), retirées à la demande d'Anthony le 23/11/2026.
FICHES_DEMO = {("personnalite", 1), ("personnalite", 2), ("personnalite", 3), ("personnalite", 4)}
# Coordonnées de personnes : jamais publiées sur l'annuaire.
CHAMPS_PRIVES = {"user_id", "email", "telephone", "contact_email", "contact_telephone"}
TYPES_PERSONNES = {"personnalite", "adhesion"}
CHAMPS_TITRE = ("titre", "nom")
CHAMPS_DESCRIPTION = ("biographie", "description_longue", "description")
CHAMPS_IMAGE = ("photo", "logo", "image")
CHAMPS_SOUS_TITRE = ("profession", "titre", "type", "secteur", "domaine")
CHAMPS_TECHNIQUES = {"id", "type_id", "category_id", "types_ids", "categories_ids", "date_creation"}

VIDES = (None, "", "[]", "{}", "0000-00-00", "0000-00-00 00:00:00")
_ESCAPES = {"0": "\0", "n": "\n", "r": "\r", "t": "\t", "Z": "\x1a", "b": "\b"}


def parse_dump(text: str) -> dict[str, list[dict]]:
    """Renvoie {table: [ligne, ...]} à partir des CREATE TABLE et INSERT d'un mysqldump."""
    colonnes = {}
    for m in re.finditer(r"CREATE TABLE `(\w+)` \((.*?)\n\)", text, re.S):
        colonnes[m.group(1)] = re.findall(r"^\s+`(\w+)`", m.group(2), re.M)
    lignes: dict[str, list[dict]] = {}
    for m in re.finditer(r"INSERT INTO `(\w+)`(?: \(([^)]*)\))? VALUES\s*", text):
        table = m.group(1)
        cols = re.findall(r"`(\w+)`", m.group(2)) if m.group(2) else colonnes[table]
        tuples, _ = _parse_values(text, m.end())
        lignes.setdefault(table, []).extend(dict(zip(cols, t)) for t in tuples)
    return lignes


def _parse_values(s: str, i: int):
    """Lit `(v, v, ...),(...);` à partir de l'index i. Renvoie (tuples, index de fin)."""
    tuples = []
    while True:
        assert s[i] == "(", f"'(' attendu à {i}"
        row, i = [], i + 1
        while True:
            while s[i] in " \n":
                i += 1
            if s[i] == "'":
                buf, i = [], i + 1
                while True:
                    c = s[i]
                    if c == "\\":
                        buf.append(_ESCAPES.get(s[i + 1], s[i + 1]))
                        i += 2
                    elif c == "'" and s[i + 1] == "'":
                        buf.append("'")
                        i += 2
                    elif c == "'":
                        i += 1
                        break
                    else:
                        buf.append(c)
                        i += 1
                row.append("".join(buf))
            else:
                j = i
                while s[j] not in ",)":
                    j += 1
                tok = s[i:j].strip()
                row.append(None if tok == "NULL" else (float(tok) if "." in tok else int(tok)))
                i = j
            if s[i] == ",":
                i += 1
                continue
            i += 1  # ')'
            break
        tuples.append(row)
        if s[i] == ";":
            return tuples, i + 1
        i += 1  # ','
        while s[i] in " \n":
            i += 1


def _json_ou_texte(v):
    if isinstance(v, str) and v[:1] in "[{":
        try:
            return json.loads(v)
        except ValueError:
            return v
    return v


def _premier(row, champs):
    for c in champs:
        if row.get(c) not in (None, ""):
            return str(row[c]).strip()
    return None


def _ids(row, cle_liste, cle_simple):
    ids = _json_ou_texte(row.get(cle_liste)) or []
    if not isinstance(ids, list):
        ids = []
    if row.get(cle_simple) not in (None, "", 0, "0"):
        ids.append(row[cle_simple])
    return {int(x) for x in ids if str(x).isdigit()}


def reparer_image(url):
    """Les URL de vignettes Wikimedia ont été tronquées à 255 caractères dans l'ancienne base :
    on reconstruit le nom de la vignette depuis le nom du fichier d'origine."""
    m = re.match(r"(https://upload\.wikimedia\.org/.+/thumb/\w/\w\w/([^/]+))/(\d+px)-", url or "")
    if m and not url.endswith(m.group(2)):
        return f"{m.group(1)}/{m.group(3)}-{m.group(2)}"
    return url


def element_depuis_ligne(type_slug: str, row: dict, slugs_pris: set) -> Element:
    if row.get("prenom") or type_slug == "personnalite":
        prenom, nom = (row.get("prenom") or "").strip(), (row.get("nom") or "").strip()
        # « Beyoncé Beyoncé », « Aleister Aleister Crowley », « Arthur Roi Arthur » : le prénom est déjà dans le nom.
        titre = nom if prenom and prenom.lower() in nom.lower() else f"{prenom} {nom}".strip()
    else:
        titre = _premier(row, CHAMPS_TITRE) or f"{type_slug} {row['id']}"
    champs_titre = {"prenom", "nom", "titre"} if type_slug == "personnalite" else set(CHAMPS_TITRE)
    description = _premier(row, CHAMPS_DESCRIPTION)
    sous_titre = _premier(row, [c for c in CHAMPS_SOUS_TITRE if c not in champs_titre])
    exclus = CHAMPS_TECHNIQUES | champs_titre | set(CHAMPS_IMAGE) | {"biographie", "description", "prenom"}
    if type_slug in TYPES_PERSONNES:
        exclus |= CHAMPS_PRIVES
    else:
        exclus.add("user_id")
    donnees = {k: _json_ou_texte(v) for k, v in row.items()
               if k not in exclus and v not in VIDES}
    if "description_longue" in donnees and donnees["description_longue"] == description:
        donnees.pop("description_longue")
    base = slugify(titre, max_length=180) or str(row["id"])
    slug, n = base, 2
    while slug in slugs_pris:
        slug, n = f"{base}-{n}", n + 1
    slugs_pris.add(slug)
    return Element(type_slug=type_slug, slug=slug, titre=titre[:300], sous_titre=sous_titre,
                   description=description, image=reparer_image(_premier(row, CHAMPS_IMAGE)), donnees=donnees,
                   source_table=type_slug, source_id=int(row["id"]))


def importer(lignes: dict[str, list[dict]], session) -> dict[str, int]:
    session.execute(delete(element_terme))
    session.execute(delete(Element))
    session.execute(delete(Terme).where(Terme.parent_id.is_not(None)))
    session.execute(delete(Terme))
    compte = {}
    for type_slug in sorted(TYPES_REELS):
        rows = [r for r in lignes.get(type_slug, []) if (type_slug, int(r["id"])) not in FICHES_DEMO]
        if not rows:
            continue
        genres, categories = {}, {}
        for g in lignes.get(f"{type_slug}_types", []):
            genres[int(g["id"])] = Terme(type_slug=type_slug, kind="genre", slug=g["slug"], nom=g["nom"],
                                    description=g.get("description"), icone=g.get("icon"),
                                    ordre=int(g.get("ordre") or 0))
        for c in lignes.get(f"{type_slug}_categories", []):
            categories[int(c["id"])] = Terme(type_slug=type_slug, kind="categorie",
                                        slug=f"{genres[int(c['type_id'])].slug}-{c['slug']}" if int(c["type_id"]) in genres else c["slug"],
                                        nom=c["nom"], description=c.get("description"), icone=c.get("icon"),
                                        ordre=int(c.get("ordre") or 0), parent=genres.get(int(c["type_id"])))
        session.add_all([*genres.values(), *categories.values()])
        slugs = set()
        for row in rows:
            el = element_depuis_ligne(type_slug, row, slugs)
            el.termes = [genres[i] for i in sorted(_ids(row, "types_ids", "type_id")) if i in genres]
            el.termes += [categories[i] for i in sorted(_ids(row, "categories_ids", "category_id")) if i in categories]
            session.add(el)
        compte[type_slug] = len(rows)
    session.commit()
    return compte


def main():
    p = argparse.ArgumentParser()
    p.add_argument("dump")
    p.add_argument("--url", default=DATABASE_URL)
    a = p.parse_args()
    lignes = parse_dump(Path(a.dump).read_text(encoding="utf-8"))
    lignes = {t: r for t, r in lignes.items() if t not in TABLES_IGNOREES}
    engine, Session = make_session_factory(a.url)
    Base.metadata.create_all(engine)
    with Session() as s:
        for t, n in importer(lignes, s).items():
            print(f"{t:15} {n}")


if __name__ == "__main__":
    main()
