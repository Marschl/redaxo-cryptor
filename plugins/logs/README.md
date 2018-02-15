Über das Cryptor/logs-Plugin können IP-Adressen in Server-Log-Files nachträglich anonymisiert werden.
Hintergrund ist die Datenschutz-Grundverordnung (DSVGO) die ab dem 25. Mai 2018 vollumfänglich in Kraft tritt.

* Informationen zur DSVGO: https://dsgvo-gesetz.de/
* Erwägungsgrund 30: "Online-Kennungen zur Profilerstellung und Identifizierung" https://dsgvo-gesetz.de/erwaegungsgruende/nr-30/

Dieses Plugin zielt vorallem auf Serverumgebungen, bei denen sich das Anonymisieren nicht serverseitig steuern lässt.
Schreibzugriff auf den Log-Ordner ist Voraussetzung. 

Bei der Ersteinrichtung macht es Sinn das Minimalalter so zu setzen, dass erst wenige Logfiles verschleiert werden.
Gerade bei umfangreichen Logfile-Ordner und Logfile-Einträgen, sollte das Verschleiern anfangs "stückweise" angegangen werden.
Danach kann der Vorgang über einen Cronjob Eintrag getriggert werden: <code>cryptor_logs::executeIpReplacement()</code>

Aktuell werden nur Gnu-Zip-Archive (.gz) unterstützt.
