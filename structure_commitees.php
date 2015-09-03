<?php
/*
 * RIS Sessions Reader by tranquillo
 *
 * Das Programm ließt den Sitzungskalender der Stadt
 * Dresden (Somacos Session RIS) ein.
 * Und gibt daraus alle Gremien als Array zurück.
 *
 * robtranquillo@gmx.de
 * twitter.com/robtranquillo
 * github.com/robtranquillo
 *
 * Version 1.0 / 03.09.2015
 *
 * Licence: public domain with BY (Attribution)
 */
 
header('Cache-Control: no-cache, must-revalidate');
header('Content-type: text/plain');

error_reporting(-1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// link zu den Gremien
$url = 'http://ratsinfo.dresden.de/gr0040.php';
$commitees = get_commitees($url);

print_r($commitees);



/*
 * Gremien des Stadtrats ermitteln
 */

function get_commitees($url)
{
    $html = get_html($url);
    if($html === false) echo 'connection error';
    else {
        $oldSetting = libxml_use_internal_errors( true );
        libxml_clear_errors();
        $dom = new DOMDocument();
        $dom->loadHtml( $html );
        $tbody = $dom->getElementsByTagName('tbody');
        $trs = $tbody[0]->getElementsByTagName('tr');
        $commitees = array();
        foreach( $trs as $tr)
        {
            $tds = $tr->getElementsByTagName('td');
            $link = $tds[0]->getElementsByTagName('a');
            if($link->length > 0)
            {
                $commitee = array(
                    'name' => $link[0]->nodeValue,
                    'link' => $link[0]->getAttribute('href')
                    );
            }
            $commitees[] = $commitee;
        }

        libxml_clear_errors();
        libxml_use_internal_errors( $oldSetting );

        return $commitees;
    }
    return false;
}


/*
 * Funktion um Seiten aus dem Ratsinfo zu ziehen
 */

function get_html($url)
{
    $ratsinfo_connector = false;
    include('ratsinfo_connect.php');
    $ratsinfo_connector = new Ratsinfo();

    if( $ratsinfo_connector === false ) return false;
    else {
        return $ratsinfo_connector->get_url($url);
    }
}



?>
