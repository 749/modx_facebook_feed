<?php
/**
 * ModX Facebook Feed
 * Allows you to easily display a Facebook pages' feed on your website.
 * Copyright (C) 2016  Jan Giesenberg <giesenja@gmail.com>
 *
 * ModX Facebook Feed is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * ModX Facebook Feed is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ModX Facebook Feed.  If not, see <http://www.gnu.org/licenses/>.
 */
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
  protected $access_token;

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

      'app_id' => $this->modx->getOption('facebook_feed.app_id', null, '')
    ),$config);
    $this->app_secret = $this->modx->getOption('facebook_feed.app_secret', null, '');
    $this->access_token = $this->modx->getOption('facebook_feed.access_token', null, '');
  }

  protected function initFB() {
    return new Facebook\Facebook([
      'app_id' => $this->config['app_id'],
      'app_secret' => $this->app_secret,
      'default_graph_version' => 'v2.8',
      'default_access_token' => $this->access_token
    ]);
  }

  protected function callFBApi() {

  }

  public function getTokenURL() {
    return 'https://graph.facebook.com/oauth/access_token?client_id=' . $this->config['app_id'] . '&client_secret=' . $this->app_secret . '&grant_type=client_credentials';
  }

  public function checkToken($token) {
    $identifier = 'access_token=';
    if(substr($token, 0, strlen($identifier)) === $identifier){
      $token = substr($token, strlen($identifier));
    } else {
      $data = json_decode($token);
      $token = $data->access_token;
    }
    if(strlen($token) == 0){
      $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Error: Tried to install empty token');
      return false;
    }

    $setting = $this->modx->getObject('modSystemSetting',array('key' => 'facebook_feed.access_token'));
    if ($setting != null) {
      $setting->set('value',$token);
      $setting->save();
      return true;
    }
    return false;
  }

  public function generateAccessToken() {
    $curl = curl_init();
    $url = $this->getTokenURL();
    //echo '<pre>'.$url.'</pre>';
    curl_setopt_array($curl, array(
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_URL => $url,
      CURLOPT_SSL_VERIFYHOST => 0,
      CURLOPT_SSL_VERIFYPEER => 0
    ));
    $result = curl_exec($curl);
    if(!$result){
      $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
      return false;
    }
    curl_close($curl);
    return $this->checkToken($result);
  }

  function calcTimeAgo($time) {
    return $this->getTimeAgo($time);
  }

  /**
   * Gets a properly formatted "time ago" from a specified timestamp. Copied
   * from MODx core output filters.
   *
   * @param string $time
   * @return string
   */
  public function getTimeAgo($time = '') {
    if (empty($time)) return false;
    $this->modx->lexicon->load('filters');
    $agoTS = array();
    $uts = array();
    $uts['start'] = strtotime($time);
    $uts['end'] = time();
    if( $uts['start']!==-1 && $uts['end']!==-1 ) {
      if( $uts['end'] >= $uts['start'] ) {
        $diff = $uts['end'] - $uts['start'];
        $years = intval((floor($diff/31536000)));
        if ($years) $diff = $diff % 31536000;
        $months = intval((floor($diff/2628000)));
        if ($months) $diff = $diff % 2628000;
        $weeks = intval((floor($diff/604800)));
        if ($weeks) $diff = $diff % 604800;
        $days = intval((floor($diff/86400)));
        if ($days) $diff = $diff % 86400;
        $hours = intval((floor($diff/3600)));
        if ($hours) $diff = $diff % 3600;
        $minutes = intval((floor($diff/60)));
        if ($minutes) $diff = $diff % 60;
        $diff = intval($diff);
        $agoTS = array(
          'years' => $years,
          'months' => $months,
          'weeks' => $weeks,
          'days' => $days,
          'hours' => $hours,
          'minutes' => $minutes,
          'seconds' => $diff,
        );
      }
    }
    $ago = array();
    if (!empty($agoTS['years'])) {
      $ago[] = $this->modx->lexicon(($agoTS['years'] > 1 ? 'ago_years' : 'ago_year'),array('time' => $agoTS['years']));
    }
    if (!empty($agoTS['months'])) {
      $ago[] = $this->modx->lexicon(($agoTS['months'] > 1 ? 'ago_months' : 'ago_month'),array('time' => $agoTS['months']));
    }
    if (!empty($agoTS['weeks']) && empty($agoTS['years'])) {
      $ago[] = $this->modx->lexicon(($agoTS['weeks'] > 1 ? 'ago_weeks' : 'ago_week'),array('time' => $agoTS['weeks']));
    }
    if (!empty($agoTS['days']) && empty($agoTS['months']) && empty($agoTS['years'])) {
      $ago[] = $this->modx->lexicon(($agoTS['days'] > 1 ? 'ago_days' : 'ago_day'),array('time' => $agoTS['days']));
    }
    if (!empty($agoTS['hours']) && empty($agoTS['weeks']) && empty($agoTS['months']) && empty($agoTS['years'])) {
      $ago[] = $this->modx->lexicon(($agoTS['hours'] > 1 ? 'ago_hours' : 'ago_hour'),array('time' => $agoTS['hours']));
    }
    if (!empty($agoTS['minutes']) && empty($agoTS['days']) && empty($agoTS['weeks']) && empty($agoTS['months']) && empty($agoTS['years'])) {
      $ago[] = $this->modx->lexicon('ago_minutes',array('time' => $agoTS['minutes']));
    }
    if (empty($ago)) { /* handle <1 min */
      $ago[] = $this->modx->lexicon('ago_seconds',array('time' => $agoTS['seconds']));
    }
    $output = implode(', ',$ago);
    $output = $this->modx->lexicon('ago',array('time' => $output));
    return $output;
  }

  function humanNumber($number) {
    if($number > 999999){
      return number_format((float)$number / 1000000., 1, '.', ''). 'm';
    } elseif ($number > 999) {
      return floor($number/1000) . 'k';
    }
    return (int)$number;
  }

  function runFeed($scriptProperties) {
    $output = '';
    $config = array_merge(array(
      'page' => '',
      'limit' => 30,
      'tpl' => 'facebook_feed_tpl',
      'authors' => '',
      'error_tpl' => 'facebook_error_tpl',
      'offset' => 0
    ), $scriptProperties);

    if(!empty($config['authors'])) {
      $authors = explode(',', $config['authors']);
    }

    if(empty($config['page'])) {
      $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'You have to give an id in the page parameter in order to use the snippet');
      return $this->modx->getChunk($config['error_tpl']);
    }

    $fb = $this->initFB();
    try{
      $url = '/' . $config['page'] . '/feed?fields=type,id,full_picture,source,from,created_time,message,name,description,story,likes.limit(2).summary(true),shares,link,permalink_url&summary=true&limit=80';
      $response = $fb->get($url);
      $data = $response->getDecodedBody()['data'];
    }catch(Facebook\Exceptions\FacebookResponseException $fb_error) {
      $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Requested URL: '. $url);
      $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Graph Error: ' . $fb_error->getMessage());
      return $this->modx->getChunk($config['error_tpl']);
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
      // When validation fails or other local issues
      $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Requested URL: '. $url);
      $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Facebook SDK returned an error: ' . $e->getMessage());
      return $this->modx->getChunk($config['error_tpl']);
    }
    $i = 0;
    foreach ($data as $post) {
      if(isset($authors) && !in_array($post['from']['id'], $authors)) {
        continue;
      }
      $pinfo = array();
      $pinfo['img'] = $post['full_picture'];
      $pinfo['name'] = $post['name'];
      $pinfo['from'] = $post['from']['name'];
      $pinfo['video'] = $post['source'];
      $pinfo['link'] = $post['link'];
      $pinfo['permalink_url'] = $post['permalink_url'];
      $pinfo['time_ago'] = $this->calcTimeAgo($post['created_time']);
      $pinfo['likes'] = $this->humanNumber($post['likes']['summary']['total_count']);
      $pinfo['shares'] = $this->humanNumber($post['shares']['count']);
      if(isset($post['message'])){
        $pinfo['message'] = nl2br($post['message']);
      } else if(isset($post['description'])) {
        $pinfo['message'] = nl2br($post['description']);
      } else {
        //ignore other types of posts
        continue;
      }
      $i++;
      if($i <= $config['offset']) {
        // ignore this post if it is at the beginning and below the offset
        continue;
      }
      if($i > $config['offset'] + $config['limit']){
        break;
        //cutoff rest of the messages
      }
      $pinfo['message'] = $this->txt2link($pinfo['message'], array('target'=>'_blank', 'rel' => 'external nofollow'));
      $output .= $this->modx->getChunk($config['tpl'], $pinfo);
    }
    //$output .= print_r($data,true);
    return $output;
  }

  public function runEvents($scriptProperties) {
      $config = array_merge(array(
          'page' => '',
          'limit' => 5,
          'tpl' => 'facebook_event_tpl',
          'empty_tpl' => 'facebook_event_empty_tpl',
          'error_tpl' => 'facebook_error_tpl',
      ), $scriptProperties);

      if(empty($config['page'])) {
          $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'You have to give an id in the page parameter in order to use the snippet');
          return $this->modx->getChunk($config['error_tpl']);
      }

      $fb = $this->initFB();
      try{
          $url = '/' . $config['page'] . '/events?fields=id,name,description,place,start_time,end_time,attending_count,declined_count,maybe_count,noreply_count,picture&limit=80';
          $response = $fb->get($url);
          $data = $response->getDecodedBody()['data'];
      }catch(Facebook\Exceptions\FacebookResponseException $fb_error) {
          $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Requested URL: '. $url);
          $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Graph Error: ' . $fb_error->getMessage());
          return $this->modx->getChunk($config['error_tpl']);
      } catch(Facebook\Exceptions\FacebookSDKException $e) {
          // When validation fails or other local issues
          $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Requested URL: '. $url);
          $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Facebook SDK returned an error: ' . $e->getMessage());
          return $this->modx->getChunk($config['error_tpl']);
      }

      $output = '';

      $i = 0;
      foreach ($data as $post) {
          $pinfo = array();
          $pinfo['img'] = $post['picture']['url'];
          $pinfo['name'] = $post['name'];
          $pinfo['description'] = nl2br($post['description']);
          $pinfo['place_name'] = $post['place']['name'];
          $pinfo['place_street'] = $post['place']['location']['street'];
          $pinfo['place_city'] = $post['place']['location']['city'];
          $pinfo['place_zip'] = $post['place']['location']['zip'];
          $pinfo['place_country'] = $post['place']['location']['country'];
          $pinfo['place_latitude'] = $post['place']['location']['latitude'];
          $pinfo['place_longitude'] = $post['place']['location']['longitude'];
          $pinfo['start_time'] = $post['start_time'];
          $pinfo['end_time'] = $post['end_time'];
          $pinfo['attending_count'] = $this->humanNumber($post['attending_count']);
          $pinfo['declined_count'] = $this->humanNumber($post['declined_count']);
          $pinfo['maybe_count'] = $this->humanNumber($post['maybe_count']);
          $pinfo['noreply_count'] = $this->humanNumber($post['noreply_count']);
          if(strtotime($post['end_time']) < time()) {
              continue;
          }
          $i++;
          if($i > $config['limit']){
              break;
              //cutoff rest of the messages
          }
          $pinfo['description'] = $this->txt2link($pinfo['description'], array('target'=>'_blank', 'rel' => 'external nofollow'));
          $output .= $this->modx->getChunk($config['tpl'], $pinfo);
      }

      if($i <= 0) {
          $output = $this->modx->getChunk($config['empty_tpl']);
      }

      return $output;
  }

  function txt2link($text, $attributes){
  	// force http: on www.
   	$text = preg_replace( '/www\./', "http://www.", $text );
  	// eliminate duplicates after force
    $text = preg_replace( '`http://http://www\.`', "http://www.", $text );
    $text = preg_replace( '`https://http://www\.`', "https://www.", $text );

    $attrs = '';
  	foreach ($attributes as $attribute => $value) {
  		$attrs .= " {$attribute}=\"{$value}\"";
  	}

  	$text = ' ' . $text;
  	$text = preg_replace(
  		'`([^"=\'>])((http|https|ftp)://[^\s<]+[^\s<\.)])`i',
  		'$1<a href="$2"'.$attrs.'>$2</a>',
  		$text
  	);
  	$text = substr($text, 1);

  	return $text;
  }
}
