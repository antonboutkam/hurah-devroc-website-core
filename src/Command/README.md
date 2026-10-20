# Gedeelde consolecommando's

`ServerStartCommand.php` start de ingebouwde PHP-server op 127.0.0.1. Het commando
ontvangt de publieke map van de eigen website. Er is geen domeinkeuze nodig.

Voorbeeld vanuit een site: `php website/console.php server:start --port=8081`.
De server stopt met Ctrl+C. Een ongeldige poort levert een foutcode op.
Beheerders kunnen deze serverondersteuning onderhouden; studentcommando's zoals
`voorbeeld:groet` blijven in de eigen `website/src/command/`-map.
