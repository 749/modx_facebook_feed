# ModX Facebook Feed
This piece of Software will allow you to add a Facebook Feed from one of your
Facebook pages to your site. This is great for companies that want to update
clients using the Facebook page, but not want an ugly Facebook widget on their
page.


Requirements
------------
* Tested with ModX 2.7.1
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

## Feed Snippet

### Snippet Options

The package includes one snippet which will do all the work for you.
It has several options however the bare minimum you need is the following:

``[[!FB_Feed? &page=`<your page id or shortname>`]]``

Further options are:

* ``&limit=`<number>` ``

  This option allows you to define the number of results you wish to be displayed  
  Maximum: 80  
  Default: 30
s
  Example: ``[[!FB_Feed? &page=`<your page id or shortname>` &limit=`6`]]``


* ``&offset=`<number>` ``

  This option allows you to define the number of results from the beginning you wish to ignore  
  Maximum: 79 - <limit>
  Default: 0

  Example: ``[[!FB_Feed? &page=`<your page id or shortname>` &offset=`1`]]``

* ``&authors=`<comma separated ids>` ``

  This option allows you to filter the feed of your page to only display certain authors  
  Use http://findmyfbid.com to find the id of the page or user how should be allowed

  Example: ``[[!FB_Feed? &page=`<your page id or shortname>` &authors=`123456789,234567891`]]``

* ``&tpl=`<name of the template chunk>` ``

  This option allows you to change the chunk that is used as a template per post

  Example: ``[[!FB_Feed? &page=`<your page id or shortname>` &tpl=`myfeedtpl`]]``

* ``&error_tpl=`<name of the template chunk>` ``

  This option allows you to change the chunk that is used in case the facebook api calls fail

  Example: ``[[!FB_Feed? &page=`<your page id or shortname>` &error_tpl=`myerrortpl`]]``


### Template Options

The default template for the snippet uses almost all fields available. The default template looks like this:

```html
<div class="post">
  <img src="[[+img]]">
  <p class="posted">by [[+name]] [[+time_ago]]</p>
  <p>[[+message]]</p>
  <p>Liked [[+likes]] times and shared [[+shares]] times. <a href="[[+link]]">View on Facebook</a></p>
</div>
```

* ``[[+img]]``

  Will insert the full url of the full_picture field of facebook

* ``[[+video]]``

  Will insert the full video url of the source field of facebook

* ``[[+name]]``

  Will insert the publishers name from the name field of facebook

* ``[[+time_ago]]``

  Will insert a nicely formatted time since posted from the created_time field of facebook

* ``[[+message]]``

  Will insert the message from the message or description field of facebook

* ``[[+likes]]``

  Will insert the number of likes as human readable number from the likes.summary.total_count field of facebook

* ``[[+shares]]``

  Will insert the number of shares as human readable number from the shares.count field of facebook

* ``[[+link]]``

  Will insert the full link to the link in the post from the link field of facebook

* ``[[+permalink_url]]``

  Will insert the full link to the facebook post from the permalink_url field of facebook

## Events Snippet

The events snippet allows you to import events from your facebook page.

### Snippet Options
It has several options however the bare minimum you need is the following:

``[[!FB_Events? &page=`<your page id or shortname>`]]``

Further options are:

* ``&limit=`<number>` ``

  This option allows you to define the number of results you wish to be displayed  
  Maximum: 80  
  Default: 5

  Example: ``[[!FB_Events? &page=`<your page id or shortname>` &limit=`6`]]``

* ``&tpl=`<name of the template chunk>` ``

  This option allows you to change the chunk that is used as a template per event

  Example: ``[[!FB_Events? &page=`<your page id or shortname>` &tpl=`myfeedtpl`]]``

* ``&empty_tpl=`<name of the template chunk>` ``

  This option allows you to change the chunk that is used in case there are no current events

  Example: ``[[!FB_Events? &page=`<your page id or shortname>` &empty_tpl=`myemptytpl`]]``

* ``&error_tpl=`<name of the template chunk>` ``

  This option allows you to change the chunk that is used in case the facebook api calls fail

  Example: ``[[!FB_Events? &page=`<your page id or shortname>` &error_tpl=`myerrortpl`]]``


### Template Options

The default template for the snippet some of the fields available. The default template looks like this:

```html
<div class="post">
    <img src="[[+img]]">
    <p>[[+name]] in [[+place_name]]</p>
    <p>[[+description]]</p>
    <p>From [[+start_time]] until [[+end_time]]</p>
    <p>Attending: [[+attending_count]], Maybe: [[+maybe_count]], Declined: [[+declined_count]] and No-Reply: [[+noreply_count]]</p>
</div>
```

* ``[[+img]]``

  Will insert the full url of the picture.url field of facebook

* ``[[+name]]``

  Will insert the name of the event from the name field of facebook

* ``[[+description]]``

  Will insert the description of the event from the description  field of facebook

* ``[[+place_name]]``

  Will insert the name of the place from the place.name field of facebook

* ``[[+place_street]]``

  Will insert the street of the place from the place.location.street field of facebook

* ``[[+place_city]]``

  Will insert the city of the place from the place.location.city field of facebook
  
* ``[[+place_zip]]``

  Will insert the zip code of the place from the place.location.zip field of facebook
  
* ``[[+place_country]]``

  Will insert the country of the place from the place.location.country field of facebook
  
* ``[[+place_latitude]]``

  Will insert the latitude of the place from the place.location.latitude field of facebook
  
* ``[[+place_longitude]]``

  Will insert the longitude of the place from the place.location.longitude field of facebook
  
* ``[[+start_time]]``

  Will insert the **unformatted** start time of the event from the start_time field of facebook

* ``[[+end_time]]``

  Will insert the **unformatted** end time of the event from the end_time field of facebook
  
* ``[[+attending_count]]``

  Will insert the number of people attending as human readable number from the attending_count field of facebook

* ``[[+declined_count]]``

  Will insert the number of people that declined as human readable number from the declined_count field of facebook
  
* ``[[+maybe_count]]``

  Will insert the number of people that said maybe as human readable number from the maybe_count field of facebook
  
* ``[[+noreply_count]]``

  Will insert the number of people that did not reply yet as human readable number from the noreply_count field of facebook


FAQ
---

1. The snippet is not displaying anything

   You probably have an error in your configuration or have not visited the Facebook Feed manager page.  
   If it is still not working have a look at the ModX Error Logs
