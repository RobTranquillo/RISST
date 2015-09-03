# RISST
Scraping tools für Ratsinformationssysteme (RIS)

derzeit kann das Dresdner Ratsinformationssystem (RIS) der Firma Somacos (Session) damit ausgelesen werden. Die Daten landen entweder als CSV oder Text auf der Platte aber die Funktionen geben sie auch als einfache Arrays zurück damit die Scripte leicht in anderen Projekten weiterverwendet werden können. 

# Funktionen
## ratsinfo_connect.php
Baut die Verbindung zum Ratsinfosystem auf (OOP) 
Anleitung: siehe Dateikopf

## structure_commitees.php
Zieht alle Gremien des Stadtrates und gibt sie in einem Array zurück

## structure_members.php
Zieht alle Mitglieder des Stadtrates und gibt sie in einem Array zurück

## structure_sessions.php
Erzeugt ein MultiArray aus welcher Sitzung welcher Gremien welche Dokumente erzeugt hat, mit link-ID und Name.
Und speichert sie als ris_sitzungenslinks.csv ab.
Anleitung: siehe Dateikopf

# last words
All scripts are made by me, Rob Tranquillo, please contact me with questions or file issues here at github. 
Or get in touch with me via twitter @robtranquillo or join our weekly opendata meetups in Dresden,
Look at http://offenesdresden.de

#Licence
public domain with BY (Attribution)
