<?php
/**
 * Main functional file
 *
 */
/**
 * main class handles all required functionalities
 */
class mainScraper {
	/**
	 * Constuctor
	 * @param string $url
	 */
	public function __construct($url)
	{
		$this->url = $url;
	}
	/**
	 * main function extracts url data and returns json array
	 * @return json array
	 */
	public function getData()
	{
		$results = array();
		$total = 0;
		
		$htmlMain = $this->getHtml($this->url);
		
		$doc = new DOMDocument();
		@$doc->loadHTML($htmlMain['result']);
		$xpath = new DOMXpath($doc);
		$products = $xpath->query('//div[@class="productInfo"]');		
		
		foreach($products as $entry) {
			$htmlLinks = $entry->getElementsByTagName("a");
			foreach($htmlLinks as $item) {				
				$href = $item->getAttribute("href");				
				$link = trim(preg_replace("/[\r\n]+/", " ", $item->nodeValue));		

				$html = $this->getHtml($href);
				$doc = new DOMDocument();
				@$doc->loadHTML($html['result']);
				$xpath = new DOMXpath($doc);
				$productUnitPrice = $xpath->query('//p[@class="pricePerUnit"]');
				foreach ($productUnitPrice as $itemPrice ) {
					$unitPrice = preg_replace('/[^0-9.]*/','',$itemPrice->nodeValue);
					break;
				}
				
				$productText = $xpath->query('//div[@class="productText"]');
				foreach ($productText as $itemText ) {
					$description = trim($itemText->nodeValue);
					break;
				}
				
				$results['result'][] = array('title' => $link,'size' => $html['size'],
						'unit_price' => $unitPrice, 'description' => $description);
				
				$total += $unitPrice;				
			}
			
		}		
		$results['total'] = $total;
		
		return json_encode($results);
	}
	/**
	 * creates curl and returns html from the given url
	 * @param string $url
	 * @return string[]|mixed[]
	 */
	private function getHtml($url)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		
		$userAgent = 'Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';		
		//$cookieFileLocation = dirname(__FILE__).'/cookie.txt';
		
		curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);		
			
		curl_setopt( $curl, CURLOPT_COOKIESESSION, true );
		//curl_setopt($curl, CURLOPT_COOKIEJAR, $cookieFileLocation);
		//curl_setopt($curl, CURLOPT_COOKIEFILE, $cookieFileLocation);
		
		$result = curl_exec($curl);
		if(!$result)
		{			
			echo "ERRNO: ".curl_errno($curl);
			echo "ERR: ".curl_error($curl);
			exit;
		}
		
		$size = round(curl_getinfo($curl,CURLINFO_SIZE_DOWNLOAD)/1024 , 2) . "kb";
		
		curl_close($curl);
		
		return array('result' => $result, 'size' => $size);
	}
}
