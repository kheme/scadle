public function main(){
	Session::put('aside', '0601');
	if (Auth::check())
	$user = Auth::user();
	//$user = (Auth::check())?Auth::user():0;
	$bodies = Body::where('deleted','=',0)->get();
	$title 	= 'Compliance Bodies';
	$data 	= ['title' => $title, 'bodies' => $bodies];
	return view('body', $data);
}