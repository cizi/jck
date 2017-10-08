<?php

namespace App\Controller;

use Nette\Http\FileUpload;

class FileController {

	/** @var string */
	private $pathDb;

	/** @var string */
	private $path;

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
				if (($imagesize[0] > 800) || ($imagesize[1] > 800)) {    // pokud je soubor větší zmenším ho
					$manipulator = new \ImageManipulator($fileUpload->getTemporaryFile());
					$newImage = $manipulator->resample(800, 800);
					$manipulator->save($this->path);
					$this->imageFixOrientation($this->pathDb);
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

	private function imageFixOrientation($filename) {
		$exif = exif_read_data($filename);
		if (!empty($exif['Orientation'])) {
			$image = imagecreatefromjpeg($filename);
			switch ($exif['Orientation']) {
				case 3:
					$image = imagerotate($image, 180, 0);
					break;

				case 6:
					$image = imagerotate($image, -90, 0);
					break;

				case 8:
					$image = imagerotate($image, 90, 0);
					break;
			}
			imagejpeg($image, $filename);
		}
	}
}