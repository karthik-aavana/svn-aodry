<?php
class Model_tool_image extends CI_model {

	public function resize($filename, $width, $height) {
		$dir_image = str_replace("\\","/", DIR_IMAGE);
		if (!is_file($dir_image . $filename) || substr(str_replace('\\', '/', realpath($dir_image . $filename)), 0, strlen($dir_image)) != $dir_image) {
			return;
		}

		$extension = pathinfo($filename, PATHINFO_EXTENSION);

		$image_old = $filename;
		$image_new = 'cache/' . substr($filename, 0, strrpos($filename, '.')) . '-' . $width . 'x' . $height . '.' . $extension;

		if (!is_file($dir_image . $image_new) || (filectime($dir_image . $image_old) > filectime($dir_image . $image_new))) {
			list($width_orig, $height_orig, $image_type) = getimagesize($dir_image . $image_old);
				 
			if (!in_array($image_type, array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF))) { 
				return $dir_image . $image_old;
			}
 
			$path = '';

			$directories = explode('/', dirname($image_new));

			foreach ($directories as $directory) {
				$path = $path . '/' . $directory;

				if (!is_dir($dir_image . $path)) {
					@mkdir($dir_image . $path, 0777);
				}
			}

			if ($width_orig != $width || $height_orig != $height) {
				$image = new Image($dir_image . $image_old);
				$image->resize($width, $height);
				$image->save($dir_image . $image_new);
			} else {
				copy($dir_image . $image_old, $dir_image . $image_new);
			}
		}


			return site_url(). 'assets/images/' . $image_new;
		
	}
}
