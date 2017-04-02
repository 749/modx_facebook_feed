<?php
/**
 * @package facebook_feed
 * @subpackage build
 */

$chunks = array();

$chunks[1]= $modx->newObject('modChunk');
$chunks[1]->fromArray(array(
    'id' => 1,
    'name' => 'facebook_feed_tpl',
    'description' => 'Default template for the Facebook Feed',
    'snippet' => file_get_contents($sources['source_core'].'/chunks/facebook_feed_tpl.chunk.tpl'),
    'properties' => '',
),'',true,true);

$chunks[2]= $modx->newObject('modChunk');
$chunks[2]->fromArray(array(
    'id' => 2,
    'name' => 'facebook_error_tpl',
    'description' => 'Default error template for the Facebook Feed',
    'snippet' => file_get_contents($sources['source_core'].'/chunks/facebook_error_tpl.chunk.tpl'),
    'properties' => '',
),'',true,true);

$chunks[3]= $modx->newObject('modChunk');
$chunks[3]->fromArray(array(
    'id' => 3,
    'name' => 'facebook_event_tpl',
    'description' => 'Default template for the Facebook Events',
    'snippet' => file_get_contents($sources['source_core'].'/chunks/facebook_event_tpl.chunk.tpl'),
    'properties' => '',
),'',true,true);

$chunks[4]= $modx->newObject('modChunk');
$chunks[4]->fromArray(array(
    'id' => 4,
    'name' => 'facebook_event_empty_tpl',
    'description' => 'Default template to be displayed when no current Events are available',
    'snippet' => file_get_contents($sources['source_core'].'/chunks/facebook_event_empty_tpl.chunk.tpl'),
    'properties' => '',
),'',true,true);

return $chunks;
