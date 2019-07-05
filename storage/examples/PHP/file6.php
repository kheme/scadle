public function upload(){
	Session::put('aside', '0503');					// set side menu
	if (Auth::check()){
		$user = Auth::user();
	}
	
	$body = Body::where('deleted','=',0)->lists('body_name','body_id');
	$title = 'Bulk Upload Compliances';
	
	$data = [
		'title' 	=> $title,
		'bodies'	=> $body,
	];
	
	return view('compliances_upload', $data);
}