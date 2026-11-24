"""Base de données : PostgreSQL `weball` en production, SQLite possible en local et en tests."""
import os
from datetime import UTC, datetime


from sqlalchemy import (JSON, Column, DateTime, ForeignKey, Integer, String, Table, Text,
                        UniqueConstraint, create_engine)
from sqlalchemy.orm import DeclarativeBase, Mapped, mapped_column, relationship, sessionmaker

def _maintenant():
    return datetime.now(UTC).replace(tzinfo=None)


DATABASE_URL = os.environ.get("WEBALL_DATABASE_URL", "postgresql+psycopg://weball@localhost/weball")


class Base(DeclarativeBase):
    pass


element_terme = Table(
    "element_terme", Base.metadata,
    Column("element_id", ForeignKey("element.id", ondelete="CASCADE"), primary_key=True),
    Column("terme_id", ForeignKey("terme.id", ondelete="CASCADE"), primary_key=True),
)


class Terme(Base):
    """Taxonomie propre à un type d'élément : genre (ex. Artiste) > catégorie (ex. Peintre)."""
    __tablename__ = "terme"
    __table_args__ = (UniqueConstraint("type_slug", "kind", "slug"),)

    id: Mapped[int] = mapped_column(primary_key=True)
    type_slug: Mapped[str] = mapped_column(String(50), index=True)
    kind: Mapped[str] = mapped_column(String(20))  # 'genre' | 'categorie'
    slug: Mapped[str] = mapped_column(String(120))
    nom: Mapped[str] = mapped_column(String(200))
    description: Mapped[str | None] = mapped_column(Text)
    icone: Mapped[str | None] = mapped_column(String(50))
    ordre: Mapped[int] = mapped_column(Integer, default=0)
    parent_id: Mapped[int | None] = mapped_column(ForeignKey("terme.id", ondelete="CASCADE"))

    parent: Mapped["Terme | None"] = relationship(remote_side=[id], back_populates="enfants")
    enfants: Mapped[list["Terme"]] = relationship(back_populates="parent", order_by="Terme.ordre")


class Element(Base):
    """Un élément de l'annuaire, quel que soit son type (personnalité, autorité, boutique...)."""
    __tablename__ = "element"
    __table_args__ = (UniqueConstraint("type_slug", "slug"),)

    id: Mapped[int] = mapped_column(primary_key=True)
    type_slug: Mapped[str] = mapped_column(String(50), index=True)
    slug: Mapped[str] = mapped_column(String(200))
    titre: Mapped[str] = mapped_column(String(300))
    sous_titre: Mapped[str | None] = mapped_column(String(300))
    description: Mapped[str | None] = mapped_column(Text)
    image: Mapped[str | None] = mapped_column(String(500))
    # Champs propres au type (dates, œuvres, citations, adresse...), clés d'origine du PHP.
    donnees: Mapped[dict] = mapped_column(JSON, default=dict)
    source_table: Mapped[str | None] = mapped_column(String(50))
    source_id: Mapped[int | None] = mapped_column(Integer)
    cree_le: Mapped[datetime] = mapped_column(DateTime, default=_maintenant)
    modifie_le: Mapped[datetime] = mapped_column(DateTime, default=_maintenant, onupdate=_maintenant)

    termes: Mapped[list[Terme]] = relationship(secondary=element_terme, order_by=Terme.ordre)


def make_session_factory(url: str = DATABASE_URL):
    engine = create_engine(url, future=True)
    return engine, sessionmaker(engine, expire_on_commit=False)
