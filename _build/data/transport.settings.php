<?php
/**
 * @package facebook_feed
 * @subpackage build
 */

$settings = array();

$settings['facebook_feed.app_id']= $modx->newObject('modSystemSetting');
$settings['facebook_feed.app_id']->fromArray(array(
    'key' => 'facebook_feed.app_id',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'facebook_feed',
    'area' => 'App_Key'
),'',true,true);

$settings['facebook_feed.app_secret']= $modx->newObject('modSystemSetting');
$settings['facebook_feed.app_secret']->fromArray(array(
    'key' => 'facebook_feed.app_secret',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'facebook_feed',
    'area' => 'App_Key'
),'',true,true);

$settings['facebook_feed.access_token']= $modx->newObject('modSystemSetting');
$settings['facebook_feed.access_token']->fromArray(array(
    'key' => 'facebook_feed.access_token',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'facebook_feed',
    'area' => 'App_Key'
),'',true,true);

return $settings;
