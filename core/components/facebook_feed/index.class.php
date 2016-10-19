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
        $this->addHtml('<script type="text/javascript">
        Ext.onReady(function() {
            FB_Feed.config = '.$this->modx->toJSON($this->feed->config).';
            FB_Feed.config.connector_url = "'.$this->feed->config['connectorUrl'].'";
        });
        </script>');
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
