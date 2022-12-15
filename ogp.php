<?php 

require 'vendor/autoload.php';
require_once __DIR__."/html_tag_helpers.php";

    \EasyRdf\RdfNamespace::setDefault('og');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <title>Document</title> -->
    <title>Open Graph Protocol example</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <style type="text/css">
    body { font-family: sans-serif; }
    dt { font-weight: bold; }
    .image { float: left; margin: 10px; max-width: 200px}
  </style>
</head>
<body>

<div class="container">
  <div class="row">

    <div class="col-auto">
      <?php
        $doc = \EasyRdf\Graph::newAndLoad('https://dbpedia.org/page/Tulus_(singer)');
        if ($doc->image) {
          echo content_tag('img', null, array('src'=>$doc->image, 'class'=>'image'));
        }
      ?>
    </div>

    <div class="col-9">
      <dl>
        <dt>Page:</dt> <dd><?= link_to($doc->url) ?></dd>
        <dt>Title:</dt> <dd><?= $doc->title ?></dd>
        <dt>Description:</dt> <dd><?= $doc->description ?></dd>
      </dl>
    </div>

    <div class="col-auto">
      <?php
        $doc = \EasyRdf\Graph::newAndLoad('https://id.wikipedia.org/wiki/Tulus_(penyanyi)');
        if ($doc->image) {
          echo content_tag('img', null, array('src'=>$doc->image, 'class'=>'image'));
        }
      ?>
    </div>

    <div class="col-9">
      <dl>
        <dt>Page:</dt> <dd><?= link_to($doc->url) ?></dd>
        <dt>Title:</dt> <dd><?= $doc->title ?></dd>
        <dt>Description:</dt> <dd><?= $doc->description ?></dd>
      </dl>
    </div>

  </div>
  
</div>

</body>
</html>