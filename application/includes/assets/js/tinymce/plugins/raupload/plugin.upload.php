<?php
list ( $root ) = explode ('application',dirname ( __FILE__ ) );
$upload_dir =  isset ( $_POST['upload_dir'] ) ? $_POST['upload_dir'] : "uploads/";
$base_url = isset ( $_POST['base_url'] ) ? $_POST['base_url'] : NULL;
if ( ! is_null ( $base_url ) ) :
	$root_path = explode ( $_SERVER['HTTP_HOST'], $base_url );
	$root_dir = trim ( end ( $root_path ), '/' ) . '/';
endif;
$upload_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $root_dir . $upload_dir;

if ( isset ( $_FILES["file"] ) )
{
	$ret = array();
	$error =$_FILES["file"]["error"];
	if(!is_array($_FILES["file"]["name"])) //single file
	{
 	 	$fileName = $_FILES["file"]["name"];
 		move_uploaded_file($_FILES["file"]["tmp_name"],$upload_path.$fileName);
 		list ( $ret['image_width'], $ret['image_height'] ) = getimagesize ( $base_url . $upload_dir . $fileName );
    	$ret['file_name']= $fileName;
    	$ret['file_url']= $base_url . $upload_dir;
	}
	else  //Multiple files, file[]
	{
	  $fileCount = count($_FILES["file"]["name"]);
	  for($i=0; $i < $fileCount; $i++)
	  {
	  	$fileName = $_FILES["file"]["name"][$i];
		move_uploaded_file($_FILES["file"]["tmp_name"][$i],$upload_path.$fileName);
 		list ( $width, $height ) = getimagesize ( $base_url . $upload_dir . $fileName );
	  	$ret[] = array (
	  		'file_name' => $fileName,
	  		'file_url' => $base_url . $upload_dir,
	  		'image_width' => $width, 'image_height' => $height
	  		);
	  }
	}
    echo json_encode($ret);
 }
 ?>