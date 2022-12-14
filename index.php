<?php
	require 'vendor/autoload.php';
?>

<!--Titik Koordinat Open Street Map-->
<?php
	$latitude = -0.305556;
	$longtitude = 100.369164;
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Tulus</title>

	<!--CSS dan Javascript Leaflet JS-->
	<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" integrity="sha256-kLaT2GOSpHechhsozzB+flnD+zUyjE2LlfWPgU04xyI=" crossorigin=""/>
	<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js" integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM=" crossorigin=""></script>

</head>
<body>

	<!--Div Tempat Map nya, Bisa dipindahkan Sesuai Template HTML nya. Tinggal Diubah aja Stylenya-->
	<div id="map" style="height:50vh; width:100vh"></div>

	<!--Script Untuk Open Street Map-->
	<script>
		var map = L.map('map').setView([<?=$latitude?>, <?=$longtitude?>], 14.5);
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
		var popup = L.popup()
		.setLatLng([<?=$latitude?>, <?=$longtitude?>])
		.setContent("Tulus' Birth Place")
		.openOn(map);
	</script>
	
</body>
</html>