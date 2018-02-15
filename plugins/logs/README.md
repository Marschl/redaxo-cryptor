Über das Cryptor/logs-Plugin können IP-Adressen in Server-Log-Files nachträglich anonymisiert werden.
Hintergrund ist die Datenschutz-Grundverordnung (DSVGO) die ab dem 25. Mai 2018 vollumfänglich in Kraft tritt.

* Informationen zur DSVGO: https://dsgvo-gesetz.de/
* Erwägungsgrund 30: "Online-Kennungen zur Profilerstellung und Identifizierung" https://dsgvo-gesetz.de/erwaegungsgruende/nr-30/

Dieses Plugin zielt vorallem auf Serverumgebungen, bei denen sich das Anonymisieren nicht serverseitig steuern lässt.
Schreibzugriff auf den Log-Ordner ist Voraussetzung. 

Bei er Ersteinrichtung macht es Sinn, das Minimalalter so zu setzen, dass erst wenige Logfiles verschleiert werden.
Gerade bei umfangreichen Logfile-Ordner und Logfile-Einträgen, sollte das verschleiern anfangs "stückweise" angegangen werden.
Danach kann der Vorgang über einen Cronjob Eintrag getriggert werden: cryptor_logs::executeIpReplacement()

Aktuell werden nur Gnu-Zip-Archive (.gz) unterstützt.
