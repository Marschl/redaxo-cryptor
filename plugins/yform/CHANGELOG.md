Changelog
=========

Version 0.1.7 - 24.09.2019
------------------------------

## Updates

* Kompatibiliät zu yForm Manager 3.x hergestellt.

Version 0.1.6 - 08.03.2018
------------------------------

## Updates

* Bugfix: cryptor[yForm] Kompatibilität mit Addon Quicknavi hergestellt (danke @Daniel)
* Bugfix: cryptor[yForm] Unterstützung von Installationen in Unterordnern


Version 0.1.5 - 17.02.2018
------------------------------

## Updates

* Kompatibiliät zu yForm Manager 2.3 hergestellt.
* yForm Manager 2.3 ist nun Voraussetzung.


Version 0.1.4 - 11.02.2018
------------------------------

## Updates

* Wird eine auto-encrypt Einstellung geändert, werden die entsprechenden Tabelleneinträge zugleich ver- bzw. entschlüsselt.


Version 0.1.3 - 11.02.2018
------------------------------

## Updates

* yForm-Tabellen-Ergebnisse können nun entschlüsselt exportiert werden (entsprechende Permission vorausgesetzt).


Version 0.1.2 - 04.02.2018
------------------------------

## Update & Bugfixes

* Bugfix: Extensionpoints werden nun noch einmalig registriert
* Uploads können ebenfalls automatisch mit dem Eintrag gelöscht werden (yForm-Feldtyp upload)
* Autoencrypt/Autodelete sind nun eigene Klassen


Version 0.1.1 - 27.01.2018
------------------------------

## Update "Autodelete"

* Es ist jetzt möglich, bei yForm-Felder des Typs 'datestamp' einen Zeitraum zu definieren, nach dem sie automatisch gelöscht werden sollen.
* Das Format des datestamps Feldes sollte vorzugsweise Y-m-d H:i:s bzw. 'mysql' oder einfach leer sein.
* Zur regelmäßigen Ausführung ist ein Cronjob bzw. Php-Callback auf cryptor_yform::executeAutodelete() nötig.
* Der Cronjob kann auch nur für eine Tabelle getriggert werden, dazu den Tabellennamen mitgeben cryptor_yform::executeAutodelete('my_table_name').

Version 0.1.0 - 21.01.2018
------------------------------

## Initial Release

* Bei Inserts in eine yForm-Tabelle werden ausgewählte Werte nachträglich verschlüsselt
* Greift bei Extensionpoint "REX_YFORM_SAVED"
* Im cryptor_yform Backend können die zu verschlüsselnden Felder ausgewählt werden.
* Automatisch verschlüsselbare yForm-Feldtypen sind aktuell: text, textarea, email
* Im yForm-Manager-Backend können verschlüsselte Werte unentschlüsselt dargestellt und editiert werden 

