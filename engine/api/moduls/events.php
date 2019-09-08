<?php
    namespace API{
        class events extends API
        {
        	public $return;
            function __construct()
            {
                $_POST['type'] = (empty($_POST['type'])) ? $_GET['type'] : $_POST['type'] ;
                switch ($_POST['type']) {
                    case 'add':
                        $this->return = $this->setEvent();
                        break;

                    case 'get':
                        $this->return = $this->getEvent();
                    break;

                    case 'chengeEventStatus':
                        $this->return = $this->setStstusEvent();
                    break;
                    
                    case 'category-list':
                        $this->return = $this->getCategoryList();
                    break;

                    case 'category-addr':
                        $this->return = $this->getUserAddr();
                    break;

                    case 'newStstus':
                        $this->return = $this->setStstus();
                    break;

                    case 'editStstus':
                        $this->return = $this->updateStstus($_GET['id']);
                    break;

                    case 'deleteStstus':
                        $this->return = $this->unsetStstus($_GET['id']);
                    break;

                    default:
                        $this->return = array('type' => 'error', 'description' => "undefind 'type' method");
                        break;
                }
            }

            private function checkAuth()
            {
                $ch = curl_init('https://city.triplespace.ru/api/user?type=auth');
                curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (iPhone; CPU iPhone OS 5_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9B179 Safari/7534.48.3" );
                curl_setopt($ch, CURLOPT_COOKIE, "uid={$_COOKIE['uid']};authData={$_COOKIE['authData']}");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_HEADER, false);
                $checkJSON = curl_exec($ch);
	            curl_close($ch);
	            return json_decode($checkJSON, true);
            }

            // Полуаем списки данных

            protected function getCategoryList()
            {
                return \RedBeanPHP\R::getAll( 'SELECT accidents_subcategory.id AS `id`, accidents_category.id AS `cid`, accidents_category.name AS `cname`, accidents_subcategory.name AS `name` FROM `accidents_subcategory` LEFT JOIN accidents_category ON accidents_category.id = accidents_subcategory.category' );
            }

            protected function getUserAddr($id)
            {
                # code...
            }

            // Работа с статусами

            protected function setStstus()
            {
                # code...
            }

            protected function updateStstus($id)
            {
                # code...
            }

            protected function unsetStstus($id)
            {
                # code...
            }

            // Работа с событиями

            protected function setEvent()
            {
                $user = $this->checkAuth();
                $_POST['category'] = explode("_", $_POST['category']);
                $add = \RedBeanPHP\R::xdispense('accidents');
                $add->category    =     $_POST['category'][0] ;
                $add->subcategory =     $_POST['category'][1] ;
                $add->title       =     $_POST['title']       ;
                $add->date_add    =     date('Y-m-d H:i:s')   ;
                $add->description =    (!empty($_POST['description'])) ? $_POST['description'] : null ;
                $add->addres      =    ($_POST['addres_type'] != 'map') ? $_POST['addres'] : null ;
                $add->addres_type =     $_POST['addres_type'] ;
                $add->latitude    =    ($_POST['addres_type'] == 'map') ? $_POST['latitude'] : null ;
                $add->longitude   =    ($_POST['addres_type'] == 'map') ? $_POST['longitude'] : null ;
                $add->uid         =    $user['id'];
                return \RedBeanPHP\R::store($add);
            }

            protected function getEvent($id = null)
            {
                if (!is_null($id)) {
                    $get = \RedBeanPHP\R::findOne('accidents', 'id = ?', [$id]);
                    $get['status'] = \RedBeanPHP\R::findAll('accidents_status', 'id = ?', [$get['status']]);
                    $get['category'] = \RedBeanPHP\R::findAll('category', 'id = ?', [$get['category']]);
                    $get['subcategory'] = \RedBeanPHP\R::findAll('accidents_subcategory', 'id = ?', [$get['subcategory']]);
                }else{
                    $get = \RedBeanPHP\R::findAll('accidents');
                    for ($i=0; $i < count($get); $i++) { 
                        $get[$i]['status'] = \RedBeanPHP\R::findOne('accidents_status', 'id = ?', [$get[$i]['status']])['name'];
                        $get[$i]['category'] = \RedBeanPHP\R::findOne('accidents_category', 'id = ?', [$get[$i]['category']]);
                        $get[$i]['subcategory'] = \RedBeanPHP\R::findOne('accidents_subcategory', 'id = ?', [$get[$i]['subcategory']]);
                        switch ($get[$i]['addres_type']) {
                            case 'id':
                                $get[$i]['addres'] = \RedBeanPHP\R::findOne('users_address', 'id = ?', [$get[$i]['addres']])['location'];
                                break;

                            case 'map':
                                # code...
                                break;

                            default:
                                break;
                        }
                    }
                    unset($get["0"]);
                }
                return array('type' => 'success', 'get' => $get);
            }

            protected function setStstusEvent($id)
            {
                # code...
            }

            protected function unsetEvent($id)
            {
                # code...
            }
        }
    }
?>