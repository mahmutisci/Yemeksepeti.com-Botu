<?php
require_once("class.simpledom.php");
/**
 * Class yemeksepeti
 *
 * @author Mahmut İŞÇİ
 *         @web http://www.mahmutisci.com
 *         @mail mahmutisci@outlook.com
 *         @date 17.04.2017
 */
class yemeksepeti extends simple_html_dom{
	private $url;
	private $content;
	private $dom;
	/**
	 * @param $url
	 * @return mixed
	 */
	public function curl($url){
		$curl=curl_init();
		curl_setopt($curl,CURLOPT_URL, $url);
		curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl,CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
		$output = curl_exec($curl);
		curl_close($curl);
		return $output;
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

	public function pointClear($text){
		return trim(strstr($text, " "));
	}

	/**
	 * Return comments and scores made to the restaurant
	 * @param int $startpage Sets the start page
	 * @param int $maxpage $maxpage -1 for unlimited pagination
	 * @return JSON
	 */
	public function getComments($startpage =1, $maxpage = 1){
		$items = array();
		$content = $this->load($this->curl($this->url));
		$maxPage = $content->find("[id=restaurant_comments] [class=pagination] li a", -1)->innertext;
		for ($i = $startpage; $i <= $maxPage; $i++){
			if($$maxpage < $i && $maxpage != -1) break;
			$content = $this->load($this->curl($this->url."?page=".$i));
			foreach($content->find("[class=comments-body]") as $comment){
				$items[] = [
					"name" => $comment->find("[class=userName] div",0)->innertext,
					"comment" => $comment->find("[class=comment] p",0, 0)->innertext,
					"speed" => $this->pointClear($comment->find("[class=speed]", 0)->innertext),
					"service" => $this->pointClear($comment->find("[class=serving]", 0)->innertext),
					"flavour" => $this->pointClear($comment->find("[class=flavour]", 0)->innertext),
					"date" => $comment->find("[class=commentDate] div", 0)->innertext,
				];
			}
		}
		$items = array_filter($items);
		return json_encode($items);
	}
	/**
	 * Return Restarant's menus and products
	 * @return JSON
	 */
	public function getMenu(){
		$items = array();
		$dom = $this->load($this->curl($this->url));
		foreach($dom->find('[class=restaurantDetailBox]') as $box){
			$title = @$box->find("[class=head] h2",0) ? @$box->find("[class=head] h2",0)->plaintext : @$box->find("[class=head] b", 0)->plaintext;
			$desc = @$box->find("[class=head] h2",0) ? @$box->find("[class=head] [class=description]",0)->plaintext : @$box->find("[class=head] p", 0)->plaintext;
			$list = array();

			foreach($box->find("[class=listBody] ul li") as $menu){
				$temp = array();
				$temp["productName"] = @$menu->find("[class=productName]",0)->plaintext;
				$temp["productInfo"] = @$menu->find("[class=productInfo] p",0)->plaintext;
				$temp["productPrice"] = @$menu->find("[class=newPrice]",0)->plaintext;
				if(@$menu->find("[class=productName] a i", 0)){
					$data = "data-imagepath";
					$temp["productImage"] = "http:".@$menu->find("[class=productName] i", 0)->$data;
				}
				$temp = array_filter($temp);
				$list[] = $temp;
			}

			$list = array_filter($list);
			if(count($list)){
				$items[] = array(
					"title" => $title,
					"description" => $desc,
					'list' => $list,
				);
			}
		}
		$items = array_filter($items);
		return json_encode($items);
	}
}
