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

	public function thumbnaildircheck()
	{
		if(!is_dir(Model::THUMBNAIL_DIR)) {
			return mkdir(Model::THUMBNAIL_DIR);
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

	/**
	 * Upload single file
	 * 
	 * @param string $index The file id
	 * @param string $destination File final destination
	 * @param bool|int $maxsize Max file size in octets
	 * @param bool|array $extensions List of authorized extensions
	 * @param bool $jpgrename Change the file exentension to .jpg
	 * 
	 * @return bool If upload process is a succes or not
	 */
	function simpleupload(string $index, string $destination, $maxsize = false, $extensions = false, bool $jpgrename = false) : bool
	{
	    //Test1: if the file is corectly uploaded
		if (!isset($_FILES[$index]) || $_FILES[$index]['error'] > 0) return false;
	    //Test2: check file size
		if ($maxsize !== false && $_FILES[$index]['size'] > $maxsize) return false;
	    //Test3: check extension
		$ext = substr(strrchr($_FILES[$index]['name'],'.'),1);
		if ($extensions !== false && !in_array($ext, $extensions)) return false;
		if($jpgrename !== false) {
			$destination .= '.jpg';
		} else {
			$destination .= '.' . $ext;
		}
	    //Move to dir
		return move_uploaded_file($_FILES[$index]['tmp_name'], $destination);
	}

	/**
	 * Upload multiple files
	 * 
	 * @param string $index Id of the file input
	 * @param string $target direction to save the files
	 */
	public function multiupload(string $index, string $target)
	{
        if($target[strlen($target)-1] != DIRECTORY_SEPARATOR)
                $target .= DIRECTORY_SEPARATOR;
            $count=0;
            foreach ($_FILES[$index]['name'] as $filename) 
            {
                $fileinfo = pathinfo($filename);
                $extension = idclean($fileinfo['extension']);
                $id = idclean($fileinfo['filename']);

                $tmp=$_FILES['file']['tmp_name'][$count];
                $count=$count + 1;
                $temp = $target . $id .'.' .$extension;
                move_uploaded_file($tmp, $temp);
                $temp='';
                $tmp='';
            }
	}

	public function adddir($dir, $name)
	{
		$newdir = $dir . DIRECTORY_SEPARATOR . $name;
		if(!is_dir($newdir)) {
			return mkdir($newdir);
		} else {
			return false;
		}
	}

}




?>