<?php
$app->get ('/',         function() use($app){return $app->make('view')->make('home');});
$app->get ('{rand}',    ['uses' => 'ScanControllerN@DoScan']);
$app->post('upload',    ['uses' => 'ScanControllerN@DoUpload']);
