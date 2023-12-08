<?php
class Cake {

	public function __construct(Advertiser $account) {
		$this->account = $account;
	}

	public function get_offers() {

		$url = "https://network.neverblue.com/affiliates/api/4/offers.asmx/OfferFeed";
		$curl = new Curl();

		$params = array(
			'api_key' => $this->account->platform_api,
			'affiliate_id' => $this->account->platform_pubid,
			'campaign_name' => '',
			'media_type_category_id' => 0,
			'vertical_category_id' => 0,
			'vertical_id' => 0,
			'offer_status_id' => 0,
			'tag_id' => 0,
			'start_at_row' => 1,
			'row_limit' => 10,
		);

		$results = $curl->get($url, $params);
		$results = new SimpleXMLElement($results->body);
		$results = $results->offers->offer;

		$offers = array();
		foreach ($results as $offer) {
			$tmp = array();
			$tmp['user_id'] = $this->account->user_id;
			$tmp['advertiser_id'] = $this->account->id;
			$tmp['xoffer_id'] = (string) $offer->offer_id;
			$tmp['name'] = (string) $offer->offer_name;
			$tmp['revenue'] = substr(utf8_decode((string) $offer->payout), 1);
			$tmp['revenue_type'] = (string) $offer->price_format;

			$status_name = (string) $offer->offer_status->status_name;

			$status = 0;
			switch ($status_name) {
			case 'Active':
			case 'Public':
			case 'Pending':
				$status = 1;
				break;
			}

			$tmp['status'] = $status;
			$tmp['description'] = (string) $offer->description;
			$tmp['restrictions'] = (string) $offer->restrictions;
			$tmp['url'] = (string) $offer->unique_link;
			$tmp['preview_url'] = (string) $offer->preview_link;
			$tmp['thumb_url'] = (string) $offer->thumbnail_image_url;

			print_rf($offer);

			$countries = array();
			foreach ($offer->allowed_countries->country as $country) {
				$countries[] = (string) $country->country_code;
			}

			$tmp['countries'] = implode(",", $countries);

			$media = array();
			foreach ($offer->allowed_media_types->media_type as $media_type) {
				$media[] = (string) $media_type->type_name;
			}

			$tmp['media_types'] = implode(",", $media);
			$offers[] = $tmp;
		}

		print_rf($offers);die;

	}

}
?>