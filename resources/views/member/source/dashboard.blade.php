<div class="row">
	<div class="col-xs-12" style="height:3000px">
		<iframe src="//{{ env('PIWIK_URL') }}/index.php?module=Widgetize&action=iframe&moduleToWidgetize=Dashboard&actionToWidgetize=index&idSite={{ $source->piwik_idsite }}&period=week&date=yesterday&token_auth={{ $user->piwik_auth_token }}" frameborder="0" marginheight="0" marginwidth="0" width="100%" height="100%" style="overflow-y: hidden;"></iframe>
	</div>
</div>