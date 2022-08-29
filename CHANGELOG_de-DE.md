# Unreleased

## Hinzugefügt
* Konfiguration zum Deaktivieren der Administrationszuweisung an Clienteinstellungen hinzugefügt
* OpenID Connect Provider hinzugefügt
* Microsoft Azure OIDC Provider hinzugefügt, welcher anstelle der `thenetworg/azure-oauth2` Bibliothek, den OpenID Connect Provider verwendet
* Google Cloud Provider hinzugefügt
* Keycloak Provider hinzugefügt
* OneLogin Provider hinzugefügt
* Okta Provider hinzugefügt
* Cidaas Provider hinzugefügt
* Hilfe-Link zu `heptacom-admin-open-auth-client-edit-page` hinzugefügt
* Bestätigung mit OAuth Anbieter, wenn nach dem Loginpasswort gefragt wird in `sw-verify-user-modal` hinzugefügt
* Berechtigung für OAuth Administratoren hinzugefügt

## Geändert
* Provider hinzufügen Seite überarbeitet, um eine bessere Übersicht über die verfügbaren Provider zu bekommen
* `\Heptacom\AdminOpenAuth\Service\OpenAuthenticationFlow::getLoginRoutes` geändert, um die Clients nach Namen zu sortieren
* `sw-profile-index`-Erweiterung geändert, damit Änderungen an den eigenen OAuth-Einstellungen auch möglich sind, wenn nur die `user_change_me` Berechtigung erteilt wurde

## Abgekündigt
* Der Microsoft Azure Provider wird in Version 5.0 durch den Microsoft Azure OIDC Provider vollständig ersetzt.

## Entfernt
* Shopware 6.4.0 bis 6.4.10 Unterstützung entfernt

## Behoben
* Fehler mit Shopware 6.4.11 Kompatibilität behoben
* Fehler behoben, der Probleme mit anderen Plugins, die `@Administration/administration/index.html.twig` extenden, verursacht hat
* Fehler behoben, der das Löschen von Benutzern verhindert, die sich über SSO eingeloggt haben.
* Fehler behoben, der die Darstellung von Checkboxen in der Administration auch außerhalb von Bereichen dieses Plugins beeinflusst hat

# 4.0.2

* Fehler mit Shopware 6.4.3 Kompatibilität behoben

# 4.0.1

* Fehler mit Shopware 6.4 Kompatibilität behoben

# 4.0.0

* Kompatibilität für Shopware 6.4 hinzugefügt
* Shopware 6.2 und 6.3 Unterstützung entfernt

# 3.0.3

* Fehler im Microsoft Azure Client behoben, wenn keine `redirectUri` in die Weiterleitungskette übergeben wurde, hat Microsoft die zuletzt angelegte Adresse verwendet. Der Benutzer wurde in eine andere Loginmaske geschickt und nicht eingeloggt

# 3.0.2

* Fehler im Microsoft Azure Client behoben, wenn Benutzer der Active Directory ohne Outlook Abonnement sich einloggen wollen
* API Verwendung verbessert bei Benutzerverknüpfung um zuverlässig HTTP Authentication Header zu schicken
* Darstellung zum Verknüpfen von Benutzern zeigt nun Namen anstelle des Clienttypen an
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
