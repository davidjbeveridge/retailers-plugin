<?
define('GMAP_URL','http://maps.google.com/maps/geo?');
define('API_KEY','ABQIAAAAz-opKYpiiYYLzqeDDr3Z-RRXj4DFLhh8iI3qAITB6ya-xdk5RhR0J5t89CdOZ514miOHdobwR6pfZA');
define('GHW_ADDRESS','2613 8th Ave, Greeley, CO 80631, USA');

header('Content-Type: text/javascript');

$db = mysql_connect('localhost','hatsboot_dbadmin','Hatman911');
mysql_query('USE `hatsboot_Site`;',$db);

function geoCode($address=null)	{
	if(is_null($address))	{return 0;}
	$url = GMAP_URL."output=xml&key=".API_KEY."&q=".urlencode($address);
	$file = utf8_encode(file_get_contents($url));
	$xml = simplexml_load_string($file);
	if(!$xml)	{return -1;}
	$status = intval($xml->Response->Status->code);
	if($status != 200)	{
		return $status;
	}
	$output = array();
	$output['address'] = '';
	foreach($xml->Response->Placemark->address as $address)	{
	$output['address'] .= $address;
	}
	$coords = split(',',$xml->Response->Placemark->Point->coordinates);
	$output['lon'] = $coords[0];
	$output['lat'] = $coords[1];
	
	return $output;
}

function getRetailers($limit=null,$address=null){
	// $limit is the number of retailers to return;
	// $address is the address from which to calculate proximity;
	
	global $db;
	
	$limitstring = is_null($limit) ? '' : "LIMIT 0,$limit";
	if(is_null($address))	{
		$address = GHW_ADDRESS;
	}
	if(!$address)	{return 0;}
	$startpoint = geoCode($address);
	if(!$startpoint)	{return 0;}
	$lat = $startpoint['lat'];
	$lon = $startpoint['lon'];
	$query =	"SELECT `id`,`name`,`address`,`phone`,`email`,`website`,`lat`,`lon`,
				ROUND(((ACOS(SIN($lat * PI() / 180) * SIN(`lat` * PI() / 180) + COS($lat * PI() / 180) * COS(`lat` * PI() / 180) * COS(($lon - `lon`) * PI() / 180)) * 180 / PI()) * 60 * 1.1515),3) AS `distance`
				FROM `retailers`
				ORDER BY distance ASC
				$limitstring;";
	$result = mysql_query($query,$db);
	if(!mysql_num_rows($result))	{return 0;}
	//$r = $result->result_array();
	//print_r($r);
	while($row = mysql_fetch_assoc($result))	{
		$retailers[] = $row;
	}
	return $retailers;
}

$limit = (isset($_REQUEST['limit']) && !empty($_REQUEST['limit'])) ? $_REQUEST['limit'] : null;
$address = (isset($_REQUEST['address']) && !empty($_REQUEST['address'])) ? $_REQUEST['address'] : GHW_ADDRESS;

$output['retailers'] = getRetailers($limit,$address);
$output['center'] = geoCode($address);
echo json_encode($output);
