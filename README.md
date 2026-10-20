# Smart Technology en studentenportfolio

Deze werkruimte bevat twee websitetemplates en een gedeeld lokaal Composer-package:

- [Smart Technology](smart-technology/README.md): de teamwebsite.
- [Studentenportfolio](studentenportfolio/README.md): de persoonlijke website.
- [Website Core](website-core/README.md): gedeelde technische basis.

De projecten staan naast elkaar en kunnen later ieder een eigen GitLab-repository krijgen.
De installatiecommando's staan in de README van iedere website.

De eisen en de acht opeenvolgende deelopdrachten staan in [tmp-instructions](tmp-instructions/README.md). De [master-specificatie](tmp-instructions/00_MASTER_SPECIFICATIE.md) blijft leidend.

## Voortgang

| Fase | Status |
| --- | --- |
| 01 Analyse van bestaande website en deployment | Afgerond; bevindingen en voorgestelde besluiten staan in [ANALYSE.md](ANALYSE.md) |
| 02 Structuur en Composer | Afgerond; beide Composer-installaties en lokale runtimecontroles geslaagd. Zie [FASE02.md](FASE02.md) |
| 03 Routing en content | Lokaal afgerond; PHP-server en Apache getest. Zie [FASE03-06.md](FASE03-06.md) |
| 04 Teamwebsite | Afgerond met fictieve voorbeeldinhoud |
| 05 Studentenportfolio | Afgerond met fictieve inhoud en gedocumenteerde importvoorbereiding |
| 06 Tests en CI | Lokale tests groen, CI voorbereid; echte pipeline wacht op de getagde core-bron |
| 07 Deployment | Gestart: actuele hook gecontroleerd, beheer/testplan en open punten in [FASE07.md](FASE07.md) |
| 08 Documentatie en acceptatie | Gepland |

De analyse betreft de lokale broncode en lokaal uitgevoerde controles. Productieconfiguratie, GitLab-instellingen en bestaande live websites zijn nog niet geverifieerd.

De [hookfix](deploy-hook-patch/README.md) is volgens terugkoppeling van de opdrachtgever
gecontroleerd toegepast en gedeployed. Een ontbrekend `website/site.json` betekent
standaard publiceren. De praktijktest is nog open en wordt uitgevoerd zodra de nieuwe
opzet testbaar is. Controleer dan exact SHA, publiceren, depubliceren, herpubliceren
en behoud van de vorige versie/status bij fouten. De nieuwe websites zijn niet gedeployed.
# hurah-devroc-website-core
# hurah-devroc-website-core
