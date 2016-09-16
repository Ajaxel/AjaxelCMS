<?php
/**
* Global configuration
* of Ajaxel CMS script
* Author: Alexander Shatalov <alx@inbox.lv>
* http://ajaxel.com
*/

/**
* Set to true, which is recommended, so your all included javascripts and css files
* will be minified. Use asset() and asset_a() callers for that
*/
define ('MINIFY_JS_CSS', false);

/**
* Minify images to background:url(data:image/png;base64,iVBO...
* To enable write a number of bytes of maximum image size to be minified
* MINIFY_JS_CSS is required
*/
define ('MINIFY_IMAGE', 5210);

/**
* Full HTML cache, for speed.
* Below define time seconds you want the cache to be overwrited
*/
define ('HTML_CACHE', false);
define ('HTML_CACHE_TIME', 7200);

/**
* Timezone set
*/
define ('TIMEZONE_PHP_DIFF', 0); // time in minutes
define ('TIMEZONE_MYSQL_DIFF',0);

define ('TIMEZONE_SET','UTC');
if (function_exists('date_default_timezone_set')) {
	date_default_timezone_set(TIMEZONE_SET);
}

/**
* If true, then file load will be the most minimum.
* Instead of TPL files, system will try to call PHP files
* You may test by typing domain.com/?mini to address
* Useful when you need to get some data taking minimum resources.
*/
define ('SITE_MINI', false);

/**
* Whether not to allow www in address, 
* system will just redirect to url without www. in address
*/
define ('NO_WWW', false);
/**
* Instant messenger settings
* IM_USER_IP - allow to chat between user and ip
* IM_ADMIN_IP - allow to chat between admin and ip
* IM_IP_IP - allow to chat between not authorized users
* IM_IP_BOARD - show IM contact list for not authorized users
*/
define ('IM_USER_IP', true);
define ('IM_ADMIN_IP', true);
define ('IM_IP_IP', false);
define ('IM_IP_BOARD', false);

/**
* Language is being detected in {'text'|lang} for google translate,
* if english letters detected then language will be english otherwise this what you define below:
* Below is the flag to enable auto translation using google translate on new intry insert. Might work slower
*/
define ('USE_TRANSLATE', 'yandex'); // google, yandex, bing
define ('MY_LANGUAGE', 'en');
define ('MY_LOCATION', 'ee');
define ('USE_AUTO_TRANSLATE', false);

/**
* Set true if you want simple way adding content and multilevel menus without modules.
* otherwise as first version, you can add many pages and different modules under content block under 2-level menus
*/
define ('USE_TREE', false);

/**
* CMS has 2 versions of orders displaying in admin panel
* First is without basket, very simple one, and another is full ecommerce feature
*/
define ('ORDERS_FULL', true);

/**
* Enable if you want to use basket in your store, system is clearing old basket data automatically
* and includes basket class on "add to basket" demand
*/
define ('USE_BASKET', false);

/**
* Whether to use IP blocker or not
*/
define ('USE_IPBLOCKER', true);

/**
* Whether to save user clicks or not, and number of made user clicks to ignore saving clicks after
*/
define ('USE_CLICKS', 0); // 500
define ('CLICKS_DAYS', 730); // 2 years

/**
* Whether to save user searches or not, and number of made user clicks to ignore saving searches after
*/
define ('USE_SEARCHES', 50000); // 500
define ('SEARCHES_DAYS', 730); // 2 years
define ('SEARCHES_GROUP', true); // if true, then will work as keywords

/**
* Whether to calculate downloads 
* 0 - does not save
* 1 - saves file downloads and groups by file url
* 2 - saved file downloads everytime, includes all statistics
*/
define ('USE_DOWNLOADS', 1);
define ('DOWNLOADS_DAYS', 730); // 2 years

/**
* Whether to calculate external url redirects 
* 0 - does not save
* 1 - saves links and groups by link url
* 2 - saved links everytime, includes all statistics
*/
define ('USE_EXTERNAL', 1);
define ('EXTERNAL_DAYS', 730); // 2 years

/**
* Searches settings
* key_id => name, minimum length, label, use with group in admin
*/

$_conf['searches'] = array(
	1 	=> array('search',3,'Search'),
	2	=> array('find',3,'Find'),
	2	=> array('keywords',3,'Keywords'),
);

/**
* If enabled then system will generate <script src="?js&stats.."></script> tag,
* this will definately strictly calculate statistics, all robots will be ignored since they can't read javascript.
* If disabled, then system will use only known robots to ignore
*/
define ('JS_STATS', true);

/**
* whether to get everything after ? sign in URL as domain.com/pagename/page/?foo=bar&bar=foo
* otherwise /pagename/page/ will be ignored if ? sign appears in URL which represents QUERY_STRING
*/
define ('URL_GET_ALL', true);

/**
* Whether to redirect from ssl no normal URL
*/
define ('SSL_TO_NORMAL', true);
/**
* Minium username and password length
*/
define ('USERNAME_MIN_LENGTH',3);
define ('PASSWORD_MIN_LENGTH',3);
/**
* When there are no users registered or you want to login without being registered,
* then here is hack username and password for logging to admin area and to become super administrator
*/
define ('ADMIN_LOGIN', '');
define ('ADMIN_PASSWORD', '');

/**
* Super admin user ID, used in mod/Allow.php class
*/
define ('SUPER_ADMIN', 1);

/**
* Some user group ids, see $_conf['user_groups'] below for understanding
*/
define ('ADMIN_GROUP', 4);
define ('BOT_GROUP', 6);
define ('DEMO_GROUP', 7);

/**
* Session lifetime in seconds, for how long you want the session to be saved 
* when users didnt refresh or click any link on the website
*/
define ('SESSION_LIFETIME', 7200);

/**
* Session data can be cleaned by every user in a times you define below
* Number 3 will clean old session data in 3 link clicks or page refreshes
*/
define ('SESSION_CLEAN_CLICKS', 3);

/*
* Whether to allow multi user login, eg. 2 or more users can be logged in one session
* please note: everybody in DEMO_GROUP are allowed to have multi session always.
*/
define ('SESSION_MULTI', true);

/**
* If you feel that your website is being hacked or pinged a lot
* then write here time in seconds within session ID shouln't be renewed relating to one IP address
*/
define ('ANTIHACK_SECONDS', 0);

/**
* System forbids for both editors/administrators to edit same content/entry,
* this value here is a time in seconds which should be less than SESSION_LIFETIME for sure 
* and this wil do so that if admin I is tryiung to edit same entry which admin II is editing already
* and admin II is editong it for less than EDIT_LIFETIME seconds (for example 5 minutes) then admin I 
* who came to edit will see a notification about so that same article is already being edited by another admin.
* This is cool, when one website manages many administrators, editors, moderators. 
* So, admin II doesn't want to be disappointed if admin I changes his already posted article.
*/
define ('EDIT_LIFETIME', 5 * 60);

/**
* Time in days. Cookies are basically used for username saving which system does already and password by demand.
* COOKIE_DOMAIN can be empty and path can be a slash
*/
define ('COOKIE_VARS', true); // if you want users to save language, template, region, currency to cookies
define ('COOKIE_LIFETIME', 30);
define ('COOKIE_DOMAIN', '');
define ('COOKIE_PATH', '/');

/**
* Prints out the comment at the bottom of the page
* showing time in seconds how long page was generated, amount of database connections and etc..
* 0 - disabled
* 1 - enabled for administrator
* 2 - enabled for everyone
*/
define ('SHOW_END', 2);

/**
* Database first SQL query
* executes after DB connection
* leave empty to disable, "SET NAMES 'utf8'" - is default
*/
define ('DB_SET','SET NAMES \'utf8\'');

/**
* This cache is basically for SHOW COLUMNS FROM tablename; and SHOW TABLES; query data saving
* Because these 2 queries are called all the time to check what columns are in  each table
* and these queries are kind a slow to run..so enable this feature,
* you can always reset cache by URL domain.com/admin/reset 
*/
define ('DB_CACHE', true);

/**
* Prints out all runned queries at the bottom of page
* 0 - disabled
* 1 - enabled for administrator
* 2 - enabled for everyone
*/
define ('DB_SQL', 0); 
/**
* If you want to highlight SQL queries when printing them
* Takes some time to generate them, this is why disabled by default.
*/
define ('DB_SQL_HL', false);

/**
* Whether to print debug backtrace in error messages or not
*/
define ('DEBUG_BACKTRACE', 1);

/**
* This should be true if you want to run friendly URLs
* Important is so that server works with .htaccess files
*
* Uncomment this if you want to set to auto, this condition sometimes doesn't work depending from hostings
* define ('HTACCESS_WRITE', (strpos(php_sapi_name(),'cgi')===false && strpos(php_sapi_name(),'apache2filter')===false && strpos(php_sapi_name(),'isapi')===false));
*/
define ('HTACCESS_WRITE', true);


/**
* jQuery script files
* For backend and frontend areas. Sometimes jQuery might change the version
* But be very careful updating jQuery version, last time i tried to update and tabs in window where WYSIWYG present didn't work..
*/

define ('JQUERY','jquery-1.11.1.min.js');
define ('JQUERY_MIGRATE','jquery-migrate-1.2.1.min.js');
define ('JQUERY_UI','jquery-ui.min.js');
define ('JQUERY_CSS','jquery-ui-1.10.3.custom.css');

define ('JQUERY_UPLOADIFY','jquery.uploadify.v2.1.4.min.js');
define ('JQUERY_COLORBOX', 3);
define ('WYSIWYG','tinymce'); // ckeditor, tinymce3, tynymce4, nicEdit


/**
* Login anti-floud enabling flag
*/
define ('FLOUD_ENABLED', true);
/**
* If flagged then system will allow user to type
* different username if he forgot password and username.
* Otherwise, system will block from authorization user
* for FLOUD_SLEEP_SECONDS after FLOUD_TIMES wrong password submitting
*/
define ('FLOUD_LOGIN', true);
/**
* Time in seconds you want user to wait before he can authorize again 
*/
define ('FLOUD_SLEEP_SECONDS', 120);
/**
* Number of wrong password
*/
define ('FLOUD_TIMES', 5);
/**
* If a number then system will auto block this visitor by his IP address
*/
define ('FLOUD_BLOCK_TIMES', 40);
/**
* If you want to save user login/logout enabe this by entering amount of days to save in history
* or 0 to disable
*/
define ('USE_AUTH_LOG', 0); // 30

/**
* Wher form is submitted by ajax form, how many milliseconds to delay before redirect if URL::redirect() is called after.
* Called when user registers on site for example.
* Enter 0 to disable this feature.
*/
define ('REDIRECT_AJAX_TIME', 500);

/**
* I assume you might be interested to have your own URL key names
*/
define ('URL_VALUE','='); // in URL replaces "=" to any wanted character. Examples: -,._= or it also may use many characters: -- or ♀,♪,º and etc..
define ('URL_SPACE','-'); // for url values to convert

define ('URL_KEY_HOME','home');
define ('URL_KEY_ADMIN','cms');
define ('URL_KEY_LANG','lang');
define ('URL_KEY_CURRENCY','currency');
define ('URL_KEY_REGION','region');
define ('URL_KEY_TEMPLATE','template');
define ('URL_KEY_TEMPLATE_ADMIN', 'template_admin');
define ('URL_KEY_UI_ADMIN','ui_admin');
define ('URL_KEY_JUMP','jump');
define ('URL_KEY_RESET','reset');
define ('URL_KEY_REFERAL','rf');
define ('URL_KEY_DO','do');
define ('URL_KEY_DB','db');
define ('URL_KEY_EMAIL','email');
define ('URL_KEY_EMAIL_CONFIRM','email_confirm');
define ('URL_KEY_EMAIL_LOGIN', 'email_login');
define ('URL_KEY_CONTENT','content');
define ('URL_KEY_LOGIN','login');
define ('URL_KEY_LOGOUT','logout');
define ('URL_KEY_PASSWORD','password');
define ('URL_KEY_REMEMBER','remember');
define ('URL_KEY_NOTMYPC','notmypc');
define ('URL_KEY_REGISTER','register');
define ('URL_KEY_LOSTPASS','lostpass');
define ('URL_KEY_PAGE','page');
define ('URL_KEY_P','p');
define ('URL_KEY_LIMIT','limit');
define ('URL_KEY_FOLDER','folder');
define ('URL_KEY_TO','to');
define ('URL_KEY_CATID','cid');
define ('URL_KEY_ONLINE','online');
define ('URL_KEY_ACTION','action');
define ('URL_KEY_PROD_OPTION', 'prod_opt');
define ('URL_KEY_POST_IGNORE','_post_ignore');

/*
* URLs keys to ignore from 404
* Skips the URL key in address. 
* For example "www.domain.com/?logout" will no longer display any page named "logout" and either 404
* Note, also you wont be able to create menu with url name: "logout" and etc..
*/
$_conf['skip_url'] = array(
	URL_KEY_LOGOUT,
	URL_KEY_DO,
	URL_KEY_LANG,
	URL_KEY_REMEMBER,
	URL_KEY_NOTMYPC,
	URL_KEY_TEMPLATE,
	URL_KEY_CURRENCY,
	URL_KEY_TEMPLATE_ADMIN,
	URL_KEY_REFERAL,
	URL_KEY_PASSWORD,
	URL_KEY_DB,
	URL_KEY_RESET,
	URL_KEY_PAGE,
	'rss.xml','mini','site','mobile',
	'_','-','r','reset_all','ref','a',
	'switch','act','msg',
	'userid_admin','to',
	'emailclick',
	'fav_user','unfav_user','fav_ad','unfav_ad',
	'fb_action_ids','fb_action_types','fb_source','fb_aggregation_id',
	'yclid','gclid',
	'utm_source','utm_medium','utm_campaign','utm_content','utm_term'
);

/**
* Emotions for AI chat bot
*/
$_conf['emotions'] = array(
	0	=> 'Normal',
	1	=> 'Smiles',
	2	=> 'Surprised',
	3	=> 'Excited',
	4	=> 'Wonders',
	5	=> 'Interested',
	6	=> 'Happy',
	7	=> 'Angry',
	8	=> 'Annoyed',
	9	=> 'Confused',
	10	=> 'Sad',
	11	=> 'Bored'
);
/**
* These are the most important values in system.
* System file/template/engine/requests switch works based on them.
* For quick understanding here is an example: for value "popup" you can no longer to call page names, 
* menu names and content names because popup is reserved and this will not work as index because system will
* call Index()->printPopup() in 'mod/Mainarea.php' file instead. "popup" will be set SITE_TYPE constant if you open domain.com/popup
* Every site type can have their own content and headers
*/
$_conf['site_types'] = array(
	 'index'
	 ,'popup'
	 ,'print'
	 ,'window'
	 ,'ajax'
	 ,'json'
	 ,'loop'
	 ,'xml'
	 ,'rss'
	 ,'js'
	 ,'upload'
	 ,'download'
	 ,'pdf'
	 ,'th'
);
/**
* User groups can be many, user group shuld have an id, ID is the index of array below.
* Every user group can have different role for accessing to admin area or even in puplic area
* All roles are configured in 'mod/Allow.php' file.
*/
$_conf['user_groups'] = array(
	0	=> 'Visitor',
	1	=> 'User',
	2	=> 'Moderator',
	3	=> 'Editor',
	4	=> 'Administrator',
	5	=> 'VIP',
	6	=> 'Bot',
	7	=> 'Demo'
);
/**
* System has additional value per each user, works same as user group
* But this is User->ClassID value, which can be used for price changes and other..
*/

$_conf['user_classes'] = array(
	0	=> 'No class',
	1	=> 'Beginner',
	2	=> 'Intermediate',
	3	=> 'Advanced',
	4	=> 'Super',
	5	=> 'Godlike'
);

/**
* User statuses. No need to describe
*/
$_conf['user_statuses'] = array(
	Site::ACCOUNT_DEACTIVATED 	=> array('Inactive', 'status/object-locked.png'), // 1
	Site::ACCOUNT_ACTIVE 		=> array('Active', 'actions/mail-flag-kmail.png'),
	Site::ACCOUNT_DELETED 		=> array('Deleted', 'status/user-trash-full.png'),
	Site::ACCOUNT_PENDING 		=> array('Pending', 'status/dialog-warning.png'),
	Site::ACCOUNT_BANNED 		=> array('Banned', 'status/user-away.png'),
	Site::ACCOUNT_CONFIRM 		=> array('Email confirm', 'status/user-away.png'),
	Site::ACCOUNT_AWAY 			=> array('Away', 'status/user-away.png'),
	Site::ACCOUNT_BUSY 			=> array('Busy', 'status/user-busy.png'),
	Site::ACCOUNT_INVISIBLE		=> array('Invisible', 'status/user-invisible.png')
);

/**
* Order statuses
*/
$_conf['order_statuses'] = array(
	Site::STATUS_NOT_PAID	=> array('Not paid','red'),
	Site::STATUS_PAID		=> array('Paid','green'),
	Site::STATUS_ACCEPTED	=> array('Accepted','maroon'),
	Site::STATUS_CANCELLED	=> array('Cancelled','grey'),
	Site::STATUS_SENT		=> array('Sent','blue'),
	Site::STATUS_REFUNDED	=> array('Refunded','purple'),
	Site::STATUS_CREDIT		=> array('Credit','magenta'),
	Site::STATUS_ERROR		=> array('Error','black')
);

/**
* Order statuses to determine whether order was paid. These statuses should be paid,accepted and sent
*/
$_conf['order_statuses_ok'] = array(
	Site::STATUS_PAID,
	Site::STATUS_ACCEPTED,
	Site::STATUS_SENT
);

/**
* System may have many types of basket items.
* For quick understanding if order item is a license then there will be no need for shipping
*/
$_conf['order_types'] = array(
	Site::ORDER_TYPE_PRODUCT	=> array('Product'),
	Site::ORDER_TYPE_SERVICE	=> array('Service'),
	Site::ORDER_TYPE_ACTIVATION	=> array('Activation'),
	Site::ORDER_TYPE_DOWNLOAD	=> array('Download'),
	Site::ORDER_TYPE_LICENSE	=> array('License')
);

/**
* Allowed rateing tables, remove this variable to allow from all,
* For security reasons this variable must exist, or else someone 
* might to hack you and fill a lot of different votes to your database.
* These are not just tables, but types (names).
* Made for using with jRating: <div id="dj_logo-{$row.id}" data-rate="{$row.dj_logo_rate}" where dj_logo is a table
*/
$_conf['rate_tables'] = array(
	'video'
);

/**
* Array of global tables which should not be prefixed by template prefix
*/
$_conf['global_tables'] = array (
	'abuse',
	'lang',
	'estimate',
	
	'users',
	'users_profile',
	'users_video',
	'users_acc',
	'users_wall',
	'users_assistant',
	'users_transfers',
	'users_fav',
	'users_friends',
	'users_pay',
	'users_trans',
	'users_aff',
	'users_aff_list',
	
	'help_contents',
	'help_board',
	
	'sessions',
	'wall',
	'wall_replies',
	'wall_hides',
	'wall_likes',
	'mail',
	'mail_templates',
	'im',
	'im_sub',
	'im_set',
	'countries',
	'templates',
	'langs',
	'logins',
	'vars',
	'votes',
	'log',
	'emails',
	'emails_read',
	'emails_camp',
	'emails_sent',
	'settings',
	'ipblocker',
	'spamip',
	
	'db',
	'orders2',
	'orders2_map',
	'orders2_basket',
	'orders2_msg',
	'snippets',
	
	'crm_clients',
	'crm_tasks',
	'crm_tasks_map',
	'crm_log',
	'crm_mailtpl',
	'crm_users',
	'crm_groups',
	
	'geo_countries',
	'geo_states',
	'geo_cities',
	'geo_districts',
	
	'poker_tables',
	'poker_players',
	
	'spamip'
);
						
/**
* Columns which should not be wrapped by tags for quick editing
*/
$_conf['not_editable_cols'] = array(
	'id',
	'rid',
	'is',
	'setid',
	'country',
	'menuid',
	'menuids',
	'lang',
	'alt',
	'keywords',
	'main_photo',
	'userid',
	'views',
	'comment',
	'comments',
	'viewtime',
	'url',
	'comments',
	'bodylist',
	'active',
	'sort',
	'bestseller',
	'main_page',
	'options',
	'videos',
	'notes',
	'catref',
	'is_admin',
	'added',
	'edited',
	'submenus',
	'display',
	'parentid',
	'position',
	'cnt',
	'cnt2',
	'sum',
	'name',
	'module',
	'table',
	'price',
	'price_old',
	'currency',
	'expires',
	'statused',
	'statuser',
	'editor',
	'source',
	'youtube'
);


/**
* Some admin configuration
*/
define ('ADMIN_LIMIT', 50);
define ('USE_LOG', 90); // log days, put 0 to disable log. 
define ('LOG_MISSING', false); // if you want to log missing files called from site, enable this
define ('REFERALS_PER_DAY', 0); // maximum amount of income referals per day to count
//define ('VISUAL_TAGS','');
define ('VISUAL_TAGS', '(div|ol|ul|dl|table|h1|h2|h3)');
define ('DEFAULT_CONTENT','article'); // use empty for all
define ('NO_DELETE', false); // if you want dont want to delete content on delete press button, will set status 2
define ('NO_DEL_CONFIRM', false); // need a confirmation on delete button press? For those what are going to log
define ('LAT_MENU_NAME', true);
define ('LAT_PAGE_NAME', true);
define ('WIN_OPEN_IN_POPUP', false);
define ('EDIT_NOTIFY',false); // whether you want to display success messages in admin after updating
define ('TOTAL_YEARS', 90);
define ('UPLOAD_SECONDS', 3600);
define ('INT_NEW_CATEGORIES', 4);
define ('ADMIN_NAV_BUTTONS',10);
define ('PAGEBREAK', '<!-- pagebreak -->');
define ('USE_MATHLIB', true);
define ('IP_LOOKUP', 'http://ip-lookup.net/?ip=%s');
define ('TEMPLATE_ADMIN', 'default'); // admin panel's template
define ('USE_ADMIN_UI', true); // in case if you want to customize admin panel's design fully
define ('ADMIN_SHOW_RSS', true);
define ('STRIP_LINK_START',20);
define ('STRIP_LINK_END',14);
define ('FLAG_ALL_LANG',true);
define ('IMAGE_TRANSFORM_LIB_PATH','/usr/bin/');
define ('FTP_DIR_IMAGICK',IMAGE_TRANSFORM_LIB_PATH);
define ('ADMIN_QRY_HIGH_SECURITY', true); // in settings -> qry tab, whether to forbid not super admin to run (insert,drop and etc..)
define ('ENABLE_LANG_SAVE', true);

/**
* File configs
* Flag to convert uploaded files to latin from cyrillic
*/
define ('CONV_FILES_LATIN',false);
define ('FTP_DIR_SECURE','../protected/');
define ('FTP_DIR_PHPTEMP','../../tmp');
define ('PHPTEMP_PATTERN','*\.php');

/**
* Email config
*/
define ('EMAIL_CHARSET','UTF-8');
define ('EMAIL_PARAMS',NULL);
//define ('EMAIL_PARAMS','-f www-data@www.example.com');
define ('EMAIL_PIXEL', true);
define ('EMAIL_CLEAN',true);
define ('EMAIL_URLS',true);
define ('EMAIL_SAVE',true);
define ('EMAIL_STYLE','');

// headers to add for mail()
$_conf['email_headers'] = array(
	//'Received: from ajaxel [192.168.10.27]'
);

// additional domains to add pixel for campaigns
$_conf['email_domains'] = array(
//	'my_another_site1.com',
//	'my_another_site2.com',
);



/**
* Snippets Documentation:

	To call a snippet with passed arguments:
		[!snippet_function|argument1|argument2!]
		where:
		$this->snippet['args'] = array('argument1','argument2');
	
	To parse big content using snippet:
		[!snippet_function|argument1|argument2!]
			some text
		[/!snippet_function!]
	where:
		$this->snippet['args'] = array('argument1','argument2');
		$this->snippet['text'] = 'some text';

	To get value from database:
		[#table|id|column#]
		for example: [#users_profile|1|firstname#] or better: [#users_profile|1|CONCAT(firstname,' ',lastname)#]
	
	To get session value:
		[!Session->Login!]
		[!Session->profile->firstname!] [!Session->profile->age!]
		
	To parse Smarty code:
		[!smarty!]
			<b>{'Hello %1!'|lang:$User.profile.firstname}</b>
		[!/smarty!]
		
* You can use snippets everywhere in texts, just write with body.
* Note, snippets are being parsed when `is_admin` is set to 1 in database entry row,
* so administrators may use snippets only, no worries.
*/

/**
* Language translation and variables documentation:
	To translate language phrase with variables:
		<?php
			echo lang('Hello %1, nice to see you!', Session()->Login);
		?>
		where %1, %2.. are the replacemdents

	To make a number human-redable:
		<?php
			echo lang('%1 {number=%1,item,items,предмета}', 5); // will represent "5 items", 3rd argument for russians when 2-4
		?>

	To get a variable:
		<?php
			echo lang('#variable_name');
		?>
		
	To escape editing when admin:
		<input value="<?php echo lang('_Admin cannot edit here');?>">
		
	To enable rich-text-editor instead of simple textareas for lang:
		<?php
			echo lang('@HTML text');
		?>
		
	As you noticed: @,_,# is first character for lang. $ - character for admin translations
	These characters can be combined such as lang('_#variable name');
*/
