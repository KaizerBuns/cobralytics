<?php
class UploadAssetHelper extends AppController {
	public function copy_to_storage($asset_id, $files = array()) {
		$time = time();
		$new_files = array();

		foreach ($files as $file) {
			if (empty($file)) {
				continue;
			}

			$tmp = array();
			if (file_exists(APP_TMP_PATH . "/{$file}")) {
				$from = APP_TMP_PATH . "/{$file}";
				$to = APP_STORAGE_PATH . "/{$asset_id}_" . $this->format_file($file);
				if (@copy($from, $to)) {
					$tmp = array(
						'name' => "{$asset_id}_" . $this->format_file($file),
						'url' => APP_STORAGE_URL . "/{$asset_id}_" . $this->format_file($file),
						'size' => filesize($to),
						'ext' => pathinfo($to, PATHINFO_EXTENSION),
						'original' => $file,
						'small' => '',
						'thumb' => '',
						'medium' => '',
					);
					@unlink($from);
				}
			}

			if (file_exists(APP_TMP_MED_PATH . "/{$file}")) {
				$from = APP_TMP_MED_PATH . "/{$file}";
				$to = APP_STORAGE_PATH . "/med_{$asset_id}_" . $this->format_file($file);
				if (@copy($from, $to)) {
					$tmp['medium'] = APP_STORAGE_URL . "/med_{$asset_id}_" . $this->format_file($file);
					@unlink($from);
				}
			}

			if (file_exists(APP_TMP_SM_PATH . "/{$file}")) {
				$from = APP_TMP_SM_PATH . "/{$file}";
				$to = APP_STORAGE_PATH . "/sm_{$asset_id}_" . $this->format_file($file);
				if (@copy($from, $to)) {
					$tmp['small'] = APP_STORAGE_URL . "/sm_{$asset_id}_" . $this->format_file($file);
					@unlink($from);
				}
			}

			if (file_exists(APP_TMP_PROFILE_PATH . "/{$file}")) {
				$from = APP_TMP_PROFILE_PATH . "/{$file}";
				$to = APP_STORAGE_PATH . "/profile_{$asset_id}_" . $this->format_file($file);
				if (@copy($from, $to)) {
					$tmp['profile'] = APP_STORAGE_URL . "/profile_{$asset_id}_" . $this->format_file($file);
					@unlink($from);
				}
			}

			if (file_exists(APP_TMP_THUMB_PATH . "/{$file}")) {
				$from = APP_TMP_THUMB_PATH . "/{$file}";
				$to = APP_STORAGE_PATH . "/thumb_{$asset_id}_" . $this->format_file($file);
				if (@copy($from, $to)) {
					$tmp['thumb'] = APP_STORAGE_URL . "/thumb_{$asset_id}_" . $this->format_file($file);
					@unlink($from);
				}
			}

			$new_files[] = $tmp;
		}
		return $new_files;
	}

	public function save_uploaded($user_id, $uploaded, $object_id, $object_type = 'item') {
		if ($files = $this->copy_to_storage($object_id, $uploaded)) {
			foreach ($files as $key => $file) {
				$params = array();
				$params['file_name'] = $file['name'];
				$params['file_url'] = $file['url'];
				$params['file_sm_url'] = $file['small'];
				$params['file_med_url'] = $file['medium'];
				$params['file_profile_url'] = $file['profile'];
				$params['file_thumb_url'] = $file['thumb'];
				$params['file_type'] = (preg_match("/gif|jpe?g|png/", $file['ext']) ? 'image' : 'document');
				$params['file_ext'] = $file['ext'];
				$params['file_size'] = $file['size'];
				$params['type'] = $object_type;
				$params['item_id'] = $object_id;
				$params['file_order'] = 0;
				$asset = new Asset();
				if (!$asset->create_asset($user_id, $params)) {
					$this->remove_tmp_files($uploaded);
					$this->render_errordb($asset->db_errors);
				}
			}
		}
	}

	public function reset_fileorder($user_id, $item_id, $type = 'item') {
		$sql = "UPDATE bv_assets SET file_order = 0 WHERE user_id = {$user_id} AND type = '{$type}' AND item_id = {$item_id}";
		DbRecord::query($sql, array('connection' => 'default'));
	}

	public function set_fileorder($user_id, $item_id, $type, $order) {
		$sql = "SELECT * FROM bv_assets WHERE user_id = {$user_id} AND type = '{$type}' AND item_id = {$item_id}";
		$assets = Asset::find_by_sql($sql);

		//update main image
		if (isset($assets[$order]) && $assets[$order]->file_type == 'image') {
			//save new main image
			$asset = $assets[$order];
			$asset->file_order = 1;
			$asset->save();
		}
	}

	public function format_file($file_name) {
		return strtolower(str_replace(" ", "_", $file_name));
	}

	public function remove_tmp_files(Array $files) {
		foreach ($files as $file) {
			if (file_exists(APP_TMP_PATH . "/{$file}")) {
				@unlink(APP_TMP_PATH . "/{$file}");
			}

			if (file_exists(APP_TMP_THUMB_PATH . "/{$file}")) {
				@unlink(APP_TMP_THUMB_PATH . "/{$file}");
			}

			if (file_exists(APP_TMP_SM_PATH . "/{$file}")) {
				@unlink(APP_TMP_SM_PATH . "/{$file}");
			}

			if (file_exists(APP_TMP_MED_PATH . "/{$file}")) {
				@unlink(APP_TMP_MED_PATH . "/{$file}");
			}
		}
	}

	public function jcrop_picture($file_path, $new_file_path, $x, $y, $w, $h, $file_size = 300) {
		$new_width = $new_height = $file_size;
		$details = getimagesize($file_path);
		$new_img = imagecreatetruecolor($new_width, $new_height);

		switch ($details['mime']) {
		case 'image/jpeg':
			$src_img = imagecreatefromjpeg($file_path);
			$write_image = 'imagejpeg';
			$image_quality = 90;
			break;
		case 'image/gif':
			imagecolortransparent($new_img, imagecolorallocate($new_img, 0, 0, 0));
			$src_img = imagecreatefromgif($file_path);
			$write_image = 'imagegif';
			$image_quality = null;
			break;
		case 'image/png':
			imagecolortransparent($new_img, imagecolorallocate($new_img, 0, 0, 0));
			imagealphablending($new_img, false);
			imagesavealpha($new_img, true);
			$src_img = imagecreatefrompng($file_path);
			$write_image = 'imagepng';
			$image_quality = 9;
			break;
		}

		$success = imagecopyresampled(
			$new_img,
			$src_img,
			0,
			0,
			(int) $x,
			(int) $y,
			$new_width,
			$new_height,
			(int) $w,
			(int) $h
		) && $write_image($new_img, $new_file_path, $image_quality);

		// Free up memory (imagedestroy does not delete files):
		imagedestroy($src_img);
		imagedestroy($new_img);

		return $success;
	}
}
?>