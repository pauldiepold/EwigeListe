@extends('layouts.main')

@section('title', 'Regeln')

@section('heading', 'Doppelkopf Regeln')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-xl-8 col-lg-9 col-md-10">
            <div class="text-left" id="regeln">
                <h3>Spielfindung</h3>
                <p>
                    Vor dem Spiel und vor Abhandlung der Vorbehalte müssen diese angekündigt werden. Sollte ein Spieler
                    einen Vorbehalt haben, hat er die Verantwortung diesen rechtzeitig zu melden. Dabei ist die
                    Reihenfolge
                    der Spieler irrelevant. Es ist sowohl möglich, einen Vorbehalt nicht anzukündigen (wenn klar ist,
                    dass
                    dieser nicht gespielt wird, weil z.B. mindestens zwei Personen einen Vorbehalt haben oder man selber
                    eine
                    Hochzeit hat), als auch einen Vorbehalt anzukündigen, ohne die entsprechenden Karten und Absicht zu
                    haben.
                </p>
                <p>
                    Anschließend werden folgende Vorbehalte jeweils in Sitzreihenfolge der Spieler abgefragt:
                </p>
                <ul>
                    <li>Soli (vom Geber im Uhrzeigersinn)</li>
                    <li>Schmeißen ist möglich, falls sich Folgendes auf einer Hand befindet:
                        <ul>
                            <li>5 Neunen</li>
                            <li>4 Neunen verschiedener Farbe (je Farbe eine)</li>
                            <li>Kein Trumpf höher als das Karo-Ass</li>
                        </ul>
                    </li>
                    <li>Armut: Maximal 3 Trumpf, drei Karten (alle Trumpfkarten) werden abgegeben, Mitspieler dürfen
                        über Mitnahme im Uhrzeigersinn entscheiden (Armut + Mitspieler sind Re-Partei), es kann dabei
                        ein Schweinchen entstehen
                    </li>
                    <li>Hochzeit: Beide Kreuzdamen auf einer Hand, erster fremder Stich innerhalb der ersten drei Stiche
                        entscheidet Mitspieler (Hochzeit + Mitspieler sind Re-Partei)
                    </li>
                </ul>
                <h3>Soli</h3>
                Folgende Soli sind möglich:
                <ul>
                    <li>Farbsolo (gewünschte Farbe ersetzt Karo als Trumpf)</li>
                    <li>Damen, Buben oder Königsolo (nur die jeweiligen Bilder sind Trumpf)</li>
                    <li>Fleischloser / Fehlsolo (kein Trumpf)</li>
                    <li>Verdeckte Hochzeit (beide Kreuzdamen auf einer Hand ohne Ankündigung)</li>
                </ul>
                <h3>Ansagen</h3>
                Folgende Ansagen sind möglich:
                <ul>
                    <li>Re bzw. Kontra mit min. 11 Karten auf der Hand</li>
                    <li>Keine 90 mit min. 10 Karten (Auf ein angesagtes Re/Kontra auch ein gegnerisches Kontra/Re)</li>
                    <li>Keine 60 mit min. 9 Karten</li>
                    <li>Keine 30 mit min. 8 Karten</li>
                    <li>Schwarz mit min. 7 Karten</li>
                </ul>
                <p>
                    Ansagen dürfen nur vor Beginn des Spiels getätigt werden, oder aber wenn der Spieler selbst am Zug
                    ist.
                    Die Ansage muss getätigt werden, bevor der Spieler seine eigene Karte legt. Ist die entsprechende
                    Karte
                    bereits gelegt, ist eine Ansage ungültig, unabhängig davon, ob der dahinter sitzende Spieler bereits
                    gespielt hat oder nicht.
                </p>
                <p>
                    Ansagen sind außerdem nur möglich, wenn alle niedrigeren Ansagen bereits getätigt wurden. Bei
                    Hochzeiten
                    wird ein Kontra zu einem Re, wenn der betreffende Spieler mit der Hochzeit spielt.
                </p>
                <p>
                    Sollte im dritten Stich, nachdem eine der beiden Partien sowohl Re/Kontra als auch "keine 90"
                    angesagt
                    hat, eine gegnerische Ansage Kontra/Re erfolgen, bezieht sich diese trotzdem auf ein Gewinnen des
                    Spiels
                    (mind. 120/121 Punkte) und nicht auf das "keine 90".
                </p>
                <h3>Wertung</h3>
                Folgende Ereignisse erhöhen den Spielwert:
                <ul>
                    <li>Gewonnen (bei 120 gewinnt die Kontrapartei): 1 Punkt</li>
                    <li>Keine 90, keine 60, keine 30 und Schwarz: je 1 Punkt</li>
                    <li>Angesagte Keine 90, Keine 60, Keine 30 und Schwarz : je 1 Punkt</li>
                    <li>Re bzw. Kontra: je 2 Punkte</li>
                </ul>
                Die Gewinner bekommen den Spielwert gutgeschrieben, die Verlierer abgezogen. Unabhängig davon werden
                folgende Sonderpunkte (und negative Punkte an die Gegenpartei) vergeben:
                <ul>
                    <li>gewonnen gegen die Re-Partei</li>
                    <li>gefangener Fuchs (Karo-Ass)</li>
                    <li>letzter Stich mit Karlchen Müller (Kreuz-Bube) gemacht</li>
                    <li>gefangener Karlchen Müller (Kreuz-Bube) im letzten Stich</li>
                    <li>Doppelkopf (40 oder mehr Punkte in einem Stich)</li>
                </ul>
                <h3>Sonderregeln</h3>
                <ul>
                    <li><b>Herz-Zehn:</b> Die Zweite sticht die Erste, außer im letzten Stich.</li>
                    <li><b>Superschwein:</b> Befinden sich beide Karo-Asse auf einer Hand und wird mit dem Ersten ein
                        Stich
                        gemacht (vom Mitspieler mitgenommen reicht nicht), so ist das zweite das Superschwein (mit
                        Ankündigung) und damit der höchste Trumpf.
                    </li>
                    <li><b>Soli:</b> Sonderpunkte werden nicht angerechnet; der Solo-Spieler bekommt den Spielwert
                        dreifach
                        angerechnet (sowohl positiv als auch negativ). Ausnahme: Verdeckte Hochzeit, hier gelten alle
                        regulären Sonderpunkte.
                    </li>
                    <li><b>Falsch bekannt:</b> Der Spieler wird mit mindestens 6 Minuspunkten bestraft und bereits
                        getätigte
                        Ansagen werden extra angerechnet.  Außerdem muss der Spieler seinen Mitspielern einen Kuchen
                        backen. Beim Eingeben des Spiels in die Liste muss das falsche Bekennen angegeben werden.
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection