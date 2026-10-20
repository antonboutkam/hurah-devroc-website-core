# Website Core

`hurah/devroc-website-core` is de gedeelde PHP-basis voor zelfstandige websites.
Het bevat geen routes, content, database of CMS. Controllers en Twig-views blijven
in de eigen applicatie. Vereist PHP 8.3 of hoger binnen PHP 8.

## Installeren

De huidige beta is `v0.1.0-beta.2`. Nadat de publieke repository op Packagist is
geregistreerd, installeer je die expliciet met:

```bash
composer require hurah/devroc-website-core:0.1.0-beta.2
```

Dit commando veronderstelt publicatie; een lokale tag alleen maakt het package
nog niet beschikbaar op Packagist. Voor gezamenlijke ontwikkeling kan een Composer
path-repository naar deze map worden gebruikt. In de twee websitetemplates staat
Composer onder `website/src`, met pad `../../../website-core`.

## Onderdelen

- `Environment`: leest optioneel de root-.env van de applicatie, zonder bestaande
  waarden te overschrijven.
- `TemplateRenderer`: Twig met HTML-autoescaping, strict variables en debug uit.
- `ContentReader`: Markdown en JSON uit vaste bestanden binnen een expliciete root.
- `DevelopmentServer`: laat uitsluitend bestaande publieke assets door; controleert
  zowel het URL-pad als het opgeloste symlinkdoel op verborgen bestanden/mappen.
- `Command/ServerStartCommand`: start de lokale PHP-server op 127.0.0.1, met een
  gevalideerde poort en Symfony Process. Alleen voor lokale ontwikkeling.

```php
use DevRoc\WebsiteCore\ContentReader;
use DevRoc\WebsiteCore\TemplateRenderer;

$content = new ContentReader('/absoluut/pad/naar/website');
$templates = new TemplateRenderer('/absoluut/pad/naar/website/src/modules');
echo $templates->render('Project/view.twig', [
    'body' => $content->markdown('project/project.md'),
]);
```

In de view mag de door ContentReader geproduceerde Markdown-HTML via `{{ body|raw }}`
worden getoond. Gewone tekstgegevens render je met `{{ naam }}`, zonder raw.

## Vertrouwensgrenzen

Gebruik uitsluitend gecontroleerde vaste contentpaden, nooit rechtstreeks URL- of
formulierinvoer. De ingestelde root is de veiligheidsgrens; de lezer maakt geen
onderscheid tussen bestanden binnen die root. Symlinks buiten de root zijn verboden.
Templates zijn vertrouwde applicatiecode: Twig-sandboxing is niet ingeschakeld.

Markdown verwijdert ruwe HTML, blokkeert onveilige linkprotocollen en begrenst nesting.
De lezer heeft nog geen bestandsgroottelimiet of delimiterlimiet: gebruik hem voorlopig
voor beheerde repositorycontent, niet rechtstreeks voor onbetrouwbare uploads.
Externe afbeeldingen/links kunnen door de browser worden geladen. Geen credentials
of logs in public zetten; de lokale server is geen productieserver.

De dependency-ondergrenzen sluiten de bij deze beta bekende kwetsbare oudere
versies van Twig, CommonMark en Symfony Process uit. Blijf ook zelf `composer audit`
uitvoeren; een minimumversie garandeert niet dat toekomstige advisories ontbreken.

## Tests en voorbereiding voor CircleCI

Vanuit de package-root:

```bash
composer install
composer check
composer audit
composer test:ci
```

`composer check` valideert composer.json en voert de tests uit. `test:ci` schrijft
ook `build/junit.xml`, bruikbaar als CircleCI-testresultaat. De tests gebruiken
uitsluitend tijdelijke fixtures en één lokale loopbackserver. Voor de
symlink-regressies moet CI op Linux draaien; op systemen zonder symlinkrechten
worden die tests overgeslagen. Dit hoort zichtbaar te blijven in CI.

Test ook de ondergrenzen in een afzonderlijke schone checkout:

```bash
composer update --prefer-lowest --prefer-stable --no-interaction
composer check
composer audit
```

Een library-lockfile wordt niet meegecommit. De geïnstalleerde website-applicaties
houden wel hun eigen composer.lock bij. CircleCI-configuratie wordt later toegevoegd;
er is nu geen externe CI-job aangemaakt of geactiveerd.

## Licentie en release

Eigen packagecode valt onder [0BSD](LICENSE): een zeer ruime licentie zonder
attributievoorwaarde. Dependencies behouden hun eigen licenties.
Zie [CHANGELOG.md](CHANGELOG.md) en [SECURITY.md](SECURITY.md).
Composer leidt de versie af uit de Git-tag; composer.json bevat bewust geen version-veld.

Bronnen: [0BSD](https://opensource.org/license/0bsd),
[CommonMark security](https://commonmark.thephpleague.com/2.x/security/),
[Packagist-publicatie](https://packagist.org/about).
# hurah-devroc-website-core
