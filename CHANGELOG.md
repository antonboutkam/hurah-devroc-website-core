# Changelog

## 0.1.0-beta.2 — 2026-10-20

- Composer-packagenaam gewijzigd naar `hurah/devroc-website-core`.
- PHP-namespace `DevRoc\WebsiteCore` en de API blijven ongewijzigd.

## 0.1.0-beta.1 — 2026-10-20

Eerste beta van `devroc/website-core`.

- Optionele environment-inlezing, Twig-rendering, Markdown/JSON en lokale PHP-server.
- Hersteld: een publieke symlink kan geen verborgen bestand/map meer ontsluiten.
- Veilige dependency-ondergrenzen voor Twig (^3.27), CommonMark (^2.10.2)
  en Symfony Process (^7.4.5).
- Zelfstandige package-tests, inclusief echte HTTP-regressie en JUnit-output voor CI.
- Expliciete 0BSD-licentie; dependencies behouden hun eigen licentie.

Beta: de API kan nog wijzigen. Alleen beheerde content en vertrouwde templates;
zie de vertrouwensgrenzen in README.md. Packagist-publicatie staat los van deze tag.
