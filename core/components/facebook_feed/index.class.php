<?php
require_once dirname(__FILE__) . '/model/fb_feed/feed.class.php';
/**
 * @package facebook_feed
 * @subpackage controllers
 */
abstract class Facebook_feedManagerController extends modExtraManagerController {
    /** @var Quip $quip */
    public $feed;
    public function initialize() {
        $this->feed = new Feed($this->modx);
        //$this->addCss($this->quip->config['cssUrl'].'mgr.css');
        //$this->addJavascript($this->quip->config['jsUrl'].'quip.js');
        //$this->addHtml('<script type="text/javascript">
        //Ext.onReady(function() {
        //    Quip.config = '.$this->modx->toJSON($this->quip->config).';
        //    Quip.config.connector_url = "'.$this->quip->config['connectorUrl'].'";
        //});
        //</script>');
        return parent::initialize();
    }
    public function getLanguageTopics() {
        return array('facebook_feed:default');
    }
    public function checkPermissions() { return true;}
}
/**
 * @package facebook_feed
 * @subpackage controllers
 */
class IndexManagerController extends Facebook_feedManagerController {
    public static function getDefaultController() { return 'home'; }
}
