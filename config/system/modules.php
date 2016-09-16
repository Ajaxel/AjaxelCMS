<?php
$ret = array();
$ret['content'] = array();
$ret['content']['article'] = array(
	'title'		=> 'Articles',
	'descr'		=> 'Articles, news, blog. Title with description, full body details and thumnbail image',
	'icon'		=> 'apps/accessories-text-editor'
);
$ret['content']['gallery'] = array(
	'title'		=> 'Gallery',
	'descr'		=> 'Multiple media files upload with category, title, description and full body details',
	'icon'		=> 'actions/view-preview'
);
$ret['content']['banner'] = array(
	'title'		=> 'Banners',
	'descr'		=> 'Banners module, gif, flash or custom javascript code',
	'icon'		=> 'actions/stamp',
);
$ret['content']['html'] = array(
	'title'		=> 'HTML',
	'descr'		=> 'Custom HTML insert',
	'icon'		=> 'actions/media-scripts',
);
$ret['content']['form'] = array(
	'title'		=> 'Forms',
	'descr'		=> 'Forms module, generate your custom forms with mouse clicks',
	'icon'		=> 'devices/media-floppy',
);
$ret['content']['product'] = array(
	'title'		=> 'Products',
	'descr'		=> 'Products module, with title, description, full details, price, options, in stock column and etc..',
	'icon'		=> 'devices/input-mouse',
);

$ret['category'] = array();
$ret['category']['gallery'] = array(
	'title'		=> 'Gallery categories',
	'icon'		=> 'places/folder-orange'
);

$ret['grid'] = array();
$ret['grid']['links'] = array(
	'title'		=> 'Links',
	'descr'		=> 'Partners, links. Grid example' 
);

return $ret;