<?php
class DBC_Retailers_Handler	{
	const GMAP_URL = 'http://maps.google.com/maps/geo?';
	public function __construct()	{}
	public function createRetailer($retailer)	{}
	public function readRetailers($location=NULL,$specialOrder=NULL)	{}
	public function updateRetailer($retailer)	{}
	public function deleteRetailer($retailerID)	{}

	static function geoCode($location)	{
		
		$API_KEY = get_option('dbc_googlemaps_api_key',FALSE);
		if(empty($API_KEY))	{
			throw new Exception('Google Maps API Key not configured.');
		}
		if(is_null($location))	{
			throw new Exception('No location given.');
		}
		$url = self::GMAP_URL."output=xml&key=".API_KEY."&q=".urlencode($location);
		$file = utf8_encode(file_get_contents($url));
		$xml = simplexml_load_string($file);
		if(!$xml)	{return -1;}
		$status = intval($xml->Response->Status->code);
		if($status != 200)	{
			return $status;
		}
		$output = array();
		$output['address'] = '';
		foreach($xml->Response->Placemark->address as $location)	{
		$output['address'] .= $location;
		}
		$coords = split(',',$xml->Response->Placemark->Point->coordinates);
		$output['lon'] = $coords[0];
		$output['lat'] = $coords[1];
		
		return $output;
	}
	
	static function url_get_contents($url)	{
		if(in_array('curl',get_loaded_extensions()))	{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_URL, $url);
			$data = curl_exec($ch);
			curl_close($ch);
			return $data;
		}
		// FIXME Finish the following clause:
		elseif(function_exists('fsockopen'))	{
			$url = (object) parse_url($url);
			if($url)	{
				if(isset($))
			}
		}
		elseif($fwrappers = get_ini(allow_url_fopen) && strcasecmp($fwrappers,'off'))	{
			$data = file_get_contents($url);
			return $data;
		}
	}
}