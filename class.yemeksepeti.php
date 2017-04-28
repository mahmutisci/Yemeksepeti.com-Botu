<?php

/**
 * Class yemeksepeti
 *
 * @author Mahmut İŞÇİ
 *         @web http://www.mahmutisci.com
 *         @mail mahmutisci@outlook.com
 *         @date 17.04.2017
 */
class yemeksepeti{
	private $url;
	private $content;

	/**
	 * @param $url
	 * @return mixed
	 */
	public function curl($url){
		$curl=curl_init();
		curl_setopt($curl,CURLOPT_URL, $url);
		curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl,CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
		$cikti = curl_exec($curl);
		curl_close($curl);
		return str_replace(array("\n","\t","\r"), null, $cikti);
	}

	/**
	 * yemeksepeti constructor.
	 * @param $restaurant
	 * @param $city
	 * @param $district
	 */
	public function __construct($restaurant, $city, $district)
	{
		$this->url = "https://www.yemeksepeti.com/".$restaurant."-".$district."-".$city;
	}

	/**
	 * @param int $startpage
	 * @param int $maxpage
	 * @return mixed
	 */
	public function getComments($startpage =1, $maxpage = 1){
		$items = array();
		for ($i = $startpage; $i <= $maxpage; $i++){
			$this->content = $this->curl($this->url."?page=".$i);
			preg_match_all("/<div class=\"comments-body\">.*?<p>(.*?)<\/p>/", $this->content, $comments);
			preg_match_all("/<div class=\"userName col-md-3\">.*?<div>(.*?)<\/div>/",$this->content, $names);
			foreach ($comments[1] as $index => $comment){
				$items[] = [
					"name" => $names[1][$index],
					"comment" => $comment
				];
			}
		}
		return $items;
	}

	/**
	 * @param string $type
	 */
	public function getMenu($type = "all"){
		$items = array();
		$content = $this->curl($this->url);
		preg_match_all("/<div class=\"restaurantDetailBox None \" id=\".*?\">(.*?)<\/div>/",$content, $menu);
		foreach ($menu[1] as $listTable){
			preg_match("/<h2><b>(.*?)<\/b><\/h2>/", $listTable, $listName);
			$products = array();
			preg_match_all("/<li>(.*?)<\/li>/", $listTable, $listUl);
			foreach ($listUl[1] as $list){
				preg_match("/<div class=\"productName\"><a .*?>(.*?)<\/a><\/div>/", $list, $productName);
				preg_match("/<span class=\"productInfo\"><p>(.*?)<\/p><\/span>/", $list, $productInfo);
				preg_match("/<span class=\"pull-right newPrice\">(.*?)<\/span>/", $list, $productPrice);
				$products[] = [
					"name" => $productName,
					"info" => $productInfo,
					"price" => $productPrice
				];
			}
			$items[] = [
				"listName" => $listName,
				"products" => $products
			];
		}
	}
}
