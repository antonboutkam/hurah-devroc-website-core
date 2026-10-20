# Package-tests

Uitvoeren vanuit de package-root: `composer test` of `composer test:ci`.

- DevelopmentServerTest: normale assets, verboden URL's, symlinkdoelen en echte HTTP.
- ContentReaderTest: Markdown-filtering, JSON en de rootgrens.
- TemplateRendererTest: HTML-escaping, onbekende variabelen en templatepaden.
- EnvironmentTest: optionele .env en bestaande instellingen behouden.
- ServerStartCommandTest: ongeldige poorten en ontbrekende documentroot.
- TemporaryDirectoryTestCase: geïsoleerde fixtures die na iedere test worden opgeruimd.

Voorbeeld: verander tijdelijk de symlinkdoelcontrole in DevelopmentServer en voer
`composer test -- --filter DevelopmentServerTest` uit. De tests horen dan rood te
worden. Herstel daarna de code. Gebruik hiervoor nooit echte geheimen.
