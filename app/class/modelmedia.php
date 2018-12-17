<?php

class Modelmedia extends Model
{


	public function basedircheck()
	{
		if(!is_dir(Model::MEDIA_DIR)) {
			return mkdir(Model::MEDIA_DIR);
		} else {
			return true;
		}
	}

	public function favicondircheck()
	{
		if(!is_dir(Model::FAVICON_DIR)) {
			return mkdir(Model::FAVICON_DIR);
		} else {
			return true;
		}
	}

	public function getmedia($entry, $dir)
	{
		$fileinfo = pathinfo($entry);

		if(isset($fileinfo['extension'])) {
			$filepath = $fileinfo['dirname'] . '.' . $fileinfo['extension'];
	
			$datas = array(
				'id' => str_replace('.' . $fileinfo['extension'], '', $fileinfo['filename']),
				'path' => $dir,
				'extension' => $fileinfo['extension']
			);
			return new Media($datas);

		} else {
			return false;
		}


	}

	public function getlistermedia($dir, $type = "all")
	{
		if ($handle = opendir($dir)) {
			$list = [];
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != "..") {

					$media = $this->getmedia($entry, $dir);

					if($media != false) {
	
						$media->analyse();
	
						if (in_array($type, self::MEDIA_TYPES)) {
							if ($media->type() == $type) {
								$list[] = $media;
							}
						} else {
							$list[] = $media;
						}

					}



				}
			}
		}

		return $list;

	}



	public function listfavicon()
	{
		$glob = Model::FAVICON_DIR . '*.png';
		$faviconlist = glob($glob);
		$count = strlen(Model::FAVICON_DIR);
		$faviconlist = array_map(function($input) use($count) {
			return substr($input, $count);
		}, $faviconlist);
		return $faviconlist;
		
	}


	public function listdir($dir)
	{

	
	$result = array(); 

	$cdir = scandir($dir); 
	$result['dirfilecount'] = 0;
	foreach ($cdir as $key => $value) 
	{ 
		if (!in_array($value,array(".",".."))) 
		{ 
			if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) 
			{ 
				$result[$value] = $this->listdir($dir . DIRECTORY_SEPARATOR . $value); 
			} 
			else 
			{ 
				$result['dirfilecount'] ++; 
			} 
		} 
	} 
	
	return $result; 

	}

	public function upload(string $target)
	{
        if($target[strlen($target)-1] != DIRECTORY_SEPARATOR)
                $target .= DIRECTORY_SEPARATOR;
            $count=0;
            foreach ($_FILES['file']['name'] as $filename) 
            {
                $fileinfo = pathinfo($filename);
                $extension = idclean($fileinfo['extension']);
                $id = idclean($fileinfo['filename']);

                $temp=$target;
                $tmp=$_FILES['file']['tmp_name'][$count];
                $count=$count + 1;
                $temp .=  $id .'.' .$extension;
                move_uploaded_file($tmp,$temp);
                $temp='';
                $tmp='';
            }
	}

	public function adddir($dir, $name)
	{
		$name = idclean($name);
		$newdir = $dir . DIRECTORY_SEPARATOR . $name;
		if(!is_dir($newdir)) {
			return mkdir($newdir);
		} else {
			return false;
		}
	}

}




?>