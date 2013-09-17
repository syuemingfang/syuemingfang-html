<?php
/*!

# Cinderella
  Source Code Convert to HTML.
  
  [Getting Started](http://html.cxm.tw) [GitHub project](https://github.com/syuemingfang/syuemingfang-html)

****************************************************************************************************/

/*!

+ Version: 0.1.0.0
+ Copyright © 2013 [Syue](mailto:syuemingfang@gmail.com). All rights reserved.
+ Date: *Tue Aug 06 2013 16:47:48 GMT+0800 (Central Standard Time)*

****************************************************************************************************/
//!
//!## Class
class Cinderella{
	public $url; //this page for search
	public $url_self; //cinderella url
	public $domain;
	public $ext;
	function __construct(){
		// BLAHBLAHBLAH....
	}
	public function getHTML($url){
		$ch=curl_init();
		$options=array(CURLOPT_URL => $url, CURLOPT_HEADER => false, CURLOPT_RETURNTRANSFER => true, CURLOPT_USERAGENT => "Google Bot", CURLOPT_SSL_VERIFYPEER => false, CURLOPT_FOLLOWLOCATION => true);
		curl_setopt_array($ch, $options);
		$html=curl_exec($ch);
		curl_close($ch);
		return $html;
	}
	public function getDomain($url){
		// Get Domain Name
		$pat='/(.*)\/(.*)/i';
		$rep='$1/';
		$domain=preg_replace($pat, $rep, $url);
		return $domain;
	}
	public function getExt($url){
		// Get Ext Name
		$pat='/(.*)\/(.*\.(.*?))/i';
		$rep='$3';
		$ext=preg_replace($pat, $rep, $url);
		return $ext;
	}
	public function replaceHTML($html){
		$array=array(
			1=>array(
				'pat' => '/(src|href).*?=\"((?!http)(.*?)\.(js|css|html))\"/i',
				'rep' => '$1="'.$this->url_self.'?url='.$this->domain.'$2"'
			),
			2=>array(
				'pat' => '/@import.*?\"(.*?)\"/i',
				'rep' => '@import "'.$this->url_self.'?url='.$this->domain.'$1"'
			),
			3=>array(
				'pat' => '/url\((.*?)\)/i',
				'rep' => 'url('.$this->domain.'$1)'
			),
			4=>array(
				'pat' => '/src.*?=\"((?!http)(.*?)\.(png|gif|jpg|swf))\"/i',
				'rep' => 'src="'.$this->domain.'$1"'
			)
		);
		foreach($array as $i){
			$html=preg_replace($i['pat'], $i['rep'], $html);
		}
		return $html;
	}
}

class main{
	private $r;
	private $url_self;
	private $url_jspipe;		
	function __construct(){
		// BLAHBLAHBLAH....
		$this->initialize();
	}
	public function initialize(){
		// Declare
		$r=array(
				'url'=>isset($_REQUEST['url']) ? $_REQUEST['url'] : false,
				'jspipe'=>isset($_REQUEST['jspipe']) ? $_REQUEST['jspipe'] : false		
			);
		$url_self='http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
		$url_jspipe='http://jspipe.cxm.tw/';
		// Constructor
		if($r['url'] == false){
			header('Content-type: text/html');
			require('main.html');
		} else{
			$c=new Cinderella();
			$c->url_self=$url_self;
			$c->domain=$c->getDomain($r['url']);
			$c->ext=$c->getExt($r['url']);
			$html=$c->getHTML($r['url']);
			$html=$c->replaceHTML($html);
			if($r['jspipe'] == false){
				if($c->ext == 'css'){
					header('Content-type: text/css');
				} else if($c->ext == 'js'){
					header('Content-Type: application/javascript');
				} else{
					header('Content-type: text/html');
				}
				echo $html;
			 	exit;
			} else{
				header('Location: '.$url_jspipe.'?url='.$url_self.'?url='.$r['url']);
			 	exit;
			}

		}
	}
}
$s=new main();

?>