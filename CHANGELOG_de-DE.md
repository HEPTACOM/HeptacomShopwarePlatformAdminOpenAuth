* Fehler behoben, dass nicht korrekt bereinigte Installationen mit Einstellung `redirectUri` nicht weiterarbeiten können

# 3.0.1

* Fehler behoben, dass die extrahierte Ressourcen nicht geladen werden können bei Plugininstallation

# 3.0.0

* Doppelten Eintrag in der Einstellungsübersicht in der Administration entfernt
* Fehler bei ZIP-Installation behoben, der externe Komponenten nicht korrekt geladen hat
* Rücksende URL wird automatisch generiert um Domain-Umzüge zu vereinfachen
* OpenAuth Codeverträge in eigenes Repository ausgelagert heptacom/open-auth
* Methode zum ClientContract hinzugefügt zum Autorisieren von API Anfragen 
* ClientFactoryContract aus ClientLoader extrahiert
* ClientProviderRepositoryInterface in den Heptacom\OpenAuth\ClientProvider\Contract Namensraum als Contract überführt
* ClientProviderInterface in den Heptacom\OpenAuth\ClientProvider\Contract Namensraum als Contract überführt
* ClientInterface in den Heptacom\OpenAuth\Client\Contract Namensraum als Contract überführt
* RedirectBehaviour-Klasse zur Steuerung von Weiterleitungsprozessen hinzugefügt
* TokenPairFactory in den Heptacom\OpenAuth\Token\Contract Namensraum als Contract überführt
* TokenPairStruct in den Heptacom\OpenAuth\Struct Namensraum überführt und Shopware-Abhängigkeit entfernt
* UserStruct in den Heptacom\OpenAuth\Struct Namensraum überführt und Shopware-Abhängigkeit entfernt

# 2.0.0

* Namen für korrekte Administrationsanpassung geändert
* Make als Werkzeug für Automatisierte Vorgänge eingerichtet
* Lizensierungsmodell von MIT zu Apache 2.0 geändert
* Technischer Name vom Plugin angepasst um Community Store Regeln zu folgen
* Pluginkompatibilität zu einer gravierenden Änderung in 6.2.3 behoben (Issue NEXT-9240)
* Loginknöpfe sehen identisch zu anderen Knöpfen im Loginbereich aus, wenn diese fokussiert werden

# 1.0.2

* Fehlenden Menüeintrag in den Administrationseinstellungen hinzugefügt
* Fehler bei Schlüsselerneuerung behoben, wenn der Erneuerungsschlüssel leer ist

# 1.0.1

* Fehler behoben, dass das Merken von Datenbankänderungen bei Neuinstallation rückgängig gemacht wird
* Fehler beim Einloggen behoben

# 1.0.0

* Schalter für Klienten hinzugefügt um diese für die Loginmaske und Verknüpfungen einzeln zu aktivieren
* Das Verknüpfen von angemeldeten Benutzern mit einem Dienst im Benutzerprofil hinzugefügt
* Konfigurationsoberfläche in der Administration hinzugefügt
* Das Entfernen von Verknüpfungen im Benutzerprofil hinzugefügt
* Feld für eigene Klientberechtigungen hinzugefügt
* Microsoft Azure als Anbieter hinzugefügt
* Atlassian als Anbieter hinzugefügt
* Schlüsselspeicher für API Werkzeuge hinzugefügt
* API Werkzeuge zum Add authorized http client to easily access remote APIs
