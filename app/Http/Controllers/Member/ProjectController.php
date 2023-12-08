<?php namespace App\Http\Controllers\Member;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MemberController;

use App\Http\Modals;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;

use App\Project;
use App\Helpers\TableMap;

class ProjectController extends MemberController {

	public function index() {
		$view = $this->request->input('view');
		switch($view) {
			case 'new':
				return $this->create();
				break;
			case 'view':
				return $this->edit();
				break;
			default:
				return $this->$view();
				break;
		}
	}

	private function create() {
		$header = array(
			'icon' => '<i class="fa fa-th"></i>',
			'title' => 'Project',
			'desc' => 'New Project'
		);

		return view('member.project.new', ['header' => $header]);
	}

	private function save() {
		$project_id = $this->request->input('project')['id'];
		$project = Project::find($project_id);
		if(is_null($project)) {
			$params = array(
				'user_id' => $this->user->id,
				'name' => $this->request->input('project')['name'],
				'description' => $this->request->input('project')['description'],
				'is_default' => 1
			);
			
			$project = Project::create($params);

			if(is_null($project)) {
				die('Saved Failed');
			}

			$this->user->default_project_id = $project->id;
			$this->user->save();
		} else {
			$project->fill($this->request->input('project'));
			$project->save();	
		}

		return redirect('/member/project/?view=manage&msg=saved');
	}

	private function edit() {
		$header = array(
			'icon' => '<i class="fa fa-th"></i>',
			'title' => 'Project',
			'desc' => 'Edit Project'
		);
		
		$project = Project::find($this->request->input('id'));
		if(!$project->is_owner($this->user)) {
			$this->redirect('/member/project/?view=manage&msg=notfound');
		}
		
		return view('member.project.new', ['header' => $header, 'project' => $project]);
	}

	private function manage() {

		$header = array(
			'icon' => '<i class="fa fa-th"></i>',
			'title' => 'Project',
			'desc' => 'Project List'
		);

		$table['results'] = Project::get_projects($this->user);

		$table['params'] = array(
			'table_id' => 'table-services',
			'action_url' => "/member/project/?view=manage"
		);

		$table['descriptor'] = array(
			'ID' => array(
				'field' => 'id', 
				'linkto' => array('url' => '/member/project/?view=edit&id={VALUE}', 'value_field' => 'id'),
			),
			'Name' => array(
				'field' => 'name', 
				'linkto' => array('url' => '/member/project/?view=set&id={VALUE}', 'value_field' => 'id'),
			),
			'Campaigns' => array('field' => 'campaign_count'),
			'Domains' => array('field' => 'domain_count'),
			'Sources' => array('field' => 'traffic_count'),
			'Created' => array('field' => 'created_at', 'format' => 'nice-date-time'),
			'Updated' => array('field' => 'updated_at', 'format' => 'nice-date-time'),
			'Actions' => array(
				'html' => array(
					'html' => "
					<a href='/member/project/?view=edit&id={ID}' title='Edit' class='btn btn-xs btn-default cmd-tip' style='cursor:pointer'><i class='fa fa-pencil'></i></a>
					<a href='/member/project/?view=set&id={ID}' title='Use this project' class='btn btn-xs btn-default cmd-tip' style='cursor:pointer'><i class='fa fa-check-square-o'></i></a>
					",
					'value_field' => array('ID' => 'id'),
					'class' => 'col-xs-1'
				),
			)
		);

		$tablemap = TableMap::create($table['results'], $table['descriptor'], $table['params']);
		return view('shared.manage', ['header' => $header, 'tablemap' => $tablemap]);
	}

	public function set() {
		$this->user->set_default_project($this->request->input('project_id'));
		if(isset($_SERVER['HTTP_REFERER'])) {
			$tmp = parse_url($_SERVER['HTTP_REFERER']);

			$url = '';
			if(isset($tmp['path'])) {
				$url .= $tmp['path'];
			}
			if(isset($tmp['query'])) {
				$url .= '?'.$tmp['query'];	
			}

			return redirect($url);	
		} else {
			return redirect('/member/project/?view=manage&msg=setproject');	
		}
	}
}
?>