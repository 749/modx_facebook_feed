<?php
/**
 * Facebook_Feed
 *
 * This snippet allows you to import a facebook page feed into a webpage and style it anyway you want.
 *
 * @param page The id of the page you want the feed to loaded from
 * @param limit The number of posts you want to display, maximum 100
 * @param tpl The chunk to be used as template for the feed
 */
 $feed = $modx->getService('fb_feed','Feed',$modx->getOption('facebook_feed.core_path',null,$modx->getOption('core_path').'components/facebook_feed/').'model/fb_feed/',$scriptProperties);
 if (!($feed instanceof Feed)) return '';

return $feed->runFeed($scriptProperties);
