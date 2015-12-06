<?php

require_once 'mainScraper.php';
/**
 * 
 * @author 
 *
 */
class testMainScraper extends \PHPUnit_Framework_TestCase
{
	/**
	 * Test function checks the returned total and
	 * results price total are same. 
	 */
	public function testgetdata(){
		
		$testData = new mainScraper("http://hiring-tests.s3-website-eu-west-1.amazonaws.com/2015_Developer_Scrape/5_products.html");
		
		$json = $testData->getData();
		
		$testResult=json_decode($json,true);
		
		$results=$testResult['result'];
		
		$itemTotal=$testResult['total'];
		
		$total=0;
		
		foreach ($results as $item) {
			
			$total += $item['unit_price'];
			echo $total;
		}
		/**
		 * Assert here if the data is equal.
		 */		
		$this->assertEquals($total,$itemTotal);
		
	}
	
}
?>
