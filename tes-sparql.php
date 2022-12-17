<?php

require_once realpath(__DIR__.'/..'). "/vendor/autoload.php";
require_once __DIR__."/html_tag_helpers.php";

?>
<?
//inisialisasi namespace untuk query rdf
\EasyRdf\RdfNamespace::set('rdf','http://www.w3.org/1999/02/22-rdf-syntax-ns#');
\EasyRdf\RdfNamespace::set('foaf','http://xmlns.com/foaf/0.1/');
\EasyRdf\RdfNamespace::set('rdfs','http://www.w3.org/2000/01/rdf-schema#');
\EasyRdf\RdfNamespace::set('owl','http://www.w3.org/2002/07/owl#');
\EasyRdf\RdfNamespace::set('dbp','http://dbpedia.org/property/');
\EasyRdf\RdfNamespace::set('dbo','http://dbpedia.org/ontology/');
\EasyRdf\RdfNamespace::set('xsd','http://www.w3.org/2001/XMLSchema#');

//set sparql endpoint
$sparql_endpoint = 'https://dbpedia.org/sparql';
$sparql = new \EasyRdf\Sparql\Client($sparql_endpoint);

$sparql_query = '
    SELECT distinct * WHERE {
        <' . $tulus_uri . '> dbo:birthPlace ?tempat_lahir ; 
            rdfs:comment ?info ; 
            foaf:isPrimaryTopicOf ?wiki .
        ?tempat_lahir rdfs:label ? tempat_lahir_label ;
            geo:lat ;
            geo:long ?long .
        ?album dbp:artist <' . $tulus_uri . '> ;
            rdfs:label ?album_label .
        OPTIONAL {album dbp:released ?rilis album.}
        FILTER (lang(?info) = "en" && lang(?tempat_lahir_label) = "en" && lang(?album_label) = "en")
    }
    ORDER BY DESC (?rilis_album)
    ';

    $result = $sparql->query($sparql_query);

    //ambil detail tulus dari $result sparql
    $detail = [];
    foreach ($result as $row) {
        $detail = [
            'tempat_lahir'=>$row->tempat_lahir_label,
            'instrumen'=>$row->instrumen,
            'info'=>$row->long,
            'wiki'=>$row->wiki,
        ];
        break;
    }
    
?>