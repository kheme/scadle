<?php
// generate random number for this upload "session"
$rand = date("U").random_int(1, 999);
?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Free Web Based Source Code Plagiarism Detection - Scadle</title>
		<meta name="description" content="Scadl is a free web based source code plagiarism detection tool, developed by Okiemute Omuta (Kheme)">
		<meta name="author" content="Okiemute Omuta">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<style>
			* {
                font-family: 'Lucida Sans Unicode', 'Lucida Grande', Sans-Serif;
                line-height: 200%;
            }
            body {
                background-color: lightgray;
                padding: 20px;
            }
            .wrapper {
                width: 800px;
                background-color: white;
                margin: auto;
                padding: 30px 50px;
                border-radius: 3px;
                border: 2px solid darkgray;
                margin-bottom: 20px;
            }
            header, footer {
                text-align: center;
            }
            h3 span, footer {
                font-weight: 100;
                color: gray;
                font-size: smaller;
            }
            fieldset {
                border: 1px solid lightgray;
                padding: 10px 20px;
            }
            table {
                border: 0;
                width: 100%;
            }
			.plain td:first-child{
				width: 80%;
			}
            .plain td {
                padding: 5px;
                vertical-align: top;
            }
            thead td, tfoot td {
                font-weight: bold;
                background-color: lightgray;
                padding: 5px;
            }
            .res td {
                font-size: small;
                padding: 5px 10px;
            }
            .res .match {
                width: 25%;
                text-align: right;
            }
			.btn{
				cursor: pointer;
			}
		</style>
        <link href="{{url('src/css/jquery.fileuploader.css')}}" media="all" rel="stylesheet">
		<link href="{{url('src/css/bootstrap.min.css')}}" media="all" rel="stylesheet">
		<!--[if lt IE 9]>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.js"></script>
		<![endif]-->
	</head>
	<body>
		<header>
			<h3><a href="{{url('/')}}">Scadle</a> <span>- A free web based source code plagiarism detection tool</span></h3>
		</header>
		<form id="form" action="" class="wrapper" enctype="multipart/form-data" method="post">
			<div class="row">
			@if(isset($error))
			<div class="alert alert-danger in col-md-12">
				<strong>{{$error}}s</strong>
			</div>
			@endif
				<div class="col-md-6 pl-md-0">
					<label>
						<strong>Language Filter</strong>
					</label>
					<select id="ext" class="form-control" name="ext" required="required">
						<option value="asm">Assembly Language (.asm)</option>
						<option value="asp">ASP/ASP.Net (.asp)</option>
						<option value="cpp">C/C++ (.cpp)</option>
						<option selected="selected" value="php">PHP (.php)</option>
						<option value="vb">VB/VB.Net (.vb)</option>
					</select>
				</div>
				<div class="col-md-6 pr-md-0">
					<label>
					<strong>Tolerance Level</strong>
					</label>
					<select id="level" class="form-control" name="level" required="required">
						<option value="2">Very Low Tolerance</option>
						<option value="3">Low Tolerance</option>
						<option selected="selected" value="4">Normal Tolerance</option>
						<option value="5">High Tolerance</option>
						<option value="6">Very High Tolerance</option>
					</select>
				</div>
				
				<table class="plain">
					<tbody>
						<tr>
							<td class="pl-md-0">
								<input name="files" type="file" />
							</td>
							<td class="pr-md-0 align-middle text-right cursor">
								<button id="upload" class="btn btn-primary btn-lg" type="button">Upload &amp; Scan</button>
							</td>
						</tr>
					</tbody>
				</table>
				
				<div id="results">

				</div>
				@if(isset($results))
				<h4>Results</h4>
				<table class="res">
					<thead>
						<tr>
							<td>Files</td>
							<td class="match">Similarity</td>
						</tr>
					</thead>
					<tbody>
						@foreach($results as $files => $match)
						<tr>
							<td>{{str_replace($rando."_",'',$files)}}</td>
							<td class="match">{{$match}}%</td>
						</tr>
						@endforeach
					</tbody>
					<tfoot>
						<tr>
							<td>Files</td>
							<td class="match">Similarity</td>
						</tr>
					</tfoot>
				</table>
				@endif
			</div>
			<input name="rand" type="hidden" value="{{$rand}}">
		</form>
		<footer>Copyright &copy; {{date("Y")}} Okiemute Omuta</footer>
		<script src="{{url('src/js/jquery-3.2.1.min.js')}}"></script>
		<script src="{{url('src/js/jquery.fileuploader.min.js')}}"></script>
		<script>
			$(document).ready(function() {
				var failed  	= 0;
				var input 		= $('input[name="files"]').fileuploader({
					limit       : 20,
					extensions  : ['asm', 'asp', 'aspx', 'cpp', 'php', 'vb'],
					thumbnails  : null,
					addMore     : true,
					enableApi 	: true,
					upload 		: {
						url 		: '{{url('upload')}}',
						type		: 'POST',
						enctype 	: 'multipart/form-data',
						contentType : 'application/json; charset=UTF-8',
						data  		: {rand : {{$rand}}},
						start 		: false,
						synchron    : true,
						beforeSend  : function(item, listEl, parentEl, newInputEl, inputEl) {
									item.upload.data.ext = $('#ext').val();
									return true;
									},
						onSuccess 	: null,
						onError 	: function(item, listEl, parentEl, newInputEl, inputEl, jqXHR, textStatus, errorThrown) {
										failed++;
										return true;
									},
						onComplete 	: function(listEl, parentEl, newInputEl, inputEl, jqXHR, textStatus) {
										if(failed == 0){
											top.location= '{{url('/')}}/' + {{$rand}} + '?level=' + $('#level').val();
										}
										else{
											alert('ERROR: Please select the appropriate Language Filter.');
											top.location= '{{url('/')}}';
										}
									},
					},
				});
				var api = $.fileuploader.getInstance(input);
				$('#upload').click(function(){
					for(var i in api.getChoosedFiles()){
						api.getChoosedFiles()[i].upload.send();
					}
				});
			});
		</script>
	</body>
</html>