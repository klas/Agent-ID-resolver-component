## Installation
* Klone das Repo
* Herstellerabhängigkeiten installieren: `docker run --rm --interactive --tty --volume $PWD:/app composer install --ignore-platform-reqs --no-scripts`
* Bei fehlenden Klassen: `docker run --rm --interactive --tty --volume $PWD:/app composer dump-autoload`
* env-Datei kopieren: `cp .env.example .env`
* Container starten: `vendor/bin/sail up -d`
* RMigrationen und Seeders ausführen: `vendor/bin/sail artisan migrate:fresh --seed`
* API veröffentlichen: `vendor/bin/sail artisan install:api`

## API
Um eine korrekt formatierte JSON-Antwort einschließlich Fehlermeldungen zu erhalten, ist der Request-Header `Accept: application/json` zu senden.

##### Makler API
* Show: GET `/api/makler?vnr={VNR}&geselschaft={GESELSCHAFT NAME}`
* Beilspiel Antwort:
`  {
  "name": "Max Mustermann"
  }`

## Testen
* Tests ausführen `vendor/bin/sail artisan test`


============================================
## Struktur
* Die Umwandlung wird durch die Umwandlungsstrategie gewährleistet,
* VnrStepFilteringResolvingStrategy basiert auf dem Filter Builder, bei dem Filterdefinitionen (die für jede Gesellschaft definiert werden, da sie unterschiedliche Formate verwenden) die verfügbaren Filter kombinieren, um normalisierte VNR zu erhalten.
## Beschränkungen
* Nur VnrStepFilteringResolvingStrategy ist implementiert
* Es gibt keine Berechtigungsprüfung

============================================
### Einleitung
Folgend findest du wie besprochen eine kleine Aufgabe, die eine unserer realen Herausforderungen zeigt.

Bei der Umsetzung haben wir dir bewusst alles offen gehalten. Damit es für dich nicht ausartet solltest du nicht mehr als 2-4h dafür investieren.
Bei der Aufgabe geht es primär darum, dass wir zusammen ins Review gehen können, damit wir uns noch besser kennenlernen.

Solltest du noch Fragen haben dann melde dich bitte gerne!

### Vermittlernummern (VNR)
Die Gesellschaften vergeben eindeutige Nummern an die Makler, um Verträge und andere
Korrespondenzen zuordnen zu können.

Die Vermittlernummer ist innerhalb einer Gesellschaft immer eindeutig.

In sämtlichen Importen in unser System (Verträge und Kunden) wird die VNR genutzt, um
den richtigen Nutzer (Makler) zu ermitteln.

Das Problem ist, dass die Nummer über verschiedene Quellen in unterschiedlichen
Formaten angegeben wird, also mehrere Formate zulässig sind.

Die folgenden Nummern sind hierbei identisch:
- 006674BA23
- 6674BA23
- 6674-BA23

Die Formate sind dabei von Gesellschaft zu Gesellschaft unterschiedlich (siehe Beispiele).

### Aufgabe:
Benötigt wird eine Komponente, die eine der Nummern und die zugehörige Gesellschaft
angegeben bekommt und daraufhin den richtigen Eintrag in der Datenbank ermittelt. Der
Eintrag in der Datenbank **kann in jedem dieser Formate** sein. Dh. Eine Normalform in
der Datenbank nehmen wir für diese Aufgabe als nicht praktikabel an. Der Hintergrund ist
dabei zum Einen, dass zum Zeitpunkt der Anlage der Vermittlernummer keine bzw. nicht
alle Formate bekannt sind und zum Zweiten, dass die Aufgabe dann einfach interessanter
ist :)

Zum Zeitpunkt des Imports ist uns die Gesellschaft und die importierte Vermittlernummer
bekannt.

Die Komponente liefert uns daraufhin, wenn vorhanden, den User-Eintrag aus der
Datenbank.

Die Datenbank sieht grob so aus:
- Wir haben Gesellschaften, Makler und Vermittlernummern.
- Ein Makler hat mehrere Vermittlernummern.
- Ein Makler kann auch zu einer Gesellschaft verschiedene Nummern haben.

Gesellschaften haben jeweils Vermittlernummern zu mehreren Maklern.

Ein paar Beispiele zu den Formaten (ähnliche Fälle in der Realität).

**Haftpflichtkasse Darmstadt:**
- 00654564
- 654564
- 654-564

**WWK**
- Q412548787
- 412548787

**Axa Versicherung**
- 15154184714-000
- 15154184714
- 99/15154184714

**Ideal Versicherung:**
- 006674BA23
- 6674BA23
- 6674-BA23
(Die Buchstaben sind Teil der Nummer)

**die Bayerische**
- 54501R784
- 54501-R784
- 54501784


Als Zusatz würden wir gerne, dass die Aufgabe mit Hilfe eine Frameworks erledigt wird.
Hierbei kannst du dich direkt mit Composer und Migrations beschäftigen.
Welches von den aktuellen Frameworks du nutzt ist dabei dir überlassen.

### Beispielszenario
Wir haben in der Datenbank zu dem Makler Max Mustermann und der Gesellschaft Ideal
die Vermittlernummer „006674BA23“ hinterlegt.
Nun wird ein Vertrag importiert (nur als Beispiel) bei dem die Vermittlernummer „6674-
BA23“ eingetragen ist.
Die Komponente soll dann also auf die Frage, welchem Makler die Vermittlernummer
„6674-BA23“ bei der Ideal-Versicherung gehört mit dem Eintrag (Max, Mustermann)
antworten.

Analog dazu dann die anderen Beispiele zu den Formaten der Gesellschaften
