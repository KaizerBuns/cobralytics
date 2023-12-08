<?php namespace App\Http\Controllers\Member;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MemberController;

use App\Http\Modals;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;

use App\Creative;
use App\Helpers\TableMap;
use App\Helpers\MyHelper;

class CreativeController extends SourceController {
	
	public function index() {
		$view = $this->request->input('view');
		switch($view) {
			case 'new':
				return $this->create();
				break;
			default:
				return $this->$view();
				break;
		}
	}

	public function create() {
		$header = array(
			'icon' => '<i class="fa fa-eye-slash"></i>',
			'title' => 'Creatives',
			'desc' => 'New Creative'
		);

		return view('member.creative.new', ['header' => $header ]);
	}

	public function preview() {
		$header = array(
			'icon' => '<i class="fa fa-eye-slash"></i>',
			'title' => 'Creatives',
			'desc' => 'Preview'
		);

		$creative = Creative::find($this->request->input('id'));
		return view('member.creative.preview', ['header' => $header, 'image' => $creative->getPublicUrl()]);
	}

	public function save() {
		\Image::configure(array('driver' => 'imagick'));

		$files = $this->request->file('fileupload');
		foreach($files as $file) 
		{
			list($width, $height) = getimagesize($file);

			$type = $this->request->input('creative')['type'];
			switch($type) {
				case "image":
					$folder = 'creatives';
					break;
				default:
					$folder = 'templates';
					break;
			}

			$extension = $file->getClientOriginalExtension();
			$hash = date("YmdHis").rand(0,1000);

			$data = array(
				'name' => "creative_{$type}_{$width}x{$height}_{$hash}.{$extension}",
				'thumb' => "creative_{$type}_{$width}x{$height}_{$hash}_thumb.{$extension}",
				'storage' => env('APP_STORAGE'),
				'file_bucket' => 'cobralytics',
				'file_folder' => $folder,
				'file_size' => $file->getSize(),
				'file_type' => $file->getMimetype(),
				'file_width' => $width,
				'file_height' => $height
			);

			if(env('APP_STORAGE') == 'local') {
				$data['file_url'] = env('APP_URL').'/'.$data['file_folder'];
			} else {
				$data['file_url'] = 'https://s3-us-west-2.amazonaws.com/'.$data['file_bucket'].'/'.$data['file_folder'];
			}

			//storage save
			$thumb = \Image::make($file)->resize(100, 100)->encode($file->getMimetype());
			$file = \Image::make($file)->resize($width, $height)->encode($file->getMimetype());
	
			\Storage::disk(env('APP_STORAGE'))->put($folder.'/'.$data['name'], $file->__toString());
			\Storage::disk(env('APP_STORAGE'))->put($folder.'/'.$data['thumb'], $thumb->__toString());
			
			//Db save
			Creative::save_creative($this->user, $data);
		}

		return redirect('/member/creative/?view=manage&msg=saved');
	}

	public function delete() {
		$creative = Creative::find($this->request->input('id'));
		if(!$creative->is_owner($this->user)) {
			$this->redirect('/member/creative/?view=manage&msg=notfound');
		}

		$folder = $creative->file_folder;
		if (\Storage::disk(env('APP_STORAGE'))->exists($folder.'/'.$creative->name)) {
			\Storage::disk(env('APP_STORAGE'))->delete($folder.'/'.$creative->name);
			\Storage::disk(env('APP_STORAGE'))->delete($folder.'/'.$creative->thumb);
		}

		$creative->delete();
		return redirect('/member/creative/?view=manage&msg=saved');
	}

	public function manage() {
		$header = array(
			'icon' => '<i class="fa fa-eye-slash"></i>',
			'title' => 'Creatives',
			'desc' => 'Manage Creatives'
		);

		$params = array(
			'limit' => $this->user->pref_page_limit, 
			'page' => $this->page_number,
			'search' => $this->request->input('search'),
			'sort' => $this->request->input('sort'),
			'order' => $this->request->input('order')
		);

		$table['results'] = Creative::get_creatives($this->user, $params);
		$table['params'] = array(
			'table_id' => 'table-creatives',
			'action_url' => "/member/creative/?view=manage"
		);

		$table['descriptor'] = array(
			'ID' => array(
				'field' => 'id', 
				'linkto' => array('url' => '/member/creative/?view=preview&id={VALUE}', 'value_field' => 'id'),
			),
			'Name' => array(
				'field' => 'name', 
				'linkto' => array('url' => '/member/creative/?view=preview&id={VALUE}', 'value_field' => 'id'),
			),
			'Thumbnail' => array(
				'html' => array(
					'html' => "<a href='/member/creative/?view=preview&id={ID}'><img src='{THUMB}' width='100' height='100'></a>",
					'value_field' => array('ID' => 'id' ,'THUMB' => 'public_thumb_url'),
					'class' => 'col-xs-2'
				),
			),
			'Type' => array('field' => 'file_type'),
			'Width' => array('field' => 'file_width'),
			'Height' => array('field' => 'file_height'),
			'Size' => array('field' => 'file_size', 'format' => 'image_size'),
			'Created' => array('field' => 'created_at', 'format' => 'nice-date-time'),
			'Updated' => array('field' => 'updated_at', 'format' => 'nice-date-time'),
			'Actions' => array(
				'html' => array(
					'html' => "							
					<a onclick=\"bootbox.confirm('Are you sure you want to delete this Creative?', function(result) { if (result===true) { window.location.href='/member/creative/?view=delete&id={ID}'; }});\" class=\"btn btn-xs btn-danger cmd-tip\" title=\"Delete\" style=\"cursor:pointer;\">
					<i class='fa fa-trash-o'></i></a>",
					'value_field' => array('ID' => 'id'),
					'class' => 'col-xs-1'
				),
			)
		);

		$tablemap = TableMap::create($table['results'], $table['descriptor'], $table['params']);
		return view('shared.manage', ['header' => $header, 'tablemap' => $tablemap]);
	}
}
?>