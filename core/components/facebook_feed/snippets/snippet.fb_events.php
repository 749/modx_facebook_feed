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
/**
 * Facebook_Events
 *
 * This snippet allows you to import events from a facebook page into a webpage and style it anyway you want.
 *
 * @param page The id of the page you want the events to loaded from
 * @param limit The number of events you want to display, maximum 80, default 5
 * @param tpl The chunk to be used as template for the event
 * @param error_tpl The chunk to be displayed in case the facebook api request fails
 */
$feed = $modx->getService('fb_feed','Feed',$modx->getOption('facebook_feed.core_path',null,$modx->getOption('core_path').'components/facebook_feed/').'model/fb_feed/',$scriptProperties);
if (!($feed instanceof Feed)) return '';

return $feed->runEvents($scriptProperties);