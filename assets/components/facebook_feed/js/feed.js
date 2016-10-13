if(!FB_Feed)
  FB_Feed = {};

Ext.onReady(function() {
  document.getElementById("FB_Login").onclick = function() {
    FB.Login(function(response) {
      if (response.authResponse) {
        //reload the page to get managed pages
      }
    }, {scope: 'manage_pages'});
  }
});
