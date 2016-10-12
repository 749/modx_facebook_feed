<?php
/**
 * Build the setup options form.
 *
 * @package quip
 * @subpackage build
 */
/* set some default values */
$values = array(
    'app_id' => '',
    'app_secret' => '',
);
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:
        $setting = $modx->getObject('modSystemSetting',array('key' => 'facebook_feed.app_id'));
        if ($setting != null) { $values['app_id'] = $setting->get('value'); }
        unset($setting);

        $setting = $modx->getObject('modSystemSetting',array('key' => 'facebook_feed.app_secret'));
        if ($setting != null) { $values['app_secret'] = $setting->get('value'); }
        unset($setting);
    break;
    case xPDOTransport::ACTION_UNINSTALL: break;
}

$output = '<label for="facebook_feed-app_id">Facebook Application ID:</label>
<input type="text" name="app_id" id="facebook_feed-app_id" width="300" value="'.$values['app_id'].'" />
<br /><br />

<label for="facebook_feed-app_secret">Facebook Application Secret:</label>
<input type="text" name="app_secret" id="facebook_feed-app_secret" width="300" value="'.$values['app_secret'].'" />';

return $output;
