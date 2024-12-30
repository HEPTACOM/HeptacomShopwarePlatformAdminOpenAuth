# Unreleased

# 7.0.1

**Hinzugefügt**

* Authentifizierter OData Request Bedingung für OpenID Connect basierte Provider hinzugefügt

**Behoben**

* Falsch negative Validierungen in der Gruppen IDs Bedingung im Microsoft Entra ID Provider behoben (GitHub Issue #27) 
* Fehler behoben, der das Konfigurieren von Scopes bei OAuth Clients verhinderte. (GitHub Issue #33)
* Unvollständige Implementierung in `\Heptacom\AdminOpenAuth\Exception\UserMismatchException` behoben (GitHub Issue #33)

# 7.0.0

**Hinzugefügt**

* Kompatibilität mit Shopware 6.6 und zugehörigen Abhängigkeiten hinzugefügt

**Geändert**

* Microsoft Azure in Microsoft Entra ID umbenannt (nur Übersetzungen; siehe Abkündigungen)

**Entfernt**

* Shopware 6.5 Kompatibilität wurde entfernt

**Abgekündigt**

* Der Microsoft Azure Anbieter wird in Version 8.0.0 technisch in Microsoft Entra ID umbenannt

# 6.0.3

**Behoben**

* Kompatibilitätsprobleme mit Shopware 6.5.8.8 behoben. Siehe [Johannes's Beitrag auf GitHub](https://github.com/HEPTACOM/HeptacomShopwarePlatformAdminOpenAuth/pull/23)

# 6.0.2

**Behoben**

* Schreibfehler in deutschen Textbausteinen `heptacomAdminOpenAuthClient.providerFields.microsoft_azure_oidc.clientSecret` und `heptacomAdminOpenAuthClient.providerFields.jira.clientSecret` behoben. Siehe [Niklas Wolf's Beitrag auf GitHub](https://github.com/HEPTACOM/HeptacomShopwarePlatformAdminOpenAuth/pull/21)
* Eigenes URL Feld für Konfigurationen genutzt um automatische Änderungen vom Shopware URL Feld (GitHub Issue #20) zu verhindern um Benutzung von [goauthentik.io](https://goauthentik.io) zu ermöglichen

# 6.0.1

**Entfernt**

* Abhängigkeiten von Symfony in der Plugin „composer.json“ entfernt, da diese bereits im Shopware-Kern enthalten sind.

# 6.0.0

**Hinzugefügt**

* Dynamische Rollenzuweisung mithilfe konfigurierbarer Regeln in Clients
* Rollen/Gruppen zu Attributszuordnung im SAML2 und JumpCloud Provider hinzugefügt
* Authentifizierter Request Bedingung für OpenID Connect basierte Provider hinzugefügt
* Gruppen IDs Bedingung zum Microsoft Azure OIDC Provider hinzugefügt
* `User.Read` Scope zum Microsoft Azure OIDC Provider hinzugefügt. Dieser ist für die Gruppen IDs Bedingung erforderlich.

**Behoben**

* Fehler in Migration `Migration1685517455SetExpiredAndTypeToRequiredFields` behoben der bei MariaDB Installationen für Probleme sorgte
* Fehler behoben, der Aktualisieren von Rollenzuweisungen verhinderte, obwohl `keepUserUpdated` aktiviert war

**Entfernt**

* Statische Rollenzuweisung für Clients entfernt. Die statische Zuweisung wird automatisch zu einer Regel für die dynamische Zuweisung migriert.
* Die Konfigurationsoption `redirectUri`, die seit [v3.0.2](#302) abgekündigt ist und für die Löschung in [v5.0.0](#500) geplant war, wurde entfernt.

# 5.0.0

**Hinzugefügt**

* Kompatibilität mit Shopware 6.5 und zugehörigen Abhängigkeiten hinzugefügt
* Schaltfläche zum Herunterladen von Metadaten zur Komponente `heptacom-admin-open-auth-client-edit-page` hinzugefügt
* Konfigurationsoption `requestedAuthnContext` zum SAML2-Anbieter hinzugefügt
* Plugin-Konfigurationsoption `denyPasswordLogin` hinzugefügt, um Passwort-Login zu deaktivieren #14
* Popup-Block-Check für Benutzer-Bestätigungs-Modale hinzugefügt, der einen nahtloseren Bestätigungsfluss ermöglicht

**Geändert**

* Anbieter-Einstellungen wurden geändert, um eine eigene Komponente zu haben, anstatt `heptacom-admin-open-auth-client-edit-page` zu überschreiben
* `view/administration/index-js.html.twig` wurde so geändert, dass nur noch auf relevante Routenübereinstimmungen geprüft wird
* Änderung von `view/administration/index-js.html.twig`, um Login in `sw-inactivity-login` zu ermöglichen
* Login-Payload wurde geändert, um die angeforderte Redirect-URL zu speichern
* Das Laden von benutzerdefinierten Admin Vue-Komponenten wurde auf asynchrones Laden umgestellt
* SAML-Provider wurde geändert, um den angeforderten Authn-Kontext standardmäßig zu deaktivieren

**Entfernt**

* Shopware 6.4 Kompatibilität wurde entfernt
* Die Abhängigkeit `heptacom/open-auth` wurde entfernt und die Komponenten mit dem Plugin ausgeliefert
* Microsoft Azure (nicht OIDC) und die erforderliche Abhängigkeit `thenetworg/oauth2-azure` entfernt
* `Heptacom\AdminOpenAuth\Contract\TokenRefresherInterface` und Implementierung `Heptacom\AdminOpenAuth\Service\TokenRefresher` wurden entfernt

**Sicherheit**

* Kontobestätigungen können nicht mehr von anderen zulässigen Personen des selben Identitätsanbieters genutzt werden und somit Folgeaktionen auslösen

# 4.3.0-beta.2

**Behoben**

* `administration.heptacom.admin_open_auth.confirm` Route gefixt, indem ein Standardwert für `expiresAt` gesetzt wurde

# 4.3.0-beta.1

**Hinzugefügt**

* `type` zu Authentifizierungsstatus hinzugefügt um Verhaltensänderungen je OAuth Ablauf zu ermöglichen
* `expiresAt` zu Authentifizierungsstatus hinzugefügt um ungenutzte und bereits abgelaufene `authorization_code` zu entfernen
* `LoginsCleanupTask` als geplante Aufgabe hinzugefügt um unnutzbare Authentifizierungsstatus zu entfernen

**Behoben**

* Nutzung von typisierten Daten mit DBAL bei der Aktualisierung von Nicht-Administratorkonten korrigiert. Siehe [AndreasA's Beitrag auf GitHub](https://github.com/HEPTACOM/HeptacomShopwarePlatformAdminOpenAuth/pull/8)

# 4.2.1

* Fehler behoben, der das Zuweisen von Rollen verhinderte

# 4.2.0

* Konfiguration zum automatischen Zuweisen von Benutzerrollen für neue Benutzer, die nicht Administratoren sind, hinzugefügt.
* Konfiguration zum automatischen Aktualisieren von Benutzerdaten, wenn sich ein Nutzer via SSO anmeldet.
* `views/administration/heptacom-admin-open-auth/page/confirm.html.twig` geändert, damit das Popup direkt nach Speicherung der Daten im LocaleStorage geschlossen wird.
* `Heptacom\AdminOpenAuth\Service\UserResolver` und `Heptacom\AdminOpenAuth\Component\Provider\OpenIdConnectClient::getUser` geändert, damit dem Nutzer weitere Daten (z.B. Sprache and Zeitzone) hinzugefügt werden können.
* Durch Hinzufügen von Standard-Werten in `Heptacom\AdminOpenAuth\Database\ClientDefinition`, Probleme in `Heptacom\AdminOpenAuth\Controller\AdministrationController::createClient` behoben.
* Versionsmuster für Composer Abhängigkeit "thenetworg/oauth2-azure" von "^1.4" auf "^1.4 | ^2.0" geändert um Projekte mit PHP 8.0 als Minimumversion zu unterstützen (Danke an Hans Höchtl @hhoechtl)
* SAML2 Provider hinzugefügt
* JumpCloud Provider hinzugefügt

# 4.1.0

**Hinzugefügt**

* Konfiguration zum Deaktivieren der Administrationszuweisung an Clienteinstellungen hinzugefügt
* OpenID Connect Provider hinzugefügt
* Microsoft Azure OIDC Provider hinzugefügt, welcher anstelle der `thenetworg/azure-oauth2` Bibliothek, den OpenID Connect Provider verwendet
* Google Cloud Provider hinzugefügt
* Keycloak Provider hinzugefügt
* OneLogin Provider hinzugefügt
* Okta Provider hinzugefügt
* Cidaas Provider hinzugefügt
* Hilfe-Link zu `heptacom-admin-open-auth-client-edit-page` hinzugefügt
* Vue Benutzerverifikationskomponente `heptacom-admin-open-auth-user-confirm-login` hinzugefügt zum einfachen Bauen eigener Bestätigungsdialoge
* Bestätigung mit OAuth Anbieter, wenn nach dem Loginpasswort gefragt wird in `sw-verify-user-modal` (generische Bestätigungskomponente) und in `sw-profile-index` (eigenes Benutzerprofil) hinzugefügt, jedoch nicht in `sw-users-permissions-user-listing` (Administrationsbenutzerliste), da ein Eingriff in die Löschbestätigung nicht sicher zu ersetzen ist
* Berechtigung für OAuth Administratoren hinzugefügt

**Geändert**

* Provider hinzufügen Seite überarbeitet, um eine bessere Übersicht über die verfügbaren Provider zu bekommen
* `\Heptacom\AdminOpenAuth\Service\OpenAuthenticationFlow::getLoginRoutes` geändert, um die Clients nach Namen zu sortieren
* `sw-profile-index`-Erweiterung geändert, damit Änderungen an den eigenen OAuth-Einstellungen auch möglich sind, wenn nur die `user_change_me` Berechtigung erteilt wurde

**Abgekündigt**

* Der Microsoft Azure Provider wird in Version 5.0 durch den Microsoft Azure OIDC Provider vollständig ersetzt.

**Entfernt**

* Shopware 6.4.0 bis 6.4.10 Unterstützung entfernt

**Behoben**

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
