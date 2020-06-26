#WP2Static Erweiterung - Bereitstellen erweiterter Scripte nach dem Deployment#

Erweitert Wp2Static um eine Funktionalität zum Versenden von Formularen aus contact-form-7 heraus.
Wenn das Crawling der Seite abgeschlossen ist, wird dazu die erzeugte Struktur von uns in einen neuen Ordner kopiert
(unser tatsächlicher htdocs Ordner mit den statischen Dateien).
Danach werden weitere Dateien zu diesem neuen Ordner hinzugefügt.

.htaccess -> leitet Anfragen weiter an das Kontaktformular
process_form.php -> verarbeitet eine Anfrage


process_form.php ist so vorbereitet, dass es ein Google Recaptcha v3 verwendet.

Es muss noch konfiguriert werden. In process_form.php muss der Google Recaptcha Code eingefügt werden.
Außerdem müssen in der selben Datei die Datenbankverbindungsdaten hinterlegt werden.
Das Script überprüft in der Wordpress Datenbank, ob die übergebenen Eingabefelder auch wirklich zu dem contact-form-7 Formular gehören.
Beide Schritte sind zur Spam-Prävention eingeführt worden.
Du kannst sie gern entfernen, und überhaupt hiermit machen, was immer du willst.

Das Script liegt unter GPLv2.
Da Wordpress unter GPLv2 liegt, und dies ein abgeleitetes Werk von Wordpress nach dem Wordpress Codec ist,
gehen wir davon aus, dass auch dieses Plugin der GPLv2 unterliegt.

Um das ganze mit Google Recaptcha v3 zum Laufen zu bekommen,
haben wir die konfigurierten Contact-Form Formulare mit Javascript erweitert.

Hier ist ein Beispiel-Code, wie man das bewerkstelligen könnte.
Der Code ist direkt im Kontakt-Formular hinterlegt,also im Menü für das Kontaktformular im Wordpress-Admin.
Der Teil mit REPLACE_WITH_RECAPTCHA_PUBLIC_KEY muss durch den Google Recaptcha-Public-Key ersetzt werden!
Außerdem benötigt das Formular ein hidden-Eingabefeld. Weiterhin hat der Senden-Button die id="mvsend". 

```javascript
<script src="https://www.google.com/recaptcha/api.js?render=REPLACE_WITH_RECAPTCHA_PUBLIC_KEY"></script>
<script>
		var mv_my_submit_button = false;			
		//Generate a token on every page load..
		grecaptcha.ready(function() {
				mv_my_submit_button = document.getElementById("mvsend");  
				mv_my_submit_button.addEventListener("click", mv_send_event_listener);
		});					
		function mv_send_event_listener(e) {
						e.preventDefault();
						grecaptcha.execute('REPLACE_WITH_RECAPTCHA_PUBLIC_KEY', {action: 'submit'}).then(function(token) {
								document.getElementById('mvtok').value = token;																	mv_my_submit_button.removeEventListener('click', mv_send_event_listener);									mv_my_submit_button.click();
		}); 
}</script>
```

## Weitere Hinweise ##
### Workflow ###
1. WP2Static beginnt mit dem Crawlen der Seite
2. Wenn WP2Static fertig ist, verwendet dieses Plugin den Hook wp2static_post_deploy_trigger um eine eigene Funktion zu starten.
3. Diese Funktion benennt das Ziel-Verzeichnis (myhtfiles2) in htdocs um. Es muss also in den Export-Einstellungen von WP2Static als Export-Verzeichnis ein Verzeichnis myhtfiles relativ zum root Ordner von Wordpress hinterlegt werden.

Da WP2Static vom Autor gerade auf eine neue Version mit neuer Infrastruktur umgestellt wird, spare ich mir vorerst die Optimierung in Sachen Konfiguration.
Es sind also auf jeden Fall Programmierkenntnisse nötig, um diese Umsetzung auf einem anderen System zum Laufen zu bekommen.

