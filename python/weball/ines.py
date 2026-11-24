"""Synchro avec l'arborescence INES.

INES est la source des noms et descriptions des 5 groupes et 25 types : ses nœuds
`weball-<slug>` (pôles pour les groupes, référentiels pour les types) remplacent les valeurs du
catalogue local. Si INES ne répond pas, Weball garde son catalogue : le site ne tombe jamais
à cause d'INES.

ponytail: lecture au démarrage seulement ; un changement dans INES demande un redémarrage
de Weball. Passer à un rafraîchissement périodique si INES change souvent.
"""
import json
import logging
import os
from dataclasses import replace
from urllib.request import Request, urlopen

from .catalogue import GROUPES, Groupe

INES_API_URL = os.environ.get("WEBALL_INES_API_URL", "")  # ex. http://ines-backend:8080
log = logging.getLogger("weball.ines")


def noeuds_weball(arbre) -> dict[str, dict]:
    """Renvoie {slug: nœud} pour chaque nœud `weball-<slug>` de l'arborescence INES."""
    trouves, pile = {}, list(arbre if isinstance(arbre, list) else [arbre])
    while pile:
        n = pile.pop()
        if str(n.get("id", "")).startswith("weball-"):
            trouves[n["id"].removeprefix("weball-")] = n
        pile.extend(n.get("children") or [])
    return trouves


def fusionner(noeuds: dict[str, dict], groupes: tuple[Groupe, ...] = GROUPES) -> tuple[Groupe, ...]:
    """Applique les noms et descriptions d'INES au catalogue. Les couleurs restent celles de
    Weball (INES impose une couleur par type de nœud, pas par groupe). Les slugs et l'ordre
    5 x 5 restent ceux de Weball (ils font les URL) : un nœud INES inconnu est ignoré."""
    def maj(objet, **extra):
        n = noeuds.get(objet.slug, {})
        return replace(objet, nom=n.get("name") or objet.nom,
                       description=n.get("description") or objet.description, **extra)

    return tuple(maj(g, types=tuple(maj(t) for t in g.types)) for g in groupes)


def charger_groupes(url: str = INES_API_URL, timeout: float = 3) -> tuple[Groupe, ...]:
    if not url:
        return GROUPES
    try:
        with urlopen(Request(f"{url.rstrip('/')}/api/arborescence/", headers={"Accept": "application/json"}),
                     timeout=timeout) as r:
            arbre = json.load(r)
    except Exception as e:  # INES indisponible : on garde le catalogue local
        log.warning("Arborescence INES illisible (%s), catalogue local utilisé", e)
        return GROUPES
    noeuds = noeuds_weball(arbre)
    if not noeuds:
        log.warning("Aucun nœud weball-* dans l'arborescence INES, catalogue local utilisé")
        return GROUPES
    return fusionner(noeuds)
