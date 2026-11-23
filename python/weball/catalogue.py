"""Structure de Weball : 5 groupes x 5 types d'éléments, plus les outils.

Chaque groupe correspond à un type de fiche INES (PROFIL, RESSOURCE, INDICATION,
PRODUIT, ACTION), validé par Anthony le 23/11/2026.

ponytail: catalogue en dur tant que la synchro avec l'arborescence INES (étape 2)
n'existe pas ; ensuite il sera lu depuis l'API INES et ce fichier servira de repli.
"""
from dataclasses import dataclass


@dataclass(frozen=True)
class TypeElement:
    slug: str
    nom: str
    nom_pluriel: str
    description: str


@dataclass(frozen=True)
class Groupe:
    slug: str
    nom: str
    fiche_ines: str
    couleur: str
    description: str
    types: tuple[TypeElement, ...]


def _t(slug, nom, pluriel, description):
    return TypeElement(slug, nom, pluriel, description)


GROUPES: tuple[Groupe, ...] = (
    Groupe("classeur", "Classeur", "profil", "#3b5bdb",
           "Qui et quoi : les personnes, autorités, organisations et lieux.", (
               _t("personnalite", "Personnalité", "Personnalités", "Artistes, penseurs, inventeurs, figures historiques et contemporaines."),
               _t("autorite", "Autorité", "Autorités", "Institutions, administrations, entreprises et organismes de référence."),
               _t("annuaire", "Annuaire", "Annuaires", "Répertoires de contacts, professionnels et services."),
               _t("entite", "Entité", "Entités", "Sociétés, associations et structures de toute nature."),
               _t("localite", "Localité", "Localités", "Villes, régions, pays et lieux remarquables."),
           )),
    Groupe("collection", "Collection", "ressource", "#0c8599",
           "Avec quoi : les outils, services, plateformes et réseaux.", (
               _t("instrument", "Instrument", "Instruments", "Outils, logiciels et instruments utiles."),
               _t("office", "Office", "Offices", "Services, bureaux et guichets."),
               _t("plateforme", "Plateforme", "Plateformes", "Sites, applications et plateformes en ligne."),
               _t("banque", "Banque", "Banques", "Banques, néobanques et services financiers."),
               _t("reseau", "Réseau", "Réseaux", "Réseaux sociaux, professionnels et communautés."),
           )),
    Groupe("registre", "Registre", "indication", "#7048e8",
           "Pour savoir : publications, archives, écoles, médias et formations.", (
               _t("publication", "Publication", "Publications", "Livres, revues, articles et études."),
               _t("archive", "Archive", "Archives", "Fonds documentaires et archives."),
               _t("ecole", "École", "Écoles", "Écoles, universités et organismes d'enseignement."),
               _t("media", "Média", "Médias", "Presse, radio, télévision, podcasts et chaînes."),
               _t("formation", "Formation", "Formations", "Cours, cursus et formations en ligne ou sur place."),
           )),
    Groupe("selection", "Sélection", "produit", "#e8590c",
           "Pour obtenir : annonces, catalogues, marchés, boutiques et adhésions.", (
               _t("annonce", "Annonce", "Annonces", "Petites annonces, offres et demandes."),
               _t("catalogue", "Catalogue", "Catalogues", "Catalogues de produits et de services."),
               _t("marche", "Marché", "Marchés", "Marchés, foires et places de marché."),
               _t("boutique", "Boutique", "Boutiques", "Boutiques physiques et en ligne."),
               _t("adhesion", "Adhésion", "Adhésions", "Abonnements, adhésions et cotisations."),
           )),
    Groupe("guide", "Guide", "action", "#2f9e44",
           "Pour agir : projets, carrières, disciplines, concours et campagnes.", (
               _t("projet", "Projet", "Projets", "Projets, initiatives et chantiers."),
               _t("carriere", "Carrière", "Carrières", "Métiers, parcours et offres d'emploi."),
               _t("discipline", "Discipline", "Disciplines", "Sports, arts, sciences et pratiques."),
               _t("concours", "Concours", "Concours", "Concours, prix et compétitions."),
               _t("campagne", "Campagne", "Campagnes", "Campagnes, causes et mobilisations."),
           )),
)

OUTILS: tuple[TypeElement, ...] = (
    _t("cadran", "Cadran", "Cadrans", "Tableaux de bord et indicateurs."),
    _t("journal", "Journal", "Journaux", "Journal d'activité."),
    _t("ecran", "Écran", "Écrans", "Écrans et affichages."),
)

GROUPES_PAR_SLUG = {g.slug: g for g in GROUPES}
TYPES_PAR_SLUG = {t.slug: (g, t) for g in GROUPES for t in g.types}


def groupe_et_type(groupe_slug: str, type_slug: str):
    """Renvoie (groupe, type) si le type appartient bien au groupe, sinon None."""
    found = TYPES_PAR_SLUG.get(type_slug)
    return found if found and found[0].slug == groupe_slug else None


assert len(TYPES_PAR_SLUG) == 25, "Weball compte 5 groupes de 5 types"
