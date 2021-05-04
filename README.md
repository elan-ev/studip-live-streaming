# LiveStreaming

Dieses Plugin soll sowohl eine Live-Streaming vom eigenen Schreibtisch, z.B. mit OBS-Studio, also auch ein Live-Streaming aus dem Hörsaal mit Opencast unterstützen. 

Es wird zusätzlich zu dem Plugin noch eine Streamg-Server Infrastruktur benötigt z.B. auf Basis von Nginx-RTMP, Wowza oder SRS. Der Streaming-Server muss in der Lage sein HLS oder DASH an die Zuschauer zu streamen. 

Das Plugin kann auch eingesetzt werden, wenn man Opencast nicht nutzt.

Wenn das Plugin in Stud.IP aktiviert wurde findet man im Menü auf der Plugin-Management-Seite noch den Eintrag "LiveStreaming konfigurieren".

Man kann dort eine Sende- und Empfangs-URL für den Streamingserver hinterlegen. Zum senden wird üblicherweise das RTMP-Protokoll genutzt. Zum Empfangen steht HLS oder MPEG DASH zur Verfügung. Der eingebaute Video.js-Player unterstützt HLS auch auf Browsern die dieses Plotokoll nicht nativ unterstützen (Firefox, Chrome, ...).

Für jede Veranstaltung wird eine zufällig ID zum streamen erzeugt. Je nach Streamingserver muss diese ID an unterschiedlichen Stellen in der URL eingefügt werden. Dies geschieht an der Stelle wo der Platzhalter <id> eingefügt wurde. 
  
Die Opencast Capture Agents streamen von sich aus schon an eine vorgegebener URL. Hier wird an der Stelle <id> die Opencast Capture Agent ID eingefügt, die aus den Opencast Daten für den Raum automatisch entnommen wird. 
  
Derzeit kann nur ein Satz Zugangsdaten angegeben werden. NGinx-RTMP erwartet gar keine Zugangsdaten und ich Wowza haben alle Nutzer das Recht auf allen Anwendungen zu senden, weshalb personalisierte Zugänge derzeit keine hohe Priorität hatten. 
  
Das Plugin kann in einem Kurs im "Mehr..." Menü vom Lehrenden aktiviert werden. 

Der Lehrende muss nur einmalig entscheiden, ob das "Live Streaming von Zuhause" oder "Live Streaming mit Opencast aus dem Hörsaal" durchgeführt werden sollen. 

Wenn sich der Lehrende für Opencast entscheidet, ist nichts weiter zu tun. Passend zur nächsten Übertragung wird der Player auf der Ansicht für die Studierende beim Ablauf des Countdowns aktiviert. 

Wenn mit OBS von Zuhause gestreamt werden soll, werden die Zugangsdaten für den Live-Stream angezeigt. OBS erwartet den letzten Teil der URL als "Streamschlüssel".
