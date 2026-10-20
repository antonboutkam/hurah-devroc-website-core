# Gedeelde PHP-klassen

Deze map is de PSR-4-root voor `DevRoc\WebsiteCore\`. De klassen ontvangen altijd
paden van de aanroepende app. Ze zoeken niet vanaf hun eigen packagepad naar content.
Daardoor werkt dezelfde code met een symlink en later met een apart geinstalleerd package.

`Environment` leest lokale instellingen; `TemplateRenderer` maakt HTML met Twig;
`DevelopmentServer` controleert statische aanvragen. Onder `Command/` staat de server-CLI.

Voorbeeld: `Environment::load('/pad/naar/studentenportfolio')` leest daar de optionele
`.env`. Controllers, routes en studentinhoud horen in de eigen applicatie. Pas deze
infrastructuur alleen bewust aan en controleer daarna beide websites.

`ContentReader.php` leest Markdown/JSON uit een expliciete website-map. Bijvoorbeeld:
`$content->markdown("project/project.md")`. Pas pagina-inhoud aan in de apps;
wijzig hier alleen gedeelde lees- en rendertechniek.
