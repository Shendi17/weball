"""Vérifie l'import d'un export mysqldump et les pages principales, sur une base SQLite jetable."""
import os

os.environ["WEBALL_DATABASE_URL"] = "sqlite://"

from fastapi.testclient import TestClient  # noqa: E402
from sqlalchemy.pool import StaticPool  # noqa: E402
from sqlalchemy import create_engine  # noqa: E402
from sqlalchemy.orm import sessionmaker  # noqa: E402

from tools.import_mysql_dump import importer, parse_dump  # noqa: E402
from weball import main  # noqa: E402
from weball.db import Base, Element  # noqa: E402

DUMP = r"""
CREATE TABLE `personnalite_types` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `ordre` int(11) DEFAULT 0
) ENGINE=InnoDB;
INSERT INTO `personnalite_types` VALUES
(1,'Artiste','artiste',1);
CREATE TABLE `personnalite_categories` (
  `id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL
) ENGINE=InnoDB;
INSERT INTO `personnalite_categories` VALUES
(7,1,'Peintre','peintre');
CREATE TABLE `personnalite` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `biographie` text,
  `email` varchar(255),
  `citations` text,
  `date_deces` date,
  `type_id` int(11),
  `categories_ids` text
) ENGINE=InnoDB;
INSERT INTO `personnalite` VALUES
('3','Kahlo','Frida','Peintre mexicaine, l\'une des plus célèbres.\nNée à Coyoacán.','frida@example.com','[\"Pies, para qué los quiero\"]','1954-07-13','1','[7]'),
('4','Kahlo','Frida',NULL,NULL,NULL,'0000-00-00',NULL,NULL);
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `password` varchar(255)
) ENGINE=InnoDB;
INSERT INTO `users` VALUES (1,'secret');
"""


def _client():
    engine = create_engine("sqlite://", connect_args={"check_same_thread": False}, poolclass=StaticPool)
    Base.metadata.create_all(engine)
    Session = sessionmaker(engine, expire_on_commit=False)
    lignes = {t: r for t, r in parse_dump(DUMP).items() if t != "users"}
    with Session() as s:
        assert importer(lignes, s) == {"personnalite": 2}
    main.SessionLocal = Session
    return TestClient(main.app), Session


def test_import_et_pages():
    client, Session = _client()
    with Session() as s:
        frida = s.query(Element).filter_by(source_id=3).one()
        assert frida.slug == "frida-kahlo" and frida.titre == "Frida Kahlo"
        assert frida.description.startswith("Peintre mexicaine, l'une") and "\n" in frida.description
        assert "email" not in frida.donnees  # coordonnées privées jamais publiées
        assert frida.donnees["citations"] == ["Pies, para qué los quiero"]
        assert sorted(t.nom for t in frida.termes) == ["Artiste", "Peintre"]
        doublon = s.query(Element).filter_by(source_id=4).one()
        assert doublon.slug == "frida-kahlo-2" and "date_deces" not in doublon.donnees

    assert client.get("/").status_code == 200
    page = client.get("/classeur/personnalite?categorie=artiste-peintre").text
    assert "Frida Kahlo" in page and "frida-kahlo-2" not in page
    fiche = client.get("/classeur/personnalite/frida-kahlo").text
    assert "Pies, para qué los quiero" in fiche and "frida@example.com" not in fiche
    assert client.get("/registre/personnalite/frida-kahlo").status_code == 404  # mauvais groupe
    r = client.get("/personnalite_detail?id=3", follow_redirects=False)
    assert r.status_code == 301 and r.headers["location"] == "/classeur/personnalite/frida-kahlo"
    assert "Frida" in client.get("/recherche?q=kahlo").text
