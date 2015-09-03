<?php
/*
 * RIS Sessions Reader by tranquillo
 *
 * Das Programm ließt den Sitzungskalender der Stadt
 * Dresden (Somacos Session RIS) ein.
 * Es extrahiert in ein MultiArray welche Sitzung, welchen
 * Gremiums welche Dokumente erzeugt hat, mit link-ID und Name.
 *
 * Dieses Array wird auch zeilenweise in eine
 * CSV Datei geschrieben.
 *
 *
 * robtranquillo@gmx.de
 * twitter.com/robtranquillo
 * github.com/robtranquillo
 *
 * Version 1.0 / 03.09.2015
 *
 * Licence: public domain with BY (Attribution)
 *
 * dependencies:
 * - ratsinfo_connect.php
 *
 * Start on console:
 * ~: php structure_session.php
 * or open in webbrowser (beware of the timeout)
 *
 * change $year / $month for individual timspans
 * $outfile for the export path and in
 * get_ris_html() the $url for another download link
 *
 */



#error_reporting(E_ALL & ~E_NOTICE);
#ini_set('display_errors', 1);
#ini_set('display_startup_errors', 1);
$outfile = 'ris_sitzungenslinks.csv';

echo "\n RIS Sitzungen werden eingelesen in $outfile ..";

$ratsinfo_connector = false;
$sessions = array();
$years = array(2009, 2010, 2011, 2012, 2013, 2014, 2015);
foreach($years as $year)
{
    for($month=1; $month<=12; $month++)
    {
        $sessions = array_merge($sessions, get_ris_sessions( $year, $month));
        echo "\n $year $month: ".count($sessions);
    }
}

persist_to_text($sessions, $outfile);

/*
 *
 */
function persist_to_text( $sessionsArr , $filepath)
{
    $headline = '"Datum", "Gremium", "link id", "link name"';
    $str = '';
    foreach($sessionsArr as $session)
    {
        $line = "\n\"$session[datum]\", \"$session[Sitzung]\"";
        foreach($session['links'] as $links)
        {
            $str .= $line .", \"$links[id]\", \"$links[name]\"";
        }
    }
    file_put_contents($filepath, $headline . $str , FILE_APPEND);
}



/*
 * Sitzungen des Stadtrats ermitteln
 */

function get_ris_sessions($year, $month)
{
    $html = get_ris_html($year, $month);
    if($html === false) echo 'connection error';
    else {
        $oldSetting = libxml_use_internal_errors( true );
        libxml_clear_errors();
        $dom = new DOMDocument();
        $dom->loadHtml( $html );
        $tbody = $dom->getElementsByTagName('tbody');
        $trs = $tbody[0]->getElementsByTagName('tr');
        $sessions = array();
        $lastdate = 0;
        foreach( $trs as $tr)
        {
            $tds = $tr->getElementsByTagName('td');

            // Datum ermitteln
            // in leeren Zeilen zählt das Datum das weiter oben in dieser Spalte schon angezeigt wurde
            $day = intval(trim($tds[3]->textContent));
            if( $day > 0 )
                $lastdate = sprintf("%'.02d", $day);

            // Dokumenten links ermitteln
            $links = get_links_from_td( $tds[8] );
            if( count($links) > 0)
            {
                $sessions[] = array('datum'=> $lastdate.'.'.$month.'.'.$year,
                                    'Sitzung'=> $tds[5]->nodeValue,
                                    'links'=>$links
                                    );
            }
        }

        libxml_clear_errors();
        libxml_use_internal_errors( $oldSetting );

        if( count($sessions)>0 ) return $sessions;
        else return array();
    }
    return false;
}



/*
 * Gibt alle in der Zelle enthaltenen links zurück in einem Array, andernfalls false
 */
function get_links_from_td( $td ) {
    if($td != Null) {
                if( $td->nodeValue )
                {
                    $links = $td->getElementsByTagName('a');
                    if($links->length > 0)
                    {
                        foreach($links as $link)
                        {
                            $name = $link->nodeValue;
                            $linktext = $link->getAttribute('href');

                            if($name && $linktext)
                            {
                                $href = array(
                                    'name' => trim($name),
                                    'link' => $linktext,
                                    'id'   => substr($linktext, 15, -8)
                                    );
                                $hrefs[] = $href;
                            }
                        }
                    }
                }
            }
    if(count($hrefs)>0) return $hrefs;
    else array();
}



/*
 * Funktion um Seiten aus dem Ratsinfo zu ziehen
 */

function get_ris_html($year, $month)
{
    // link zur Sitzungsübersicht
    $url = 'http://ratsinfo.dresden.de/si0040.php?__cjahr='.$year.'&__cmonat='.$month.'&__canz=1&__cselect=0';

    $ratsinfo_connector;
    include_once('ratsinfo_connect.php');
    $ratsinfo_connector = new Ratsinfo();

    if( $ratsinfo_connector === false ) return false;
    else {
        return $ratsinfo_connector->get_url($url);
    }
}

?>


