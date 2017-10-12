<?php

namespace App\Controller;

use Nette\Http\FileUpload;

class FileController {

	/** @var string */
	private $pathDb;

	/** @var string */
	private $path;

	public static function imageFixOrientation($filename) {
		$type = exif_imagetype($filename); // [] if you don't have exif you could use getImageSize()
		$allowedTypes = array(
			1,  // [] gif
			2,  // [] jpg
			3  // [] png
		);
		if (!in_array($type, $allowedTypes)) {
			return false;
		}
		$ctype="image/jpeg";
		switch ($type) {
			case 1 :
				$im = imageCreateFromGif($filename);
				$ctype = "image/gif";
				break;
			case 2 :
				$im = imageCreateFromJpeg($filename);
				break;
			case 3 :
				$im = imageCreateFromPng($filename);
				$ctype = "image/png";
				break;
		}

		if (isset($im)) {
			// header('Content-type: ' . $ctype);
			$exif = exif_read_data($filename);
			if (!empty($exif['Orientation'])) {
				$source = imagecreatefromjpeg($filename);
				switch ($exif['Orientation']) {
					case 3:
						$image = imagerotate($source, 180, 0);
						break;

					case 6:
						$image = imagerotate($source, -90, 0);
						break;

					case 8:
						$image = imagerotate($source, 90, 0);
						break;
				}
				if (isset($image)) {
					ob_start();
					imagejpeg($image, NULL, 100);
					$rawImageBytes = ob_get_clean();
					echo 'data:'.$ctype.';base64,' . base64_encode( $rawImageBytes );
				}
			}
			ob_start();
			imagejpeg($im, NULL, 100);
			$rawImageBytes = ob_get_clean();
			echo 'data:'.$ctype.';base64,"' . base64_encode( $rawImageBytes );
		}
	}

	/**
	 * @param FileUpload $fileUpload
	 * @param array $formats example: ["jpg", "png", ...etc]
	 * @return bool
	 */
	public function upload(FileUpload $fileUpload, array $formats, $baseUrl) {
		$result = true;
		try {
			$suffix = pathinfo($fileUpload->name, PATHINFO_EXTENSION);
			if (!in_array(strtolower($suffix), $formats)) {
				return false;
			}
			$this->pathDb = $baseUrl . 'upload/' . date("Ymd-His") . "-" . $fileUpload->name;
			$this->path = UPLOAD_PATH . '/' . date("Ymd-His") . "-" . $fileUpload->name;

			if ($this->isFileImage($fileUpload->getTemporaryFile())) {
				$imagesize = getimagesize($fileUpload->getTemporaryFile());
				if (($imagesize[0] > 2000) || ($imagesize[1] > 2000)) {    // pokud je soubor větší zmenším ho
					$manipulator = new \ImageManipulator($fileUpload->getTemporaryFile());
					$newImage = $manipulator->resample(2000, 2000);
					$manipulator->save($this->path);
					// $this->imageFixOrientation($this->pathDb);
				} else {
					$fileUpload->move($this->path);
				}
			} else {
				$fileUpload->move($this->path);
			}
		} catch (\Exception $e) {
			dump($e->getMessage());
			$result = false;
		}

		return $result;
	}

	/**
	 * @return string
	 */
	public function getPathDb() {
		return $this->pathDb;
	}

	/**
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * @param string $path
	 * @return bool
	 */
	private function isFileImage($path) {
		if(@is_array(getimagesize($path))){
			$image = true;
		} else {
			$image = false;
		}

		return $image;
	}
}