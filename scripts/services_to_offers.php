<?
chdir(dirname(__FILE__));
require_once('../config/config.php');
require_once(ROOT_PATH.'/config/includes.php');
require_once(ROOT_PATH.'/system/utils/functions.php');

die("No longer needed\n");
$services = Service::find_all();
foreach($services as $service) {

	$new_offer = new Offer();
	$new_offer->name = "AUTO - ".$service->name;
	$new_offer->user_id = $service->user_id;
	$new_offer->save();

	echo "Creating new Offer -> {$new_offer->id}\n";

	$rules = Rule::find_all("rule_type = 'service' AND rule_type_id = '{$service->id}'");
	foreach($rules as $rule) {
		//load offer
		if($rule->type == 'offer') {
			if($offer = Offer::find(array('id' => $rule->offer_id))) {
				echo "Found Offer -> {$offer->id}\n";
				$url = $offer->url;
			
				$landingpage = $rule;
				unset($landingpage->id);
				$landingpage->new_record = true;
				$landingpage->url = $url;
				$landingpage->rule_type = 'offer';
				$landingpage->rule_type_id = $new_offer->id;
				$landingpage->type = 'landingpage';
				$landingpage->offer_id = 0;

				if($landingpage->save()) {
					echo "Saved - {$landingpage->id}\n";
					//$rule->delete();
					//$offer->delete();
				} else {
					echo "Failed to save LP\n";
				}
			}
		}
	}
	//$service->delete();
}

echo "Completed\n";
?>