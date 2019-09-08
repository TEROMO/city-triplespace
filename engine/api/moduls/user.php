<?php
	namespace API{
        class user extends API
        {
        	public $return;
            function __construct()
            {
                $_POST['type'] = (empty($_POST['type'])) ? $_GET['type'] : $_POST['type'] ;
                switch ($_POST['type']) {
                    case 'registration':
                        $this->return = $this->reg();
                        break;
                    
                    case 'login':
                        $this->return = $this->login($_POST['login'], $_POST['password']);
                        break;

                    case 'auth':
                        $this->return = $this->checkSession();
                    break;

                    case 'recovy':
                        $this->return = $this->recovy();
                        break;

                    case 'get':
                        $this->return = $this->geuUIDdata();
                    break;

                    default:
                        $this->return = array('type' => 'error', 'description' => "undefind 'type' method");
                        break;
                }
            }

            private function reg()
            {
                if (count(\RedBeanPHP\R::findAll('users', 'phone = ? or polis = ? or (name = ? and surname = ?)', array(
                    $this->encrypt($_POST['phone']),
                    $this->encrypt($_POST['polis']),
                    $this->encrypt($_POST['name']),
                    $this->encrypt($_POST['surname'])
                ))) == 0) {
                    $_POST['diseases'] = (empty($_POST['diseases'])) ? '-' : $_POST['diseases'] ;
                	if(
                        !empty($_POST['name']) and
                        !empty($_POST['surname']) and
                        !empty($_POST['phone']) and
                        !empty($_POST['diseases'])
                    ){
                        $user = \RedBeanPHP\R::xdispense('users');
                        $user->name       = $this->encrypt($_POST['name']);
                        $user->surname    = $this->encrypt($_POST['surname']);
                        $user->phone      = $this->encrypt($_POST['phone']);
                        $user->diseases   = $this->encrypt($_POST['diseases']);
                        $user->patronymic = $this->encrypt($_POST['patronymic']);
                        $user->email      = $this->encrypt($_POST['email']);
                        $user->polis      = $this->encrypt($_POST['polis']);
                        $user->password   = md5($this->encrypt($_POST['password']));
                        $user->group      = 1; 
                        $id = \RedBeanPHP\R::store($user);

                        $_POST['passport'] = array(
                            'serial' => explode(' ', $_POST['passport'])[0].explode(' ', $_POST['passport'])[1],
                            'number' => explode(' ', $_POST['passport'])[2],
                            'get'    => $_POST['passport_get'],
                            'date'   => $_POST['passport_year']
                        );

                        $this->EncryptKeyMode($id);

                        $passport = \RedBeanPHP\R::xdispense('users_passport');
                        $passport->uid    = $id;
                        $passport->serial = $this->encrypt($_POST['passport']['serial']);
                        $passport->number = $this->encrypt($_POST['passport']['number']);
                        $passport->get    = $this->encrypt($_POST['passport']['get']);
                        $passport->date   = date('Y-m-d', strtotime($_POST['passport']['date']));
                        $PassId = \RedBeanPHP\R::store($passport);
                    
                        $this->EncryptKeyUnMode();

                        return array('type' => 'success', 'id' => $PassId, 'session' => $this->startSession($id)['hash'], "uid" => md5($getUser['id']));
                    }else{
                        return array("type" => "error", "description" => "This is method required POST params: name, surname, phone, diseases. Plese, check your query.");
                    }
                }else{
                    return array("type" => "error", "description" => "This is user find to database");
                }
            }

            private function startSession($id)
            {
                $key = md5($this->encrypt($id.$_SERVER['REMOTE_ADDR']));
                $session = \RedBeanPHP\R::findAll('users_sessions', 'hash = ?', [$key]);
                if (count($session) == 0) {
                    $session = \RedBeanPHP\R::xdispense('users_sessions');
                    $session->uid  = $id;
                    $session->hash = $key;
                    $sessionID = \RedBeanPHP\R::store($session);
                }
                return array('type' => 'success', 'hash' => $this->encrypt($key));
            }

            protected function checkSession()
            {
                $hash = $this->decode($_COOKIE['authData']);
                $session = \RedBeanPHP\R::findOne('users_sessions', 'hash = ?', [$hash]);
                if (md5($session['uid']) == $_COOKIE['uid'] and !empty($session['uid'])) {
                    $user = \RedBeanPHP\R::findOne('users', 'id = ?', array($session['uid']));
                    foreach ($user as $key => $value) {
                        $dec = $this->decode($value);
                        if (!empty($dec)) {
                            $user[$key] = $dec;
                        }
                    }
                    unset($user['password']);
                    $user['group'] = \RedBeanPHP\R::findOne('users_groups', 'id = ?', array($user['group']));
                    $user['avatar'] = \RedBeanPHP\R::findOne('users_avatars', 'id = ?', array($user['avatar']));
                    $user['address'] = \RedBeanPHP\R::findAll('users_address', 'uid = ?', array($user['id']));
                    unset($user['id']);
                    return array('type' => 'success', 'user' =>  $user);
                }else{
                    return array('type' => 'error');
                }
            }

            private function login($phone, $password)
            {
                $getUser = \RedBeanPHP\R::findOne('users', 'phone = ?', array($this->encrypt($phone)));
                if ($getUser['password'] == md5($this->encrypt($password))) {
                    return array('type' => 'success', 'session' => $this->startSession($getUser['id'])['hash'], "uid" => md5($getUser['id']));
                }else{
                    return array('type' => 'error', 'description' => 'Your password or login not validate');
                }
            }

            private function recovy($value='')
            {
            	# code...
            }

            private function update($value='')
            {
            	# code...
            }

            private function rating($num)
            {
            	# code...
            }

        }
    }
?>