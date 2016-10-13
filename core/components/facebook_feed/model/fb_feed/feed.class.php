<?php
require_once __DIR__.'/../Facebook/autoload.php';

class Feed {
  /**
   * A reference to the modX object.
   * @var modX $modx
   */
  public $modx = null;

  /**
   * The Facebook Application Secret
   */
  protected $app_secret;
  protected $page_token;

  public $config;

  function __construct(modX &$modx,array $config = array()) {
    $this->modx =& $modx;

    /* allows you to set paths in different environments
     * this allows for easier SVN management of files
     */
    $corePath = $this->modx->getOption('facebook_feed.core_path',null,$modx->getOption('core_path').'components/facebook_feed/');
    $assetsPath = $this->modx->getOption('facebook_feed.assets_path',null,$modx->getOption('assets_path').'components/facebook_feed/');
    $assetsUrl = $this->modx->getOption('facebook_feed.assets_url',null,$modx->getOption('assets_url').'components/facebook_feed/');

    $this->config = array_merge(array(
      'corePath' => $corePath,
      'modelPath' => $corePath.'model/',
      'processorsPath' => $corePath.'processors/',
      'controllersPath' => $corePath.'controllers/',
      'templatesPath' => $corePath.'templates/',
      'chunksPath' => $corePath.'elements/chunks/',
      'snippetsPath' => $corePath.'elements/snippets/',

      'baseUrl' => $assetsUrl,
      'cssUrl' => $assetsUrl.'css/',
      'jsUrl' => $assetsUrl.'js/',
      'connectorUrl' => $assetsUrl.'connector.php',

      'app_id' => $this->modx->getOption('facebook_feed.app_id', null, ''),
      'page_id' => $this->modx->getOption('facebook_feed.page_id', null, '')
    ),$config);
    $this->app_secret = $this->modx->getOption('facebook_feed.app_secret', null, '');
    $this->page_token = $this->modx->getOption('facebook_feed.page_token', null, '');
  }

  function initFB() {
    return new Facebook\Facebook([
      'app_id' => $this->config['app_id'],
      'app_secret' => $this->app_secret,
      'default_graph_version' => 'v2.8',
      'default_access_token' => '300618826991721|sPBUAkM_V0ok3SbpYHy0iwWtSCY'
    ]);
  }

  function getExtendedToken($fb) {
    $helper = $fb->getJavaScriptHelper();
    try {
      $accessToken = $helper->getAccessToken();
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
      // When Graph returns an error
      echo 'Graph returned an error: ' . $e->getMessage();
      return '';
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
      // When validation fails or other local issues
      echo 'Facebook SDK returned an error: ' . $e->getMessage();
      return '';
    }

    if($accessToken){
      $oAuth2Client = $fb->getOAuth2Client();
      if (! $accessToken->isLongLived()) {
        // Exchanges a short-lived access token for a long-lived one
        try {
          $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
          echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
          return '';
        }
      }
      $_SESSION['FBF_Token'] = $accessToken;
      return $accessToken;
    }
  }

  function getPages() {
    $fb = $this->initFB();

    $accessToken = $_SESSION['FBF_Token'];

    if(!$accessToken)
      $accessToken = $this->getExtendedToken($fb);
    if(!$accessToken)
      return '';
    $response = $fb->get('/me/accounts?fields=access_token,name&access_token='.$accessToken->getValue());
    return $response->getGraphEdge();
  }

  function runFeed($scriptProperties) {
    $output = '';
    $config = array_merge(array(
      'page' => '1',
      'limit' => 30,
      'tpl' => 'facebook_feed_tpl'
    ), $scriptProperties);

    $fb = $this->initFB();
    $response = $fb->get('/' . $config['page'] . '/feed?fields=full_picture,created_time,id,message,description,story,likes.limit(2).summary(true),shares,comments_mirroring_domain,comments,link&summary=true&limit=' . $config['limit']);
    $data = $response->getDecodedBody()['data'];
    foreach ($data as $post) {
      $pinfo = array();
      $pinfo['img'] = $post['full_picture'];
      $pinfo['name'] = $post['name'];
      //$pinfo['time_ago'] = $this->calcTimeAgo($post['created_time']);
      if(isset($post['message'])){
        $pinfo['message'] = nl2br($post['message']);
      } else if(isset($post['description'])) {
        $pinfo['message'] = nl2br($post['description']);
      } else {
        //ignore other types of posts
        continue;
      }
      $output .= $modx->getChunk($config['tpl'], $pinfo);
    }
    $output .= print_r($data,true);
    return $output;
  }
}
