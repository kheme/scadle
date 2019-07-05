public function DoUpload(){
$user = (Auth::check())?Auth::user():0;
$body = Body::where("deleted",0)->lists("body_name","body_id");
$info = ["bodies" => $body, "title" => $title];
$title = "Bulk Upload Compliances";
Session::put("aside", "0503");
return view("upload_compliances", $info);
}