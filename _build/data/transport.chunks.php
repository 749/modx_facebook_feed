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

return $chunks;
