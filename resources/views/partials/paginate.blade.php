<?
	

	$url_page = App\Helpers\MyHelper::page_url();
	
	$prev_class = '';
	$next_class = '';

	$prev_page = $page_number - 1;
	$next_page = $page_number + 1;

	if($page_number < 5) {
		$start = 1;
		$end = 5;
	} else {
		$start = $page_number - 2;
		$end = $page_number + 2;
	}
	
	$prev_link = "{$url_page}&page={$prev_page}";
	$next_link = "{$url_page}&page={$next_page}";

	if($prev_page < 1) {
		$prev_class = 'disabled';
		$prev_link  = '';
	}
?>
<ul class="pagination">
	<li class="<?=$prev_class?>"><a href="<?=$prev_link?>">«</a></li>
	<?for($page=$start;$page<=$end;$page++) { ?>
		<li class="<?=$page == $page_number ? 'active' : ''?>"><a href="<?=$url_page?>&page=<?=$page?>"><?=$page?></a></li>
	<?}?>
	<li><a href="<?=$next_link?>">»</a></li>
</ul>