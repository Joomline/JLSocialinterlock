<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */
//require_once JPATH_BASE.'/plugins/slogin_auth/twitter/assets/twitteroauth/twitteroauth.php';
require_once JPATH_BASE.'/plugins/system/jllikelock/lib/twitteroauth.php';

class plgJLLikeLockTwitterHelper
{
    var $params, $consumer_key, $consumer_secret, $screenName, $accessToken, $accessTokenSecret;

    function __construct($params)
    {
        $this->params = $params;
        $this->consumer_key = $this->params->get('twitter_consumer_key','');
        $this->consumer_secret = $this->params->get('twitter_consumer_secret','');
        $this->screenName = $this->params->get('twitter_screen_name','');
        $this->accessToken = $this->params->get('twitter_access_token','');
        $this->accessTokenSecret = $this->params->get('twitter_access_token_secret','');
    }

    public function getTwirrerFollowers(){
        $input = JFactory::getApplication()->input;
        $id = $input->getString('id','');
        $twitauth = new JllikelockTwitterOAuth($this->consumer_key, $this->consumer_secret, $this->accessToken, $this->accessTokenSecret);
        $data = array();
        if($id){
            $userData = JFactory::getApplication()->getUserState('jllikelock.twitter.data', array());

            if(empty($userData['id'])){
                return 0;
            }
        }
        $data = array('screen_name' => $this->screenName);
        $request = $twitauth->get('followers/ids',$data);

        if(isset($request->ids) && is_array($request->ids)){
            if($id){
                return in_array($userData['id'], $request->ids) ? 1 : 0;
            }
            return count($request->ids);
        }
        return 0;
    }

    public function getTwitterAuthUrl()
    {
        $twitauth = new JllikelockTwitterOAuth($this->consumer_key, $this->consumer_secret);

        $request_token = $twitauth->getRequestToken('');

        if (empty($request_token['oauth_token'])) {
            die('Error: oauth_token not set');
        }

        //установка значений в сессию
        $session = JFactory::getSession();
        $session->set('oauth_token', $request_token['oauth_token']);
        $session->set('oauth_token_secret', $request_token['oauth_token_secret']);

        //редирект на страницу авторизации
        $url = $twitauth->getAuthorizeURL($request_token);

        return $url;

    }

    public function loadTwitterId()
    {
        $input = JFactory::getApplication()->input;
        $oauth_token = $input->getString('oauth_token','');
        $oauth_verifier = $input->getString('oauth_verifier','');
        $code = $input->getString('oauth_verifier', '');
        if($code){
            //получение значений из сессии
            $session = JFactory::getSession();
            $oauth_token = $session->get('oauth_token');
            $oauth_token_secret = $session->get('oauth_token_secret');

            $connection = new JllikelockTwitterOAuth($this->consumer_key, $this->consumer_secret, $oauth_token, $oauth_token_secret);
            $access_token = $connection->getAccessToken($code);

            if (200 == $connection->http_code) {
                $request = $connection->get('users/show', array('screen_name' => $access_token['screen_name']));
                //удаляем данные из сессии, уже не нужны
                $session->clear('oauth_token');
                $session->clear('oauth_signature');

                if(empty($request->id)){
                    echo 'Error - empty user data';
                    exit;
                }
                else if(!empty($request->errors)){
                    foreach($request->errors as $errors){
                        echo 'Error - '. $errors->message;
                    }
                    exit;
                }

                //сохраняем данные токена в сессию
                //expire - время устаревания скрипта, метка времени Unix
                JFactory::getApplication()->setUserState('jllikelock.twitter.data', array(
                    'provider' => 'twitter',
                    'id' => $request->id,
                    'screen_name' => $request->screen_name,
                ));

                $data = array('screen_name' => $this->screenName);
                $followers = $connection->get('followers/ids',$data);
                if(isset($followers->ids) && is_array($followers->ids) && in_array($request->id, $followers->ids)){
                    ?>
                    <html>
                    <head>
                        <script type="text/javascript">
                            window.opener.openText(window.opener.protocol + "//" + window.opener.location.host);
                            window.close();
                        </script>
                    </head>
                    <body>
                    </body>
                    </html>
                    <?php
                }
                else{
                    JFactory::getApplication()->redirect('https://twitter.com/intent/follow?screen_name=' . $this->screenName);
                }

            }
            else{
                echo 'Error - not connect to Twitter';
                exit;
            }

        }
        else{
            echo 'Error - not connect to Twitter';
            exit;
        }
    }
}
