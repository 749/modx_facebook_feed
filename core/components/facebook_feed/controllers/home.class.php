<?php

class Facebook_feedHomeManagerController extends Facebook_feedManagerController {
    /**
     * Any specific processing we want to do here. Return a string of html.
     * @param array $scriptProperties
     */
    public function process(array $scriptProperties = array()) {
      $data = $this->feed->getPages();
      return '<p><a href="javascript:" id="FB_Login">Login with Facebook</a></p>'.print_r($data, true);
    }
    /**
     * The pagetitle to put in the <title> attribute.
     * @return null|string
     */
    public function getPageTitle() {
        return 'Facebook Feed Manager';
    }
    /**
     * Register needed assets. Using this method, it will automagically
     * combine and compress them if that is enabled in system settings.
     */
    public function loadCustomCssJs() {
        /*$this->addCss('url/to/some/css_file.css');
        $this->addJavascript('url/to/some/javascript.js');*/
        $this->addJavascript($this->feed->config['jsUrl'].'feed.js');
        $this->addHtml('<script type="text/javascript">
        window.fbAsyncInit = function() {
          FB.init({
            appId  : \''.$this->feed->config['app_id'].'\',
            xfbml  : false,
            version: \'v2.8\'
          });
          FB.getLoginStatus(function(status){
            if (response.status === \'connected\') {
              //remove login button
              var elem = document.getElementById("FB_Login").parentNode;
              elem.parentNode.removeChild(elem);
            }
          });
        };
        Ext.onReady(function() {
          (function(d, s, id){
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {return;}
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
          }(document, \'script\', \'facebook-jssdk\'));
        });
        </script>');
    }
}
