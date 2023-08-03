<?php

/**
 * Vvveb
 *
 * Copyright (C) 2022  Ziadin Givan
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 */

namespace Vvveb\System\Image;

$memory_limit = ini_get('memory_limit');

if ((int)$memory_limit < 128) {
	ini_set('memory_limit','128M');
}
class Image {
	private $file;

	private $image;

	private $info;

	public function __construct($file) {
		if (file_exists($file)) {
			$this->file = $file;

			$info = getimagesize($file);

			$this->info = [
				'width'  => $info[0],
				'height' => $info[1],
				'bits'   => $info['bits'] ?? '',
				'mime'   => $info['mime'] ?? '',
			];

			$this->image = $this->create($file);
		} else {
			throw new \Exception('Could not load image ' . $file);
		}
	}

	private function create($image) {
		$mime = $this->info['mime'];

		if ($mime == 'image/gif') {
			return imagecreatefromgif($image);
		} elseif ($mime == 'image/png') {
			return imagecreatefrompng($image);
		} elseif ($mime == 'image/jpeg') {
			return imagecreatefromjpeg($image);
		} elseif ($mime == 'image/webp') {
			return imagecreatefromwebp($image);
		}
	}

	public function save($file, $quality = 85) {
		$info = pathinfo($file);

		$extension = strtolower($info['extension']);

		if (is_resource($this->image)) {
			if ($extension == 'jpeg' || $extension == 'jpg') {
				imagejpeg($this->image, $file, $quality);
			} elseif ($extension == 'png') {
				imagepng($this->image, $file);
			} elseif ($extension == 'gif') {
				imagegif($this->image, $file);
			}

			imagedestroy($this->image);
		}
	}

	public function resize($width = 0, $height = 0, $default = '') {
		if (! $this->info['width'] || ! $this->info['height']) {
			return;
		}

		$xpos  = 0;
		$ypos  = 0;
		$scale = 1;

		$scale_w = $width / $this->info['width'];
		$scale_h = $height / $this->info['height'];

		if ($default == 'w') {
			$scale = $scale_w;
		} elseif ($default == 'h') {
			$scale = $scale_h;
		} else {
			$scale = min($scale_w, $scale_h);
		}

		if ($scale == 1 && $scale_h == $scale_w && $this->info['mime'] != 'image/png') {
			return;
		}

		$new_width  = (int)($this->info['width'] * $scale);
		$new_height = (int)($this->info['height'] * $scale);
		$xpos       = (int)(($width - $new_width) / 2);
		$ypos       = (int)(($height - $new_height) / 2);

		$image_old   = $this->image;
		$this->image = imagecreatetruecolor($width, $height);

		if (isset($this->info['mime']) && $this->info['mime'] == 'image/png') {
			imagealphablending($this->image, false);
			imagesavealpha($this->image, true);
			$background = imagecolorallocatealpha($this->image, 255, 255, 255, 127);
			imagecolortransparent($this->image, $background);
		} else {
			$background = imagecolorallocate($this->image, 255, 255, 255);
		}

		imagefilledrectangle($this->image, 0, 0, $width, $height, $background);

		imagecopyresampled($this->image, $image_old, $xpos, $ypos, 0, 0, $new_width, $new_height, $this->info['width'], $this->info['height']);
		imagedestroy($image_old);

		$this->info['width']  = $width;
		$this->info['height'] = $height;
	}

	public function resizewidth($width = 0, $height = 0, $default = '') {
		if (! $this->info['width'] || ! $this->info['height']) {
			return;
		}

		$photo_width  = $this->info['width'];
		$photo_height = $this->info['height'];

		$new_width  = $width;
		$new_height = (($this->info['height'] * $width) / $this->info['width']);

		$from_y  = 0;
		$from_x  = 0;
		$photo_y = $photo_height;
		$photo_x = $photo_width;

		$image_old   = $this->image;
		$this->image = imagecreatetruecolor($width, $new_height);

		if (isset($this->info['mime']) && $this->info['mime'] == 'image/png') {
			imagealphablending($this->image, false);
			imagesavealpha($this->image, true);
			$background = imagecolorallocatealpha($this->image, 255, 255, 255, 127);
			imagecolortransparent($this->image, $background);
		} else {
			$background = imagecolorallocate($this->image, 255, 255, 255);
		}

		imagefilledrectangle($this->image, 0, 0, $width, $new_height, $background);

		imagecopyresampled($this->image, $image_old, 0, 0, $from_x, $from_y, $new_width, $new_height, $photo_x, $photo_y);
		imagedestroy($image_old);

		$this->info['width']  = $width;
		$this->info['height'] = $height;
	}

	public function resizeheight($width = 0, $height = 0, $default = '') {
		if (! $this->info['width'] || ! $this->info['height']) {
			return;
		}

		$photo_width  = $this->info['width'];
		$photo_height = $this->info['height'];

		$new_width  = (($this->info['width'] * $height) / $this->info['height']);
		$new_height = $height;

		$from_y  = 0;
		$from_x  = 0;
		$photo_y = $photo_height;
		$photo_x = $photo_width;

		$image_old   = $this->image;
		$this->image = imagecreatetruecolor($new_width, $new_height);

		if (isset($this->info['mime']) && $this->info['mime'] == 'image/png') {
			imagealphablending($this->image, false);
			imagesavealpha($this->image, true);
			$background = imagecolorallocatealpha($this->image, 255, 255, 255, 127);
			imagecolortransparent($this->image, $background);
		} else {
			$background = imagecolorallocate($this->image, 255, 255, 255);
		}

		imagefilledrectangle($this->image, 0, 0, $width, $new_height, $background);

		imagecopyresampled($this->image, $image_old, 0, 0, $from_x, $from_y, $new_width, $new_height, $photo_x, $photo_y);
		imagedestroy($image_old);

		$this->info['width']  = $width;
		$this->info['height'] = $height;
	}

	public function cropsize($width = 0, $height = 0) {
		if (! $this->info['width'] || ! $this->info['height']) {
			return;
		}

		$photo_width  = $this->info['width'];
		$photo_height = $this->info['height'];

		$new_width  = $width;
		$new_height = $height;

		if (($photo_width / $new_width) < ($photo_height / $new_height)) {
			$from_y  = ceil(($photo_height - ($new_height * $photo_width / $new_width)) / 2);
			$from_x  = '0';
			$photo_y = ceil(($new_height * $photo_width / $new_width));
			$photo_x = $photo_width;
		}

		if (($photo_height / $new_height) < ($photo_width / $new_width)) {
			$from_x  = ceil(($photo_width - ($new_width * $photo_height / $new_height)) / 2);
			$from_y  = '0';
			$photo_x = ceil(($new_width * $photo_height / $new_height));
			$photo_y = $photo_height;
		}

		if (($photo_width / $new_width) == ($photo_height / $new_height)) {
			$from_x  = ceil(($photo_width - ($new_width * $photo_height / $new_height)) / 2);
			$from_y  = '0';
			$photo_x = ceil(($new_width * $photo_height / $new_height));
			$photo_y = $photo_height;
		}

		$image_old   = $this->image;
		$this->image = imagecreatetruecolor($width, $height);

		if (isset($this->info['mime']) && $this->info['mime'] == 'image/png') {
			imagealphablending($this->image, false);
			imagesavealpha($this->image, true);
			$background = imagecolorallocatealpha($this->image, 255, 255, 255, 127);
			imagecolortransparent($this->image, $background);
		} else {
			$background = imagecolorallocate($this->image, 255, 255, 255);
		}

		imagefilledrectangle($this->image, 0, 0, $width, $height, $background);

		imagecopyresampled($this->image, $image_old, 0, 0, $from_x, $from_y, $new_width, $new_height, $photo_x, $photo_y);
		imagedestroy($image_old);

		$this->info['width']  = $width;
		$this->info['height'] = $height;
	}

	public function onesize($maxsize = 0) {
		if (! $this->info['width'] || ! $this->info['height']) {
			return;
		}

		$photo_width  = $this->info['width'];
		$photo_height = $this->info['height'];

		if ($photo_width > $maxsize or $photo_height > $maxsize) {
			if ($photo_width == $photo_height) {
				$width  = $maxsize;
				$height = $maxsize;
			} elseif ($photo_width > $photo_height) {
				$scale  = $photo_width / $maxsize;
				$width  = $maxsize;
				$height = round($photo_height / $scale);
			} else {
				$scale  = $photo_height / $maxsize;
				$height = $maxsize;
				$width  = round($photo_width / $scale);
			}
		} else {
			$width  = $photo_width;
			$height = $photo_height;
		}

		$image_old   = $this->image;
		$this->image = imagecreatetruecolor($width, $height);

		if (isset($this->info['mime']) && $this->info['mime'] == 'image/png') {
			imagealphablending($this->image, false);
			imagesavealpha($this->image, true);
			$background = imagecolorallocatealpha($this->image, 255, 255, 255, 127);
			imagecolortransparent($this->image, $background);
		} else {
			$background = imagecolorallocate($this->image, 255, 255, 255);
		}

		imagefilledrectangle($this->image, 0, 0, $width, $height, $background);

		imagecopyresampled($this->image, $image_old, 0, 0, 0, 0, $width, $height, $photo_width, $photo_height);
		imagedestroy($image_old);

		$this->info['width']  = $width;
		$this->info['height'] = $height;
	}

	public function crop($top_x, $top_y, $bottom_x, $bottom_y) {
		$image_old   = $this->image;
		$this->image = imagecreatetruecolor($bottom_x - $top_x, $bottom_y - $top_y);

		imagecopy($this->image, $image_old, 0, 0, $top_x, $top_y, $this->info['width'], $this->info['height']);
		imagedestroy($image_old);

		$this->info['width']  = $bottom_x - $top_x;
		$this->info['height'] = $bottom_y - $top_y;
	}

	public function rotate($degree, $color = 'FFFFFF') {
		$rgb = $this->html2rgb($color);

		$this->image = imagerotate($this->image, $degree, imagecolorallocate($this->image, $rgb[0], $rgb[1], $rgb[2]));

		$this->info['width']  = imagesx($this->image);
		$this->info['height'] = imagesy($this->image);
	}

	private function filter($filter) {
		imagefilter($this->image, $filter);
	}

	private function merge($file, $x = 0, $y = 0, $opacity = 100) {
		$merge = $this->create($file);

		$merge_width  = imagesx($merge);
		$merge_height = imagesy($merge);

		imagecopymerge($this->image, $merge, $x, $y, 0, 0, $merge_width, $merge_height, $opacity);
	}

	private function html2rgb($color) {
		if ($color[0] == '#') {
			$color = substr($color, 1);
		}

		if (strlen($color) == 6) {
			list($r, $g, $b) = [$color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]];
		} elseif (strlen($color) == 3) {
			list($r, $g, $b) = [$color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]];
		} else {
			return [];
		}

		$r = hexdec($r);
		$g = hexdec($g);
		$b = hexdec($b);

		return [$r, $g, $b];
	}
}
