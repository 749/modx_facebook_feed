<?php
include '../Facebook/autoload.php';

class Feed {
  /**
   * A reference to the modX object.
   * @var modX $modx
   */
  public $modx = null;

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
      'app_secret' => $this->modx->getOption('facebook_feed.app_secret', null, '')
    ),$config);
  }

  function runFeed($scriptProperties) {
    $output = '';
    $config = array_merge(array(
      'page' => '1',
      'limit' => 30,
      'tpl' => 'facebook_feed_tpl'
    ), $scriptProperties);

    $fb = new Facebook\Facebook([
      'app_id' => $this->config['app_id'],
      'app_secret' => $this->config['app_secret'],
      'default_graph_version' => 'v2.8',
    ]);
    $request = new Facebook\FacebookRequest(
      $fb->getApp(),
      'GET',
      '/'.$config['page'].'/feed?fields=full_picture,created_time,id,message,description,story,likes.limit(2).summary(true),shares,comments_mirroring_domain,comments,link&summary=true&limit='.$config['limit']
    );
    $response = $request->execute();
    $graphObject = $response->getGraphObject();
    $output = print_r($graphObject, true);
    return $output;
  }
}
