<?php 
namespace API{
	class API
	{
		protected $method;
		private $session;
		protected $token;
		protected $EncryptKey;
		protected $EncryptKeyRemember;
		function __construct($method)
		{
			$this->EncryptKey = file_get_contents('encrypt.key');
			$this->method  = $method;
		}

		protected function EncryptKeyMode($id)
		{
			$this->EncryptKeyRemember = $this->EncryptKey;
			$this->EncryptKey .= md5(base64_encode(md5($id)));
		}

		protected function EncryptKeyUnMode()
		{
			$this->EncryptKey = $this->EncryptKeyRemember;
		}

		public function encrypt($str)
		{
			$string=base64_encode($str);
			$arr=array();
			$x=0;
			$newstr = '';
			while ($x++< strlen($string)) {
				$arr[$x-1] = md5(md5($this->EncryptKey.$string[$x-1]).$this->EncryptKey);
				$newstr = $newstr.$arr[$x-1][3].$arr[$x-1][6].$arr[$x-1][1].$arr[$x-1][2];
			}
			return $newstr;
		}

		protected function decode($str)
		{
			$strofsym="qwertyuiopasdfghjklzxcvbnm1234567890QWERTYUIOPASDFGHJKLZXCVBNM= -+()йцукенгшщзхъфывапролджэячсмитьбюёЙЦУКЕНГШЩЗХЪ/|ФЫВАПРОЛДЖЭЯЧСМЬБЮ.,;:";
			$x=0;
			while ($x++<= strlen($strofsym)) {
				$tmp = md5(md5($this->EncryptKey.$strofsym[$x-1]).$this->EncryptKey);
				$str = str_replace($tmp[3].$tmp[6].$tmp[1].$tmp[2], $strofsym[$x-1], $str);
			}
			return base64_decode($str);
		}

		private function getModuls($flodr)
		{
			$filelist = glob($flodr.'/*.php');
			for ($i=0; $i < count($filelist); $i++) { 
				$filelist[$i] = str_replace($flodr.'/', '', $filelist[$i]);
			}
			return $filelist;
		}
		
		protected function setSession($session)
		{
			$this->session = $session;
		}
		protected function setToken($token)
		{
			$this->token = $token;
		}
		protected function auth()
		{
			$getInfoForCheck = \RedBeanPHP\R::findOne('api_session', 'hash = ?', [$this->session]);
			$getTokenForCheck = \RedBeanPHP\R::findOne('api_keys', 'tkey = ?', [$this->token]);
			if ($getInfoForCheck['tk'] == $getTokenForCheck['id']) {
				return true;
			}else{
				return false;
			}
		}

		protected function createKey()
		{
			$apikeyWord = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
			$apikey = 'BBC';
			for ($i=1; $i < 10; $i++) { 
				$apikey .= '-';
				$getWord = array_rand($apikeyWord,rand(4, 10));
				for ($getWordi=0; $getWordi < count($getWord); $getWordi++) { 
					$apikey .= $apikeyWord[$getWord[$getWordi]];
				}
			}
			$return = $apikey;
			$set = \RedBeanPHP\R::xdispense('api_keys');
			$set->tkey = $apikey;
			$set->rand = rand(100000, 999999);
			$set->date = date('Y-m-d H:i:s');
			\RedBeanPHP\R::store($set);
			return $return;
		}

		protected function getSessionKey()
		{
			# code...
		}

		public function action()
		{
			$floder = 'engine/api/moduls';
			$files = $this->getModuls($floder);
			if(in_array($this->method.'.php', $files)){
				require $floder.'/'.$this->method.'.php';
				$class = '\\API\\'.$this->method;
				$module = new $class();
				return $module->return;
			}else{
				return array('type' => 'error', 'description' => 'Method not defined');
			}
		}
	}
}
?>