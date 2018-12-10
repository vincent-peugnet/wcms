<?php

class Modelmedia extends Model
{

	public function addmedia(array $file, $maxsize = 2 ** 24, $id)
	{
		$message = 'runing';
		$id = strtolower(strip_tags($id));
		$id = str_replace(' ', '_', $id);
		if (isset($file) and $file['media']['error'] == 0 and $file['media']['size'] < $maxsize) {
			$infosfichier = pathinfo($file['media']['name']);
			$extension_upload = $infosfichier['extension'];
			$extensions_autorisees = $this::MEDIA_EXTENSIONS;
			if (in_array($extension_upload, $extensions_autorisees)) {
				if (!file_exists($this::MEDIA_DIR . $id . '.' . $extension_upload)) {

					$extension_upload = strtolower($extension_upload);
					$uploadok = move_uploaded_file($file['media']['tmp_name'], $this::MEDIA_DIR . $id . '.' . $extension_upload);
					if ($uploadok) {
						$message = 'uploadok';
					} else {
						$message = 'uploaderror';
					}
				} else {
					$message = 'filealreadyexist';

				}
			}
		} else {
			$message = 'filetoobig';

		}

		return $message;
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

}




?>