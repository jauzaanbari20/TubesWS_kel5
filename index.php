<?php
	require 'vendor/autoload.php';
	require_once __DIR__."/html_tag_helpers.php";
	$sparql_endpoint = 'https://dbpedia.org/sparql';

	// Jangan Lupa Buat Dataset Baru ya di Jena Fuseki, Nama Dataset-nya Tulus //
	$sparql_dbpedia = new \EasyRdf\Sparql\Client($sparql_endpoint);
	$sparql_jena = new \EasyRdf\Sparql\Client('http://localhost:3030/Tulus/query');

	// $uri_rdf = 'http://localhost/TubesWS_kel5/tulus.rdf';
	// $data = \EasyRdf\Graph::newAndLoad($uri_rdf);
	// $doc = $data->primaryTopic();

	// //ambil data dbpedia tulus dari tulus.pdf
	// $tulus_uri = 'http://localhost/TubesWS_kel5/tulus.rdf';
	// foreach ($doc->all('owl:sameAs') as $akun) {
	// 	$tulus_uri = $akun->get('foaf:homepage');
	// 	break;
	// }

	// Namespace //
	\EasyRdf\RdfNamespace::set('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
	\EasyRdf\RdfNamespace::set('foaf', 'http://xmlns.com/foaf/0.1/');
	\EasyRdf\RdfNamespace::set('rdfs', 'http://www.w3.org/2000/01/rdf-schema#');
	\EasyRdf\RdfNamespace::set('owl', 'http://www.w3.org/2002/07/owl#');
	\EasyRdf\RdfNamespace::set('geo', 'http://www.opengis.net/ont/geosparql#');
	\EasyRdf\RdfNamespace::set('dbp', 'http://dbpedia.org/property/');
	\EasyRdf\RdfNamespace::set('dc', 'http://purl.org/dc/elements/1.1/');
	\EasyRDf\RdfNamespace::setDefault('og');

	// Query Ambil Data dari DBPedia, Tinggal Tambahin aja Predikat ama Hasil yang Mau Diambil //
	$query_dbpedia = "
        SELECT * WHERE {
        ?tulus rdfs:label 'Tulus (singer)'@en.
		?tulus dbo:abstract ?description.
        ?tulus dbo:birthName ?birthName.
        ?tulus dbo:birthDate ?birthDate.
        FILTER( LANG (?description) = 'en')
    }";
	$result_dbpedia = $sparql_dbpedia->query($query_dbpedia);
	$dbpedia = [];
	foreach ($result_dbpedia as $row)
	{
		$dbpedia = [
			'description' => $row->description,
			'birthName' => $row->birthName,
			'birthDate' => $row->birthDate,
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

	// query manggil rdf
	$sparql_query_name = 'SELECT ?nama ?tanggallahir ?ta WHERE {
		?subject dbp:birthName ?nama;
		dbp:birthDate ?tanggallahir;
		dbp:yearsActive ?ta.
	} ';

	$result_rdf_name = $sparql_jena->query($sparql_query_name);
	$rdf_name = [];
	foreach ($result_rdf_name as $row)
	{
		$rdf_name = [
			'nama' => $row->nama,
			'tanggallahir' => $row->tanggallahir,
			'ta' => $row->ta,
		];
	}
?>

<!--Titik Koordinat Open Street Map-->
<?php
	$latitude = $rdf_map['lat'];
	$longtitude = $rdf_map['long'];
	$name = $rdf_map['name'];
	$namaa = $rdf_name['nama'];
	$tanggallahirr = $rdf_name['tanggallahir'];
	$tahunaktif = $rdf_name['ta'];
	$description = $dbpedia['description'];
	$birthname = $dbpedia['birthName'];
	$birthdate = $dbpedia['birthDate']
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Web Semantik</title>
		<!-- Styles -->
		<!-- Bootstrap CSS -->
		<link href="css/bootstrap.min.css" rel="stylesheet">	
		<!-- Animate CSS -->
		<link href="css/animate.min.css" rel="stylesheet">
		<!-- Basic stylesheet -->
		<link rel="stylesheet" href="css/owl.carousel.css">
		<!-- Font awesome CSS -->
		<link href="css/font-awesome.min.css" rel="stylesheet">		
		<!-- Custom CSS -->
		<link href="css/style.css" rel="stylesheet">
		<link href="css/style-color.css" rel="stylesheet">		
		<!-- Favicon -->
		<link rel="shortcut icon" href="img/logo/icon.jpg">
		<!--CSS dan Javascript Leaflet JS-->
		<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" integrity="sha256-kLaT2GOSpHechhsozzB+flnD+zUyjE2LlfWPgU04xyI=" crossorigin=""/>
		<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js" integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM=" crossorigin=""></script>
		<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"> -->
	<style type="text/css">
		body { font-family: sans-serif; }
		dt { font-weight: bold; }
		.image { float: right; margin: 15px; max-width: 50vh}
	</style>
	</head>	
	<body>

			<!-- header area -->
			<header>
				<nav class="navbar navbar-fixed-top navbar-default">
					<div class="container">
						<!-- Brand and toggle get grouped for better mobile display -->
						<div class="navbar-header">
							<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
								<span class="sr-only">Toggle navigation</span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
							</button>
						</div>

						<!-- Collect the nav links, forms, and other content for toggling -->
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
			<!--/ header end -->
			
			<!-- banner area -->
			<div class="banner">
				<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
					<!-- Wrapper for slides -->
					<div class="carousel-inner" role="listbox">
						<div class="item active">
							<img src="img/banner/tulus.png" alt="...">
							<div class="container">
								<!-- banner caption -->
								<div class="carousel-caption slide-one">
									<!-- heading -->
									<h2 class="animated fadeInLeftBig"> Tulus </h2>
									<!-- paragraph -->
									<!-- <h3 class="animated fadeInRightBig"> About Tulus </h3> -->
								</div>
							</div>
						</div>
						<div class="item">
							<img src="img/banner/tulus2.jpg" style="width:300vh;" alt="...">
						</div>
					</div>

					<!-- Controls -->
					<a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
						<span class="fa fa-arrow-left" aria-hidden="true"></span>
					</a>
					<a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
						<span class="fa fa-arrow-right" aria-hidden="true"></span>
					</a>
				</div>
			</div>
			<!--/ banner end -->
			
			<!-- block for animate navigation menu -->
			<div class="nav-animate"></div>
			
			<!-- Hero block area -->
			<div id="websemantik" class="hero pad">
				<div class="container">
					<!-- hero content -->
					<div class="hero-content ">
						<!-- heading -->
						<h2>Tugas Web Semantik</h2>
						<hr>
						<!-- paragraph -->
						<!-- aku mau ambil nama depan, sm belakang dri file rdf, gimanaaa?? -->
						<h4>Nama : <?=$birthname?> </h4> 
						<h4>Tahun Aktif : <?= $tahunaktif ?> </h4>
						<h4>Tanggal Lahir : <?= $tanggallahirr ?></h4>
						<p><?= $description ?></p>
					</div>
					<!-- hero play list -->
					<div class="hero-playlist">
						<div class="row">
							<div class="col-md-6 col-sm-6">
								<!-- music album image -->
								<div class="figure"><?php
										$doc = \EasyRdf\Graph::newAndLoad('https://dbpedia.org/page/Tulus_(singer)');
										if ($doc->image) {
										echo content_tag('img', null, array('src'=>$doc->image, 'class'=>'image'));
										} 
										// $lol = \EasyRdf\Graph::newAndLoad('https://id.wikipedia.org/wiki/Tulus_(penyanyi)');
										// if ($lol->image) {
										// echo content_tag('img', null, array('src'=>$lol->image, 'class'=>'image'));
										// }
										?>
								</div>
							</div>
							<div class="col-md-6 col-sm-6">
								<!-- play list -->
								<div class="playlist-content">
									<ul class="list-unstyled">
										<li class="playlist-number">
											<!-- song information -->
											<div class="song-info">
											<div class="col-9">
												<dl>
													<dt>Page:</dt> <dd><?= link_to($doc->url) ?></dd>
													<dt>Title:</dt> <dd><?= $doc->title ?></dd>
													<dt>Description:</dt> <dd><?= $doc->description ?></dd>
												</dl>
											</div>
											</div>
											<!-- music icon -->
											<div class="music-icon">
												<a href="#"><i class="fa fa-play"></i></a>
												<a href="#"><i class="fa fa-pause"></i></a>
											</div>
											<div class="clearfix"></div>
										</li>
										<!-- <li class="playlist-number">
											<div class="song-info">
											<div class="col-9">
												<dl>
													<dt>Page:</dt> <dd><?= link_to($lol->url) ?></dd>
													<dt>Title:</dt> <dd><?= $lol->title ?></dd>
													<dt>Description:</dt> <dd><?= $lol->description ?></dd>
												</dl>
												</div>
											</div>
											<div class="music-icon">
												<a href="#"><i class="fa fa-play"></i></a>
												<a href="#"><i class="fa fa-pause"></i></a>
											</div>
											<div class="clearfix"></div>
										</li> -->
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!--/ hero end -->
			
			<!-- Bagian Map -->
			<div class="default-heading" id="tempatlahir">
				<h2>Tulus' Birth Place</h2>
				<br>
				<h3 align="center"><?=$name?></h3>
			</div>
			<div id="map" style="height:100vh; width:150vh; margin:0 auto"></div>
			<!-- End Map -->

			<!-- featured abbum -->
			<div class="featured pad" id="discography">
				<div class="container">
					<!-- default heading -->
					<div class="default-heading">
						<!-- heading -->
						<h2>DISCOGRAPHY</h2>
					</div>
					<!-- featured album elements -->
					<div class="featured-element">
						<div class="row">
							<div class="col-md-4 col-sm-6">
								<!-- featured item -->
								<div class="featured-item ">
									<!-- image container -->
									<div class="figure">
										<!-- image -->
										<img class="img-responsive" src="img/featured/1.jpg" alt="" />
										<!-- paragraph -->
										<p>There are many variations of passages available, but the majority have suffered Lorem alteration in some form, by injected look even slightly believable.</p>
									</div>
									<!-- featured information -->
									<div class="featured-item-info">
										<!-- featured title -->
										<h4>Power Pop</h4>
										<!-- horizontal line -->
										<hr />
										<!-- some responce from social medial or web likes -->
										<p>1024+ <span class="label label-theme">Like</span> &nbsp;&nbsp; 825+ <span class="label label-theme">Love</span></p>
									</div>
								</div>
							</div>
							<div class="col-md-4 col-sm-6">
								<!-- featured item -->
								<div class="featured-item ">
									<!-- image container -->
									<div class="figure">
										<!-- image -->
										<img class="img-responsive" src="img/featured/2.jpg" alt="" />
										<!-- paragraph -->
										<p>There are many variations of passages available, but the majority have suffered Lorem alteration in some form, by injected look even slightly believable.</p>
									</div>
									<!-- featured information -->
									<div class="featured-item-info">
										<!-- featured title -->
										<h4>Bad Brains</h4>
										<!-- horizontal line -->
										<hr />
										<!-- some responce from social medial or web likes -->
										<p>1024+ <span class="label label-theme">Like</span> &nbsp;&nbsp; 825+ <span class="label label-theme">Love</span></p>
									</div>
								</div>
							</div>
							<div class="col-md-4 col-sm-6">
								<!-- featured item -->
								<div class="featured-item ">
									<!-- image container -->
									<div class="figure">
										<!-- image -->
										<img class="img-responsive" src="img/featured/3.jpg" alt="" />
										<!-- paragraph -->
										<p>There are many variations of passages available, but the majority have suffered Lorem alteration in some form, by injected look even slightly believable.</p>
									</div>
									<!-- featured information -->
									<div class="featured-item-info">
										<!-- featured title -->
										<h4>The Mars Volta</h4>
										<!-- horizontal line -->
										<hr />
										<!-- some responce from social medial or web likes -->
										<p>1024+ <span class="label label-theme">Like</span> &nbsp;&nbsp; 825+ <span class="label label-theme">Love</span></p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- features end -->
			<!-- news letter -->
			<div class="news-letter">
				<div class="container">
					<!-- news letter inner content -->
					<div class="news-content ">
						<!-- heading -->
						<h3>Sign Up For Newsletter</h3>
						<!-- paragraph -->
						<p><strong>Contact Us</strong> and let us know if you have any questions, Don't forget to subscribe for more awesome stuff by typing mail bellow.</p>
						<!-- subscribe form -->
						<form>
							<div class="input-group">
								<input type="text" class="form-control input-lg" placeholder="Email">
								<span class="input-group-btn">
									<button class="btn btn-default btn-lg" type="button">Subscribe</button>
								</span>
							</div><!-- /input-group -->
						</form>
					</div>
				</div>
			</div>
			<!-- news letter end -->
			
			
			<!-- footer -->
			<footer>
				<div class="container">
					<p class="copy-right">&copy; copyright 2018, All rights are reserved.</p>
				</div>
			</footer>
			<!-- footer end -->
			
			<!-- Scroll to top -->
			<span class="totop"><a href="#"><i class="fa fa-chevron-up"></i></a></span> 
			
		</div>

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

		<!-- JQuery -->
		<script src="js/jquery.js"></script>
		<!-- Bootstrap JS -->
		<script src="js/bootstrap.min.js"></script>
		<!-- WayPoints JS -->
		<script src="js/waypoints.min.js"></script>
		<!-- Include js plugin -->
		<script src="js/owl.carousel.min.js"></script>
		<!-- One Page Nav -->
		<script src="js/jquery.nav.js"></script>
		<!-- Respond JS for IE8 -->
		<script src="js/respond.min.js"></script>
		<!-- HTML5 Support for IE -->
		<script src="js/html5shiv.js"></script>
		<!-- Custom JS -->
		<script src="js/custom.js"></script>
	</body>	
</html>