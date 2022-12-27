<?php
	require 'vendor/autoload.php';
	require_once __DIR__."/html_tag_helpers.php";
	$sparql_endpoint = 'https://dbpedia.org/sparql';

	$sparql_dbpedia = new \EasyRdf\Sparql\Client($sparql_endpoint);
	$sparql_jena = new \EasyRdf\Sparql\Client('http://localhost:3030/Tulus/query');

	// Namespace //
	\EasyRdf\RdfNamespace::set('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
	\EasyRdf\RdfNamespace::set('foaf', 'http://xmlns.com/foaf/0.1/');
	\EasyRdf\RdfNamespace::set('rdfs', 'http://www.w3.org/2000/01/rdf-schema#');
	\EasyRdf\RdfNamespace::set('geo', 'http://www.opengis.net/ont/geosparql#');
	\EasyRdf\RdfNamespace::set('dbp', 'http://dbpedia.org/property/');
	\EasyRdf\RdfNamespace::set('sfy', 'https://open.spotify.com/');
	\EasyRDf\RdfNamespace::setDefault('og');

	// Query Ambil Data dari DBPedia //
	$query_dbpedia = "
        SELECT * WHERE {
        ?tulus rdfs:label 'Tulus (singer)'@en.
		?tulus dbo:abstract ?description.
        ?tulus dbo:birthName ?birthName.
        FILTER( LANG (?description) = 'en')
    }";
	$result_dbpedia = $sparql_dbpedia->query($query_dbpedia);
	$dbpedia = [];
	foreach ($result_dbpedia as $row)
	{
		$dbpedia = [
			'description' => $row->description,
			'birthName' => $row->birthName,
		];
		break;
	}

	$query_dbpedia1 = 'SELECT * WHERE {
        ?tulus rdfs:label "Tulus (singer)"@en.
		?tulus dbo:birthYear ?a.
		?tulus dbo:activeYearsStartYear ?b.
		bind(year(now()) - year(?a) as ?umur).
		bind(year(now()) - year(?b) as ?lamaaktif).
    }';
	$result_dbpedia1 = $sparql_dbpedia->query($query_dbpedia1);
	$dbpedia1 = [];
	foreach ($result_dbpedia1 as $row)
	{
		$dbpedia1 = [
			'umur' => $row->umur,
			'lamaaktif' => $row->lamaaktif,
		];
		break;
	}
	
	// Query Untuk Mengambil Koordinat Map //
	$sparql_query_map = 'SELECT ?lat ?long ?name WHERE {
        ?subject geo:lat ?lat;
        geo:long ?long;
        dbp:officialName ?name.
    }';
	$result_rdf_map = $sparql_jena->query($sparql_query_map);
	$rdf_map = [];
	foreach ($result_rdf_map as $row)
	{
		$rdf_map = [
			'lat' => $row->lat,
			'long' => $row->long,
			'name' => $row->name,
		];
	}

	// Query Untuk Mengambil Statistik Chart //
	$sparql_query_album = "SELECT ?TujuhBelas ?Kelana ?Remedi ?Interaksi ?Ingkar ?JatuhSuka ?Nala ?HatiHatidiJalan ?Diri ?SatuKali
    WHERE {
        ?subject sfy:number ?TujuhBelas.
        ?subject sfy:number ?Remedi.
        ?subject sfy:number ?Interaksi.
        ?subject sfy:number ?Ingkar.
        ?subject sfy:number ?JatuhSuka.
        ?subject sfy:number ?Nala.
        ?subject sfy:number ?HatiHatidiJalan.
        ?subject sfy:number ?Kelana.
        ?subject sfy:number ?Diri.
        ?subject sfy:number ?SatuKali.
  
        FILTER( (?TujuhBelas) = '18255652')
        FILTER( (?Kelana) = '10385435')
        FILTER( (?Remedi) = '9781211')
        FILTER( (?Interaksi) = '54398256')
        FILTER( (?Ingkar) = '21560638')
        FILTER( (?JatuhSuka) = '16844048')
        FILTER( (?Nala) = '13566089')
        FILTER( (?HatiHatidiJalan) = '153191222')
        FILTER( (?Diri) = '63338847')
        FILTER( (?SatuKali) = '10291769')
	}";
	$result_rdf_album = $sparql_jena->query($sparql_query_album);
	$rdf_album= [];
	foreach ($result_rdf_album as $row)
	{
		$rdf_album = [
			'TujuhBelas'        =>  $row->TujuhBelas,
			'Kelana'            =>  $row->Kelana,
			'Remedi'            =>  $row->Remedi,
			'Interaksi'         =>  $row->Interaksi,
			'Ingkar'            =>  $row->Ingkar,
			'JatuhSuka'         =>  $row->JatuhSuka,
			'Nala'              =>  $row->Nala,
			'HatiHatidiJalan'   =>  $row->HatiHatidiJalan,
			'Diri'              =>  $row->Diri,
			'SatuKali'          =>  $row->SatuKali,
		];
	}

	//Query Menghitung Total Pendengar //
	$sparql_query_totalListener = 'SELECT (SUM(?number) as ?TotalListener)
	WHERE
	{
		VALUES (?number) {
			(18255652)
			(10385435)
			(9781211)
			(54398256)
			(21560638)
			(16844048)
			(13566089)
			(153191222)
			(63338847)
			(10291769)			 
		}
	}';
	$result_totalListener = $sparql_jena->query($sparql_query_totalListener);
	$rdf_total_pendengar = [];
	foreach ($result_totalListener as $row)
	{
		$rdf_total_pendengar = [
			'total' => $row->TotalListener
		];
	}

	// Query Untuk Memanggil Data dari RDF //
	$sparql_query = 'SELECT ?nama ?tanggallahir ?ta WHERE {
		?subject dbp:birthDate ?tanggallahir;
		dbp:yearsActive ?ta.
	} ';
	$result_rdf = $sparql_jena->query($sparql_query);
	$rdf = [];
	foreach ($result_rdf as $row)
	{
		$rdf = [
			'tanggallahir' => $row->tanggallahir,
			'ta' => $row->ta,
		];
	}

?>


<?php
	// Deklarasi Variabel //
	$latitude = $rdf_map['lat'];
	$longtitude = $rdf_map['long'];
	$name = $rdf_map['name'];
	$tanggallahirr = $rdf['tanggallahir'];
	$tahunaktif = $rdf['ta'];
	$description = $dbpedia['description'];
	$birthname = $dbpedia['birthName'];
	$umur = $dbpedia1['umur'];
	$lamaaktif = $dbpedia1['lamaaktif'];
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Tulus</title>
		<link href="css/bootstrap.min.css" rel="stylesheet">
		<link href="css/animate.min.css" rel="stylesheet">
		<link rel="stylesheet" href="css/owl.carousel.css">
		<link href="css/font-awesome.min.css" rel="stylesheet">
		<link href="css/style.css" rel="stylesheet">
		<link href="css/style-color.css" rel="stylesheet">	
		<link rel="shortcut icon" href="img/logo/icon.jpg">
		<script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>

		<!--CSS dan Javascript Leaflet JS-->
		<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" integrity="sha256-kLaT2GOSpHechhsozzB+flnD+zUyjE2LlfWPgU04xyI=" crossorigin=""/>
		<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js" integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM=" crossorigin=""></script>

		<!--Setting Google Chart-->
		<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
		<style type="text/css">
			body { font-family: sans-serif; }
			dt { font-weight: bold; }
			.image { float: right; margin: 15px; max-width: 50vh}
		</style>	
	</head>	
	<body>
		<header>
			<nav class="navbar navbar-fixed-top navbar-default">
				<div class="container">
					<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
						<ul class="nav navbar-nav navbar-right">
							<li><a href="#websemantik">About</a></li>
							<li><a href="#tempatlahir">Birth Place</a></li>
							<li><a href="#discography">Discography</a></li>
						</ul>
					</div>
				</div>
			</nav>
		</header>
		<div class="banner">
			<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
				<div class="carousel-inner" role="listbox">
					<div class="item active">
						<img src="img/banner/tulus.png" alt="...">
						<div class="container">
							<div class="carousel-caption slide-one">
								<h2 class="animated fadeInLeftBig"><i class="fas fa-music"></i>Tulus</h2>
								<h3 class="animated fadeInRightBig">Check The Infomation About Him Below</h3>
								<a href="#websemantik" class="animated fadeIn btn btn-theme">Biography</a>
							</div>
						</div>
					</div>
					<div class="item">
						<img src="img/banner/tulus3.png" alt="...">
						<div class="container">
							<div class="carousel-caption slide-two">
								<h2 class="animated fadeInLeftBig" style="color: black;"><i class="fas fa-music"></i> Tulus</h2>
								<h3 class="animated fadeInRightBig" style="color: black;">Check The Infomation About Him Below</h3>
								<a href="#websemantik" class="animated fadeIn btn btn-theme">Biography</a>
							</div>
						</div>
					</div>
				</div>
				<a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
					<span class="fa fa-arrow-left" aria-hidden="true"></span>
				</a>
				<a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
					<span class="fa fa-arrow-right" aria-hidden="true"></span>
				</a>
			</div>
		</div>
		<div class="nav-animate">
		</div>
		<div id="websemantik" class="hero pad">
			<div class="container">
				<div class="hero-content ">
					<h2>Tulus' Biography</h2>
					<hr>
					<p><?= $description ?></p>
				</div>
				<div class="hero-playlist">
					<div class="row">
						<div class="col-md-6 col-sm-6" style="margin-top: 40px;"> 
							<?php
								$doc = \EasyRdf\Graph::newAndLoad('https://dbpedia.org/page/Tulus_(singer)');
								if ($doc->image) {
									echo content_tag('img', null, array('src'=>$doc->image, 'class'=>'image'));
								} 
							?>
						</div>
						<div class="col-md-6 col-sm-6">
							<div class="playlist-content" style="width: 350px;">
								<ul class="list-unstyled">
									<li class="playlist-number">
										<div class="song-info">
										<div class="col-9">
											<dl>
												<br>
												<dt>Page:</dt> <dd><?= link_to($doc->url) ?></dd><br>
												<dt>Name:</dt> <dd><?= $birthname?></dd><br>
												<dt>Start Active:</dt> <dd><?= $tahunaktif ?></dd><br>
												<dt>Years Active:</dt> <dd><?= $lamaaktif?> Years </dd><br>
												<dt>Birth Date:</dt> <dd><?= $tanggallahirr?></dd><br>
												<dt>Age:</dt> <dd><?= $umur?> Years Old</dd><br>
												<dt>Title:</dt> <dd><?= $doc->title ?></dd><br>
											</dl>
										</div>
										</div>
										<div class="clearfix"></div>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
			
		<!-- Bagian Map -->
		<div class="default-heading" id="tempatlahir">
			<h2>Tulus' Birth Place</h2>
			<br>
			<h3 align="center"><?=$name?></h3>
		</div>
		<div id="map" style="height:100vh; width:180vh; margin:0 auto"></div>

		<!-- Bagian Google Chart -->
		<div class="featured pad" id="discography">
			<div class="default-heading">
				<h2>Tulus' Discography</h2>
				<br>
				<h3 align="center">Album Manusia</h3>
			</div>
			<div id="chart" style="width: 150vh; height: 100vh; margin:0 auto"></div>
			<br><br>
			<div class="container" style="position: center;">
  				<div class="row justify-content-md-center">
    				<div class="col col-lg-9" style="margin-left: 200px;">
						<p style="font-size:x-large">This Album Has Been Played With Total <b><?= $rdf_total_pendengar['total'] ?></b> Listener In <i>Spotify</i></p>
    				</div>
				</div>
			</div>
		</div>
		</div>
		<div class="news-letter">
			<div class="container">
				<div class="news-content ">
					<h3>Thank You</h3>
					<p><strong>Thank You For Your Attention</strong>. Sorry If There Is A Error, And Let Us Know If You Have Any Questions.</p>
				</div>
			</div>
		</div>
		<footer>
			<div class="container">
				<p class="copy-right">&copy; Kelompok 5 Tubes WS, 2022, All rights Are Reserved.</p>
			</div>
		</footer>
		<span class="totop"><a href="#"><i class="fa fa-chevron-up"></i></a></span>

		<!--Script Untuk Open Street Map-->
		<script>
			var map = L.map('map').setView([<?=$latitude?>, <?=$longtitude?>], 16);
			L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
				maxZoom: 19,
				attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
			}).addTo(map);
			var circle = L.circle([<?=$latitude?>, <?=$longtitude?>], {
				color: 'blue',
				fillColor: 'blue',
				fillOpacity: 0.5,
				radius: 400
			}).addTo(map);
		</script>

		<!--Script Untuk Google Chart-->
		<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
		<script type="text/javascript">
			google.charts.load('current', {'packages':['bar']});
			google.charts.setOnLoadCallback(drawStuff);
			function drawStuff() {
				var data = new google.visualization.arrayToDataTable([
				['Single', 'Total Listener'],
				["Tujuh Belas",  <?= $rdf_album['TujuhBelas'] ?>,],
				["Kelana", <?= $rdf_album['Kelana'] ?>,],
				["Remedi",  <?= $rdf_album['Remedi'] ?>,],
				["Interaksi",  <?= $rdf_album['Interaksi'] ?>,],
				['Ingkar',  <?= $rdf_album['Ingkar'] ?>,],
				["Jatuh Suka",  <?= $rdf_album['JatuhSuka'] ?>,],
				["Nala",  <?= $rdf_album['Nala'] ?>,],
				["Hati-Hati di Jalan",  <?= $rdf_album['HatiHatidiJalan'] ?>,],
				["Diri",  <?= $rdf_album['Diri'] ?>,],
				['Satu Kali', <?= $rdf_album['SatuKali'] ?>,]
				]);
				var options = {
				width: 1000,
				legend: { position: 'none' },
				chart: {
					title: 'Total Player on Spotify Album Manusia',
					subtitle: 'By: Tulus'},
				axes: {
					x: {
					0: { side: 'top', label: 'Album Manusia'} // Top x-axis.
					}
				},
				bar: { groupWidth: "100%" }
				};
				var chart = new google.charts.Bar(document.getElementById('chart'));
				chart.draw(data, google.charts.Bar.convertOptions(options));
			};
		</script>
	
		<!-- Script Untuk HTML -->
		<script src="js/jquery.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/waypoints.min.js"></script>
		<script src="js/owl.carousel.min.js"></script>
		<script src="js/jquery.nav.js"></script>
		<script src="js/respond.min.js"></script>
		<script src="js/html5shiv.js"></script>
		<script src="js/custom.js"></script>

	</body>	
</html>