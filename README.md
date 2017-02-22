# ModX Facebook Feed
This piece of Software will allow you to add a Facebook Feed from one of your
Facebook pages to your site. This is great for companies that want to update
clients using the Facebook page, but not want an ugly Facebook widget on their
page.


Requirements
------------
* Tested with ModX 2.5.1
* Needs PHP 5.4 or newer (Facebook API)


Installation
------------

1. Create a Facebook app using the Facebook for Developers page
   https://developers.facebook.com/apps
2. Note down both the AppID and the App Secret
3. Install the package using the ModX package manager
   During the installation you will be asked to provide the ID and Secret
   of your Facebook app
4. Use the Facebook Feed manager page to request a valid access token from
   facebook
5. You will need the id of the page you want to display.
   Try http://findmyfbid.com to find to correct id of your page
6. Use the provided snippet to display a Feed on your page ``[[!FB_Feed? &page=`<your page id>` &limit=`6`]]``


ModX Extra ID
58075fc3bc8dd305248b4567-588210cdbc8dd32d338b4567
