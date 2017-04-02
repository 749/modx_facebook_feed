<?php

/* create category */
$category= $modx->newObject('modCategory');
$category->set('id',1);
$category->set('category','FB_Feed');
$modx->log(modX::LOG_LEVEL_INFO,'Packaged in category.'); flush();


/* create the snippet */
$snippet= $modx->newObject('modSnippet');
$snippet->set('id',0);
$snippet->set('name', 'FB_Feed');
$snippet->set('description', 'A simple Facebook Feed component.');
$snippet->set('snippet',file_get_contents($sources['source_core'].'/snippets/snippet.fb_feed.php'));

$category->addMany($snippet);

/* create the snippet */
$snippet= $modx->newObject('modSnippet');
$snippet->set('id',0);
$snippet->set('name', 'FB_Events');
$snippet->set('description', 'A simple Facebook Events component.');
$snippet->set('snippet',file_get_contents($sources['source_core'].'/snippets/snippet.fb_events.php'));

$category->addMany($snippet);
unset($snippet);

/* add chunks */
$chunks = include $sources['data'].'transport.chunks.php';
if (is_array($chunks)) {
    $category->addMany($chunks);
} else { $modx->log(modX::LOG_LEVEL_FATAL,'Adding chunks failed.'); }

return $category;
