"""Application Weball : l'annuaire de l'écosystème WAO (FastAPI + Jinja2 rendu côté serveur)."""
from pathlib import Path

from fastapi import Depends, FastAPI, HTTPException, Request
from fastapi.responses import RedirectResponse
from fastapi.staticfiles import StaticFiles
from fastapi.templating import Jinja2Templates
from sqlalchemy import func, or_, select
from sqlalchemy.orm import Session, selectinload

from .catalogue import GROUPES, GROUPES_PAR_SLUG, OUTILS, TYPES_PAR_SLUG, groupe_et_type
from .db import Element, Terme, make_session_factory

ICI = Path(__file__).parent
PAR_PAGE = 24

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


templates.env.globals["url_fiche"] = url_fiche


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
    return templates.TemplateResponse(request, "fiche.html", {"groupe": groupe, "type": type_el, "el": el})
