"""Application Weball : l'annuaire de l'écosystème WAO (FastAPI + Jinja2 rendu côté serveur)."""
from pathlib import Path

from fastapi import Depends, FastAPI, HTTPException, Request
import json
import os

from fastapi.responses import PlainTextResponse, RedirectResponse, Response
from fastapi.staticfiles import StaticFiles
from fastapi.templating import Jinja2Templates
from sqlalchemy import func, or_, select
from sqlalchemy.orm import Session, selectinload

from . import ines
from .catalogue import OUTILS
from .db import Element, Terme, make_session_factory

ICI = Path(__file__).parent
# Noms, descriptions et couleurs lus dans l'arborescence INES au démarrage (catalogue local sinon).
GROUPES = ines.charger_groupes()
GROUPES_PAR_SLUG = {g.slug: g for g in GROUPES}
TYPES_PAR_SLUG = {t.slug: (g, t) for g in GROUPES for t in g.types}


def groupe_et_type(groupe_slug: str, type_slug: str):
    found = TYPES_PAR_SLUG.get(type_slug)
    return found if found and found[0].slug == groupe_slug else None


PAR_PAGE = 24
SITE_URL = os.environ.get("WEBALL_SITE_URL", "https://weball.weballone.com").rstrip("/")

app = FastAPI(title="Weball", docs_url=None, redoc_url=None)
app.mount("/static", StaticFiles(directory=ICI / "static"), name="static")
# Photos et logos hérités du site PHP (chemins /photos/x.jpg et /logos/x.png en base).
PUBLIC = ICI.parents[1] / "public"
for dossier in ("photos", "logos"):
    if (PUBLIC / dossier).is_dir():
        app.mount(f"/{dossier}", StaticFiles(directory=PUBLIC / dossier), name=dossier)
templates = Jinja2Templates(directory=ICI / "templates")
templates.env.globals.update(GROUPES=GROUPES, OUTILS=OUTILS)

engine, SessionLocal = make_session_factory()


def get_db():
    with SessionLocal() as s:
        yield s


def _comptes(db: Session) -> dict[str, int]:
    return dict(db.execute(select(Element.type_slug, func.count()).group_by(Element.type_slug)).all())


def url_fiche(el: Element) -> str:
    groupe, _ = TYPES_PAR_SLUG[el.type_slug]
    return f"/{groupe.slug}/{el.type_slug}/{el.slug}"


def canonique(request: Request) -> str:
    """URL canonique : domaine public, sans paramètres sauf les filtres et la page."""
    garder = {k: v for k, v in request.query_params.items() if k in ("genre", "categorie", "page") and v}
    if garder.get("page") == "1":
        garder.pop("page")
    qs = "&".join(f"{k}={v}" for k, v in sorted(garder.items()))
    return f"{SITE_URL}{request.url.path}" + (f"?{qs}" if qs else "")


templates.env.globals.update(url_fiche=url_fiche, canonique=canonique, SITE_URL=SITE_URL)
LIBELLES = {
    "nationalite": "Nationalité", "date_naissance": "Naissance", "date_deces": "Décès",
    "lieu_naissance": "Lieu de naissance", "lieu_deces": "Lieu de décès", "cause_deces": "Cause du décès",
    "oeuvres_principales": "Œuvres principales", "site_web": "Site web", "wikipedia_url": "Wikipédia",
    "reseaux_sociaux": "Réseaux sociaux", "evenements": "Événements", "videos": "Vidéos",
    "prix_recompenses": "Prix et récompenses", "heritage_culturel": "Héritage culturel",
    "musees_expositions": "Musées et expositions", "lieux_associes": "Lieux associés",
    "produits_associes": "Produits associés", "liens_externes": "Liens externes",
    "galerie_photos": "Galerie", "date_creation_org": "Création", "chiffre_affaires": "Chiffre d'affaires",
    "description_longue": "Présentation", "realisations": "Réalisations", "activites": "Activités",
    "forme_juridique": "Forme juridique", "siret": "SIRET", "tva_intracommunautaire": "TVA intracommunautaire",
    "engagement_rse": "Engagement RSE", "chiffres_cles": "Chiffres clés", "comite_direction": "Comité de direction",
}
MOIS = ("janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre",
        "octobre", "novembre", "décembre")


def libelle(cle: str) -> str:
    return LIBELLES.get(cle) or cle.replace("_", " ").capitalize()


def date_fr(v):
    """2024-03-09 → 9 mars 2024 ; laisse le reste intact."""
    if isinstance(v, str) and len(v) == 10 and v[4] == v[7] == "-" and v[:4].isdigit():
        a, m, j = v.split("-")
        if 1 <= int(m) <= 12 and int(j):
            return f"{int(j)} {MOIS[int(m) - 1]} {int(a)}"
    return v


templates.env.globals["libelle"] = libelle
templates.env.tests["images"] = lambda v: all(
    isinstance(x, str) and x.startswith("http") and x.lower().rsplit(".", 1)[-1] in ("jpg", "jpeg", "png", "webp", "gif", "svg")
    for x in v)
templates.env.filters["date_fr"] = date_fr
templates.env.add_extension("jinja2.ext.do")
templates.env.filters["tojson_ld"] = lambda d: json.dumps(d, ensure_ascii=False).replace("</", "<\\/")


@app.get("/")
def accueil(request: Request, db: Session = Depends(get_db)):
    recents = db.scalars(select(Element).where(Element.type_slug.in_(TYPES_PAR_SLUG))
                         .order_by(Element.cree_le.desc(), Element.id.desc()).limit(8)).all()
    return templates.TemplateResponse(request, "accueil.html",
                                      {"comptes": _comptes(db), "recents": recents})


@app.get("/recherche")
def recherche(request: Request, q: str = "", db: Session = Depends(get_db)):
    resultats = []
    q = q.strip()[:100]
    if q:
        motif = f"%{q}%"
        resultats = db.scalars(select(Element).where(
            Element.type_slug.in_(TYPES_PAR_SLUG),
            or_(Element.titre.ilike(motif), Element.sous_titre.ilike(motif), Element.description.ilike(motif)),
        ).order_by(Element.titre).limit(60)).all()
    return templates.TemplateResponse(request, "recherche.html", {"q": q, "resultats": resultats})


@app.get("/robots.txt", response_class=PlainTextResponse)
def robots():
    return f"User-agent: *\nDisallow: /recherche\nSitemap: {SITE_URL}/sitemap.xml\n"


@app.get("/sitemap.xml")
def sitemap(db: Session = Depends(get_db)):
    urls = [f"{SITE_URL}/"]
    for g in GROUPES:
        urls.append(f"{SITE_URL}/{g.slug}")
        urls += [f"{SITE_URL}/{g.slug}/{t.slug}" for t in g.types]
    lignes = db.execute(select(Element.type_slug, Element.slug, Element.modifie_le)
                        .where(Element.type_slug.in_(TYPES_PAR_SLUG))).all()
    corps = "".join(f"<url><loc>{u}</loc></url>" for u in urls)
    corps += "".join(f"<url><loc>{SITE_URL}/{TYPES_PAR_SLUG[t][0].slug}/{t}/{sl}</loc>"
                     f"<lastmod>{m:%Y-%m-%d}</lastmod></url>" for t, sl, m in lignes)
    xml = f'<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">{corps}</urlset>'
    return Response(xml, media_type="application/xml")


# --- Anciennes URL du site PHP (redirections permanentes) ---------------------------------

@app.get("/{type_slug}_detail")
def ancienne_fiche(type_slug: str, id: int = 0, db: Session = Depends(get_db)):
    if type_slug not in TYPES_PAR_SLUG:
        raise HTTPException(404)
    el = db.scalar(select(Element).where(Element.type_slug == type_slug, Element.source_id == id))
    if not el:
        raise HTTPException(404)
    return RedirectResponse(url_fiche(el), status_code=301)


@app.get("/{slug}")
def groupe_ou_ancien_type(request: Request, slug: str, db: Session = Depends(get_db)):
    if slug in TYPES_PAR_SLUG:  # ancienne URL /personnalite
        return RedirectResponse(f"/{TYPES_PAR_SLUG[slug][0].slug}/{slug}", status_code=301)
    groupe = GROUPES_PAR_SLUG.get(slug)
    if not groupe:
        raise HTTPException(404)
    return templates.TemplateResponse(request, "groupe.html", {"groupe": groupe, "comptes": _comptes(db)})


@app.get("/{groupe_slug}/{type_slug}")
def liste(request: Request, groupe_slug: str, type_slug: str, genre: str = "", categorie: str = "",
          page: int = 1, db: Session = Depends(get_db)):
    found = groupe_et_type(groupe_slug, type_slug)
    if not found:
        raise HTTPException(404)
    groupe, type_el = found
    genres = db.scalars(select(Terme).where(Terme.type_slug == type_slug, Terme.kind == "genre")
                        .options(selectinload(Terme.enfants)).order_by(Terme.ordre, Terme.nom)).all()
    requete = select(Element).where(Element.type_slug == type_slug)
    actif = None
    if categorie or genre:
        actif = db.scalar(select(Terme).where(Terme.type_slug == type_slug,
                                              Terme.kind == ("categorie" if categorie else "genre"),
                                              Terme.slug == (categorie or genre)))
        if not actif:
            raise HTTPException(404)
        requete = requete.where(Element.termes.any(Terme.id == actif.id))
    total = db.scalar(select(func.count()).select_from(requete.subquery()))
    page = max(1, page)
    elements = db.scalars(requete.order_by(Element.titre).offset((page - 1) * PAR_PAGE).limit(PAR_PAGE)).all()
    return templates.TemplateResponse(request, "liste.html", {
        "groupe": groupe, "type": type_el, "genres": genres, "actif": actif, "elements": elements,
        "total": total, "page": page, "pages": max(1, -(-total // PAR_PAGE)),
    })


@app.get("/{groupe_slug}/{type_slug}/{slug}")
def fiche(request: Request, groupe_slug: str, type_slug: str, slug: str, db: Session = Depends(get_db)):
    found = groupe_et_type(groupe_slug, type_slug)
    if not found:
        raise HTTPException(404)
    el = db.scalar(select(Element).where(Element.type_slug == type_slug, Element.slug == slug)
                   .options(selectinload(Element.termes)))
    if not el:
        raise HTTPException(404)
    groupe, type_el = found
    return templates.TemplateResponse(request, "fiche.html", {
        "groupe": groupe, "type": type_el, "el": el, "jsonld": jsonld_fiche(el, groupe, type_el)})


SCHEMA_TYPES = {"personnalite": "Person", "localite": "Place", "ecole": "EducationalOrganization",
                "autorite": "Organization", "entite": "Organization", "banque": "BankOrCreditUnion",
                "media": "Organization", "boutique": "Store", "publication": "CreativeWork",
                "formation": "Course", "plateforme": "WebSite"}


def _image_absolue(image: str | None) -> str | None:
    return f"{SITE_URL}{image}" if image and image.startswith("/") else image


def jsonld_fiche(el: Element, groupe, type_el) -> list[dict]:
    """Données structurées schema.org de la fiche et de son fil d'Ariane."""
    d = el.donnees or {}
    url = f"{SITE_URL}{url_fiche(el)}"
    chose = {"@context": "https://schema.org", "@type": SCHEMA_TYPES.get(el.type_slug, "Thing"),
             "@id": url, "url": url, "name": el.titre}
    if el.description:
        chose["description"] = el.description[:500]
    if el.image:
        chose["image"] = _image_absolue(el.image)
    liens = [v for k in ("wikipedia_url", "site_web", "linkedin_url", "twitter_url") if (v := d.get(k))]
    if liens:
        chose["sameAs"] = liens
    if el.type_slug == "personnalite":
        for cle, prop in (("date_naissance", "birthDate"), ("date_deces", "deathDate"),
                          ("lieu_naissance", "birthPlace"), ("nationalite", "nationality"),
                          ("profession", "jobTitle")):
            if d.get(cle):
                chose[prop] = d[cle]
    ariane = {"@context": "https://schema.org", "@type": "BreadcrumbList", "itemListElement": [
        {"@type": "ListItem", "position": i, "name": nom, "item": f"{SITE_URL}{chemin}"}
        for i, (nom, chemin) in enumerate([("Weball", "/"), (groupe.nom, f"/{groupe.slug}"),
                                           (type_el.nom_pluriel, f"/{groupe.slug}/{type_el.slug}"),
                                           (el.titre, url_fiche(el))], start=1)]}
    return [chose, ariane]
