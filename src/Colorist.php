<?php

namespace Asboldyrev\YaColors;

use Exception;
use Imagick;
use ImagickPixel;

class Colorist {
	/**
	 * @var Imagick
	 */
	protected $originalImage;

	/**
	 * @var array
	 */
	protected $hsl;


	public static function create(string $imagePath) {
		return new self($imagePath);
	}


	public function __construct(string $imagePath) {
		$this->originalImage = new Imagick($imagePath);

		$this->saveFile(clone $this->originalImage, '-original');
		$cleaned_image = $this->cleanImage();
		$this->resizeImage($cleaned_image);

		return $this;
	}


	public function getHSLColors() {
		return $this->hsl;
	}


	public function getHEX(array $hsl) {
		$rgb = $this->getRGB($hsl);
		$result = [];

		foreach ($rgb as $key => $value) {
			$result[$key] = dechex($rgb[$key]);

			if ($rgb[$key] < 16) {
				$result[$key] = '0' . $result[$key];
			}
		}

		return '#' . implode('', $result);
	}


	public function getFileUrl(string $postfix = NULL) {
		$filename = pathinfo($this->originalImage->getImageFilename(), PATHINFO_FILENAME);
		$extension = pathinfo($this->originalImage->getImageFilename(), PATHINFO_EXTENSION);

		return sprintf('/media/%s/%s%s.%s', $filename, $filename, $postfix, $extension);
	}


	protected function cleanImage() {
		$colors = [];
		$iterator = $this->originalImage->getPixelIterator();
		foreach ($iterator as $row => $pixels) {
			/** @var ImagickPixel $pixel */
			foreach ($pixels as $col => $pixel) {
				$color = $pixel->getColor();      // values are 0-255
				$alpha = $pixel->getColor(true);  // values are 0.0-1.0

				$r = $color['r'];
				$g = $color['g'];
				$b = $color['b'];
				$a = $alpha['a'];

				$l = $pixel->getHSL()['luminosity'];

				if ($l != 0 && $l != 1 && (int)$a != 0) {
					$colors[] = compact('r', 'g', 'b', 'a');
				}
			}
			$iterator->syncIterator();
		}

		$width = (int)(sqrt(count($colors)));
		$height = $width;

		if (sqrt(count($colors)) != $width) {
			$height++;
		}

		$cleaned_image = new Imagick();
		$cleaned_image->newImage($width, $height + 1, new ImagickPixel("black"));
		$cleaned_image->setImageFormat($this->originalImage->getImageFormat());

		$iterator = $cleaned_image->getPixelIterator();
		$count = 0;

		foreach ($iterator as $row => $pixels) {
			foreach ($pixels as $col => $pixel) {
				try {
					$pixel->setColor('rgba(' . $colors[$count]['r'] . ', ' . $colors[$count]['g'] . ',  ' . $colors[$count]['b'] . ', ' . $colors[$count]['a'] . ')');
				} catch (Exception $exception) {
				}
				$count++;
			}
			$iterator->syncIterator();
		}

		$this->saveFile($cleaned_image, '-cleaned');

		return $cleaned_image;
	}


	protected function resizeImage(Imagick $image) {
		$filter = Imagick::FILTER_BOX;
		$palette_image = clone $image;
		$palette_image->resizeImage(2, 2, $filter, 0);

		$iterator = $palette_image->getPixelIterator();
		foreach ($iterator as $row => $pixels) {
			foreach ($pixels as $col => $pixel) {
				$hsl = $pixel->getHSL();

				$h = $hsl['hue'];
				$s = $hsl['saturation'];
				$l = $hsl['luminosity'];
				$this->hsl[] = compact('h', 's', 'l');
			}
			$iterator->syncIterator();
		}

		usort($this->hsl, function ($a, $b) {
			return $a['l'] > $b['l'];
		});

		$palette_image->resizeImage(
			$this->originalImage->getImageWidth(),
			$this->originalImage->getImageHeight(),
			$filter,
			0
		);

		$this->saveFile($palette_image, '-resize');

		return $palette_image;
	}


	protected function saveFile(Imagick $image, string $postfix = NULL) {
		$dirpath = $this->getFileDir();
		$filepath = $this->getFilePath($postfix);

		if (!file_exists($dirpath)) {
			mkdir($dirpath);
		}

		$image->writeImage($filepath);
	}


	protected function getFilePath($postfix = NULL) {
		$dirpath = $this->getFileDir();
		$filename = pathinfo($this->originalImage->getImageFilename(), PATHINFO_FILENAME);
		$extension = pathinfo($this->originalImage->getImageFilename(), PATHINFO_EXTENSION);

		return $dirpath . sprintf('%s%s.%s', $filename, $postfix, $extension);
	}


	protected function getFileDir() {
		$filename = pathinfo($this->originalImage->getImageFilename(), PATHINFO_FILENAME);

		return sprintf('%s/../public/media/%s/', __DIR__, $filename);
	}


	public function getRGBAsString(array $hsl, $reverse = false) {
		$rgb = $this->getRGB($hsl);

		if ($reverse) {
			return 'rgb(' . (255 - $rgb['r']) . ',' . (255 - $rgb['g']) . ',' . (255 - $rgb['b']) . ')';
		} else {
			return 'rgb(' . $rgb['r'] . ',' . $rgb['g'] . ',' . $rgb['b'] . ')';
		}
	}


	protected function getRGB(array $hsl) {
		$h = $hsl['h'];
		$s = $hsl['s'];
		$l = $hsl['l'];

		$r = $l;
		$g = $l;
		$b = $l;
		$v = ($l <= 0.5) ? ($l * (1.0 + $s)) : ($l + $s - $l * $s);
		if ($v > 0) {
			$m = 0;
			$sv = 0;
			$sextant = 0;
			$fract = 0;
			$vsf = 0;
			$mid1 = 0;
			$mid2 = 0;

			$m = $l + $l - $v;
			$sv = ($v - $m) / $v;
			$h *= 6.0;
			$sextant = floor($h);
			$fract = $h - $sextant;
			$vsf = $v * $sv * $fract;
			$mid1 = $m + $vsf;
			$mid2 = $v - $vsf;

			switch ($sextant) {
				case 0:
					$r = $v;
					$g = $mid1;
					$b = $m;
					break;
				case 1:
					$r = $mid2;
					$g = $v;
					$b = $m;
					break;
				case 2:
					$r = $m;
					$g = $v;
					$b = $mid1;
					break;
				case 3:
					$r = $m;
					$g = $mid2;
					$b = $v;
					break;
				case 4:
					$r = $mid1;
					$g = $m;
					$b = $v;
					break;
				case 5:
					$r = $v;
					$g = $m;
					$b = $mid2;
					break;
			}
		}

		return ['r' => $r * 255.0, 'g' => $g * 255.0, 'b' => $b * 255.0];
	}
}
