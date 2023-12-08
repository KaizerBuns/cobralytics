<table class="table">
	<tr>
		<th>Tracking Links <small> - Generated using tracking domains</small></th>
	</tr>
	<?
			foreach($campaign->domains as $tracking_domain) {
				$link = strtolower($tracking_domain->source_name.'/?t='.$campaign->linkhash);
				if(preg_match("/^e\./", $link)) {
					echo "<tr><td><code>https://$link</code></td></tr>";
				} else {
					echo "<tr><td><code>http://$link</code></td></tr>";
				}
			}

			if($user->is_admin()) {
				$link = strtolower('https://e.cobralytics.com/?t='.$campaign->linkhash);
				echo "<tr><td><code>$link</code></td></tr>";	
			}
		?>	
</table>
<table class="table">
	<tr>
		<th>iFrame Pixels <small> - Cookie based pixel conversion - Used on pages you have access to the thank you page and the cobralytics clickid cookie will fire.</small></th>
	</tr>
		<?
		  	foreach($campaign->domains as $tracking_domain) {
		  		$link = strtolower($tracking_domain->source_name);
		  		if(preg_match("/^e\./", $link)) {
		  			$code = "<iframe src='https://" . $link. "/fpx' width='0' height='0' style='display:none'></iframe>";
		  		} else {
		  			$code = "<iframe src='http://" . $link. "/fpx' width='0' height='0' style='display:none'></iframe>";
		  		}
		  		echo "<tr><td><code style='font-size:11px'>".htmlentities($code)."</code></td></tr>";
		  	}
	  		if($user->is_admin()) {
				$link = strtolower('https://e.cobralytics.com/fpx');
				$code = "<iframe src='" . $link. "' width='0' height='0' style='display:none'></iframe>";
				echo "<tr><td><code style='font-size:11px'>".htmlentities($code)."</code></td></tr>";
			}
		  	?>
</table>
<table class="table">
	<tr>
		<th>S2S Pixels <small> - Server2Server pixel conversion - Used on networks that can fire back a value e.g {PARAM}. On the offer url you will send the {CLICKID} and have the network fire back the {CLICKID} on conversion</small></th>
	</tr>
		<?
		  	foreach($campaign->domains as $tracking_domain) {
		  		$link = strtolower($tracking_domain->source_name);
		  		if(preg_match("/^e\./", $link)) {
		  			$code = "https://" . $link. "/fpx?cobraCID={PARAM}";
		  		} else {
		  			$code = "http://" . $link. "/fpx?cobraCID={PARAM}";
		  		}
		  		echo "<tr><td><code style='font-size:11px'>".htmlentities($code)."</code></td></tr>";
		  	} 
	  		if($user->is_admin()) {
				$link = strtolower('https://e.cobralytics.com/fpx')."?cobraCID={PARAM}";
				$code = $link;
				echo "<tr><td><code style='font-size:11px'>".htmlentities($code)."</code></td></tr>";
			}
		?>
</table>
<table class="table">
	<tr> 
		<th>URL Parameters</th>
	</tr>
	<tr>
		<td>
			<p>subid={YOURSUB}</p>
			<p><i class="fa fa-info-circle"></i> Use at the end of your url tracking link</p>
		</td>
	</tr>
</table>