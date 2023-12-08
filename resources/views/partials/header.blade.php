<?
$show_header = true;
if(!isset($header)) {
	$header = array(
		'title' => ucfirst($app['action_name']),
		'desc' => ''
	);
}

if(isset($this->header['show_header']) && $this->header['show_header'] == false) {
	$show_header = false;
}
?>
@if($show_header)
<div id="page-title">
    <h1 class="page-header text-overflow">{!! $header['icon'] or '' !!} {{ $header['title'] }}</h1>
    <!-- SEARCHBOX -->
    <div class="searchbox">
        <div class="input-group custom-search-form">
            <input type="text" class="form-control" placeholder="Search.." />
            <span class="input-group-btn">
                <button class="text-muted" type="button">
                    <i class="fa fa-search"></i>
                </button>
            </span>
        </div>
    </div>
</div>
@endif