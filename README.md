eVOC - Englisch Vokabeltrainer
==============================

Dies ist ein sehr einfacher Vokabeltrainer, der für das Lernen englischer
Vokabeln gedacht ist.

Funktionen
----------
* Vokabelliste
* Erkennung von Synonymen
* Druckansicht der Vorabelliste
* Vokabel-Export nach CSV
* Trainer mit Statistik
* Mehrbenutzerfähig
* Administration per Webinterface

Bugs/Fehler
-----------
* die zufällige Auswahl der Wörter beim Trainer ist nicht zufällig genug

Systemvoraussetzungen
---------------------
* PHP5
* MySQL
* Apache-Webserver mit aktiviertem ```mod_rewrite``` und PHP-Unterstützung

Installation
------------
Alle Dateien auf den Webserver laden und per Web-Browser ansurfen. Alle nötigen
Kofigurationseinstellungen werden per Webinterface abgefragt, und die
entsprechende Konfigurationsdatei wird erstellt. Danach ist eVOC einsatzbereit.

Manuelle Konfiguration
----------------------
Der automatische Installer legt eine ```settings.cfg``` an, in der die
Konfiguration gespeichert ist. Wenn sich aus irgend einem Grund die
Zugangsdaten zur Datenbank ändern, oder die Registrierung von Usern deaktiviert
werden soll, so lässt sich dies hier anpassen.

* ```$MYSQL['hostname']```: Hostname des Datenbankservers
* ```$MYSQL['database']```: Datenbankname
* ```$MYSQL['username']```: MySQL-Benutzername
* ```$MYSQL['password']```: Passwort des MySQL-Benutzers
* ```$MYSQL['prefix']```: Tabellenpräfix
* ```$SETTINGS['allow_register']```: Registrierung von Accounts via
  Webinterface zulassen oder verweigern.

FAQ
---
### Wie werden Passwörter gespeichert?
Als ungesalzenes SHA-1

### Kann ein User Vokabeln löschen?
Nur bedingt. Wenn er ein Vokabel gelöscht hat, so ist es zwar für andere User
unsichtbar, aber ein Administrator kann es noch sehen und auch bei Bedarf
wiederherstellen (oder endgültig löschen).

### Gibt es eine Mobile-Version (also für Telefone o.ä.)?
Nein.

### Kann der Administrator seinen eigenen Account löschen?
Ja. Wenn man das tut muss man ihn per direktem Eingriff in die Datenbank wieder
anlegen!

### Was hat es mit den Gruppen auf sich?
* Gast: kann nur lesend auf die Vokabelliste zugreifen
* Benutzer: kann Vokabeln anlegen, editieren und löschen
* Administrator: kann von Usern gelöschte Vokabeln wiederherstellen und
  auch endgültig löschen sowie Accounts verändern (Gruppe festlegen, Passwort
  setzen, löschen).

### Kann man die Account-Registrierung deaktivieren?
Ja, via ```settings.cfg```.

### Kann man bei deaktivierter Account-Registrierung Accounts anlegen?
Nein, das ist dann nur via Datenbank-Eingriff möglich.

### Wozu muss man den Nachnamen angeben?
Damit auf der Statistikseite ein Name steht, mit dem man auch etwas anfangen
kann, und damit dich der Trainer ganz persönlich fragen kann.
