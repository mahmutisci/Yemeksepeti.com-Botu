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
	private $comments;

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
		$this->comments["name"] = [];
		$this->comments["comment"] = [];
		for ($i = $startpage; $i <= $maxpage; $i++){
			$this->content = $this->curl($this->url."?page=".$i);
			preg_match_all("/<div class=\"comments-body\">.*?<p>(.*?)<\/p>/", $this->content, $comment);
			preg_match_all("/<div class=\"userName col-md-3\">.*?<div>(.*?)<\/div>/",$this->content, $name);
			$this->comments["name"] = array_merge($this->comments["name"], $name[1]);
			$this->comments["comment"] = array_merge($this->comments["comment"], $comment[1]);
		}
		return $this->comments;
	}
}
