Über das Cryptor/yForm-Plugin können yForm-Datenbankeinträge automatisch verschlüsselt und (nach einem definierten Zeitraum) gelöscht werden. 
Hintergrund ist die Datenschutz-Grundverordnung (DSVGO) die ab dem 25. Mai 2018 vollumfänglich in Kraft tritt. 
Durch dieses Plugin können (sensible) personenbezogene Daten anonymisiert, geschützt und entfernt werden.

* Informationen zur DSVGO: https://dsgvo-gesetz.de/
* Anforderungen an die Sicherheit der Datenverarbeitung: https://dsgvo-gesetz.de/bdsg-neu/64-bdsg-neu/

### Automatische Verschlüsselung
Die Verschlüsselung greift bei einem Insert über den Extension-Point "REX_YFORM_SAVED"
Die zu verschlüsselnden Felder sind über die Auto-Encryption-Tabellenübersicht festzulegen.
Verschlüsselt werden aktuell die yform-Feldtypen: text, textarea und email.

Über das (normale) yform-Backend können verschlüsselte Daten entschlüsselt angezeigt und editiert werden.
Ein komplett entschlüsselter Export ist möglich, sofern die yForm-Tabelle einen Export erlaubt.
Wird die Verschlüsselung bei bestehenden und befüllten Tabellen aktiviert, werden vorhandene Werte verschlüsselt und vice versa.

### Automatisches Löschen
Voraussetzung für ein automatisiertes Löschen ist ein yForm-Datenfeld des Typs 'datestamp'.
Als Format empfiehlt sich hier * <code>mysql</code>, <code>Y-m-d H:i:s</code> bzw. eine leere Format-Angabe.
Rein theoretisch sind auch andere Format-Varianten möglich, nur aktuell nicht getestet.

Im Cryptor/yForm-Backend können diesen datestamp Felder ein Zeitpunkt zugewiesen werden, nachdem der Eintrag automatisiert gelöscht werden soll. Um diesen Vorgang zu triggern ist ein Cronjob Eintrag nötig:
* <code>cryptor_yform::executeAutodelete()</code> um es auf alle definierten Tabellen auszuführen.
* <code>cryptor_yform::executeAutodelete($tableName)</code> um es nur auf eine bestimmte Tabelle auszuführen.