<?php

abstract class Stats_DB {
	public static function Browsers($toid=true) {
		return array(
		'msie'							=> 'IE',
		'microsoft internet explorer'	=> 'IE',
		'internet explorer'				=> 'IE',
		'netscape6'						=> 'NS',
		'netscape'						=> 'NS',
		'chrome'						=> 'CR',
		'galeon'						=> 'GA',
		'phoenix'						=> 'PX',
		'firefox'						=> 'FF',
		'mozilla firebird'				=> 'FB',
		'firebird'						=> 'FB',
		'seamonkey'						=> 'SM',
		'chimera'						=> 'CH',
		'camino'						=> 'CA',
		'safari'						=> 'SF',
		'k-meleon'						=> 'KM',
		'mozilla'						=> 'MO',
		'opera'							=> 'OP',
		'konqueror'						=> 'KO',
		'icab'							=> 'IC',
		'lynx'							=> 'LX',
		'links'							=> 'LI',
		'ncsa mosaic'					=> 'MC',
		'amaya'							=> 'AM',
		'omniweb'						=> 'OW',
		'hotjava'						=> 'HJ',
		'browsex'						=> 'BX',
		'amigavoyager'					=> 'AV',
		'amiga-aweb'					=> 'AW',
		'ibrowse'						=> 'IB',
		'unknown'						=> 'un'
		);
	}
	
	public static function OS ($toid = true) {
		return array(
		'Windows NT 6.1' => 'WVI',
		'Windows NT 6.0' => 'WVI',
		'Windows Vista'  => 'WVI',
		'Windows NT 5.2' => 'WS3',
		'Windows Server 2003' => 'WS3',
		'Windows NT 5.1' => 'WXP',
		'Windows XP'     => 'WXP',
		'Win98'          => 'W98',
		'Windows 98'     => 'W98',
		'Windows NT 5.0' => 'W2K',
		'Windows 2000'   => 'W2K',
		'Windows NT 4.0' => 'WNT',
		'WinNT'          => 'WNT',
		'Windows NT'     => 'WNT',
		'Win 9x 4.90'    => 'WME',
		'Win 9x 4.90'    => 'WME',
		'Windows Me'     => 'WME',
		'Win32'          => 'W95',
		'Win95'          => 'W95',		
		'Windows 95'     => 'W95',
		'Mac_PowerPC'    => 'MAC', 
		'Mac PPC'        => 'MAC',
		'PPC'            => 'MAC',
		'Mac PowerPC'    => 'MAC',
		'Mac OS'         => 'MAC',
		'Linux'          => 'LIN',
		'SunOS'          => 'SOS', 
		'FreeBSD'        => 'BSD', 
		'AIX'            => 'AIX', 
		'IRIX'           => 'IRI', 
		'HP-UX'          => 'HPX', 
		'OS'	         => 'OS2', 
		'NetBSD'         => 'NBS',
		'Unknown'        => 'UNK' 
		);
	}
	
	
	public static function robots() {
		return array(
			'Google' => array('Googlebot','Mediapartners-Google'),
			'Mail.Ru'=> array('Mail.Ru'),
			'Excita' => array('ArchitextSpider'), 
			'Altavista' => array('Scooter','vscooter','Mercator','AltaVista-Intranet'), 
			'Lycos' => array('Lycos_Spider'), 
			'Northern Light' => array('Gulliver'), 
			'AllTheWeb' => array('FAST-WebCrawler','fastlwspider','Wget'), 
			'Inktomi' => array('Slurp'),
			'Teoma' => array('teoma_agent1'),
			'Yandex' => array('Yandex'),
			'Yahoo' => array('YahooSeeker'),
			'Abacho' => array('AbachoBOT'),
			'Abcdatos' => array('abcdatos_botlink'),
			'Aesop' => array('AESOP_com_SpiderMan'),
			'Ah-ha' => array('ah-ha.com crawler'),
			'Alexa' => array('ia_archiver'),
			'Acoon' => array('Acoon Robot'),
			'Antisearch' => array('antibot'),
			'Atomz' => array('Atomz'),
			'Buscaplus' => array('Buscaplus Robi'),
			'CanSeek' => array('CanSeek'),
			'ChristCrawler' => array('ChristCRAWLER'),
			'Crawler' => array('Crawler'),
			'Daadle' => array('DaAdLe.com ROBOT'),
			'Daum' => array('RaBot'),
			'DeepIndex' => array('DeepIndex'),
			'Ditto' => array('DittoSpyder'),
			'Domanova' => array('Jack'),
			'Entire web' => array('Speedy Spider'),
			'Euroseek' => array('Arachnoidea'),
			'EZResults' => array('EZResult'),
			'Fast search' => array('Fast PartnerSite Crawler','FAST Data Search Crawler'),
			'Fireball' => array('KIT-Fireball'),
			'Fyber search' => array('FyberSearch'),
			'Galaxy' => array('GalaxyBot'),
			'Geckobot' => array('geckobot'),
			'GenDoor' => array('GenCrawler'),
			'Geona' => array('GeonaBot'),
			'Goo' => array('moget/2.0'),
			'Girafa' => array('Aranha'),
			'Hoppa' => array('Toutatis'),
			'Hubat' => array('Hubater'),
			'IlTrovatore' => array('IlTrovatore-Setaccio'),
			'IncyWincy' => array('IncyWincy'),
			'InTags' => array('Mole2'),
			'MP3Bot' => array('MP3Bot'),
			'IP3000' => array('ip3000.com-crawler'),
			'Kuloko' => array('kuloko-bot'),
			'Lexis-Nexis' => array('LNSpiderguy'),
			'Look' => array('NetResearchServer'),
			'Look smart' => array('MantraAgent'),
			'Loop improvements' => array('NetResearchServer'),
			'Joocer' => array('JoocerBot'),
			'Mirage' => array('HenryTheMiragoRobot'),
			'mozDex' => array('mozDex'),
			'MSN' => array('MSNBOT'),
			'Northern light' => array('Gulliver'),
			'Objects Search' => array('ObjectsSearch'),
			'Pico Search' => array('PicoSearch'),
			'Portal Juice' => array('PJspider'),
			'Maxbot' => array('Spider/maxbot.com'),
			'National directory' => array('NationalDirectory-SuperSpider'),
			'Naver' => array('NaverRobot'),
			'OpenFind' => array('Openfind piranha','Openbot'),
			'Pic search' => array('psbot'),
			'PinPoint' => array('CrawlerBoy Pinpoint.com'),
			'Search hippo' => array('Fluffy the spider'),
			'Scrub the Web' => array('Scrubby'),
			'SingingFish' => array('asterias'),
			'SpeedFind' => array('speedfind ramBot xtreme'),
			'Kototoi' => array('Kototoi'),
			'SearchSpider' => array('Searchspider'),
			'SightQuest' => array('SightQuestBot'),
			'SpiderMonkey' => array('Spider_Monkey'),
			'Spider' => array('Spider'),
			'Surf-no-more' => array('Surfnomore Spider'),
			'Teradex Mapper' => array('Teradex_Mapper'),
			'Travel finder' => array('ESISmartSpider'),
			'TraficDublu' => array('Spider TraficDublu'),
			'Tutorgig' => array('Tutorial Crawler'),
			'UK Searcher' => array('UK Searcher Spider'),
			'Vivante' => array('Vivante Link Checker'),
			'Walhello' => array('appie'),
			'Websmostlinked.com' => array('Nazilla'),
			'WebTop' => array('MuscatFerret'),
			'WiseNut' => array('ZyBorg'),
			'World search center' => array('WSCbot'),
			'Yellow pet' => array('Yellopet-Spider'),
			'W3C validator' => array('W3C_Validator'),
			'Tooter' => array('Tooter'),
			'Alligator' => array('Alligator'),
			'BatchFTP' => array('BatchFTP'),
			'ChinaClaw' => array('ChinaClaw'),
			'Download accelerator' => array('DA'),
			'NetZIP' => array('Download Demon','NetZip Downloader','SmartDownload'),
			'Download Master' => array('Download Master'),
			'Download Ninja' => array('Download Ninja'),
			'Download Wonder' => array('Download Wonder'),
			'Ez Auto Downloader' => array('Ez Auto Downloader'),
			'FreshDownload' => array('FreshDownload'),
			'Go!Zilla' => array('Go!Zilla'),
			'GetRight' => array('GetRight'),
			'GetSmart' => array('GetSmart'),
			'HiDownload' => array('HiDownload'),
			'FlagGet' => array('JetCar','FlashGet'),
			'Kapere' => array('Kapere'),
			'Kontiki' => array('Kontiki Client'),
			'LeechFTP' => array('LeechFTP'),
			'LeechGet' => array('LeechGet'),
			'LightningDownload' => array('LightningDownload'),
			'Mass Downloader' => array('Mass Downloader'),
			'MetaProducts' => array('MetaProducts'),
			'NetAnts' => array('NetAnts'),
			'NetButler' => array('NetButler'),
			'NetPumper' => array('NetPumper'),
			'Net Vampire' => array('Net Vampire'),
			'Nitro Downloader' => array('Nitro Downloader'), 
			'Octopus' => array('Octopus'), 
			'PuxaRapido' => array('PuxaRapido'),
			'RealDownload' => array('RealDownload'), 
			'SpeedDownload' => array('SpeedDownload'), 
			'WebDownloader' => array('WebDownloader'),
			'WebLeacher' => array('WebLeacher'), 
			'WebPictures' => array('WebPictures'), 
			'X-Uploader' => array('X-Uploader'),
			'DigOut4U' => array('DigOut4U'), 
			'DISCoFinder' => array('DISCoFinder'), 
			'eCatch' => array('eCatch'),
			'EirGrabber' => array('EirGrabber'), 
			'ExtractorPro' => array('ExtractorPro'), 
			'FairAd' => array('FairAd Client'),
			'iSiloWeb' => array('iSiloWeb'), 
			'Kenjin' => array('Kenjin Spider'), 
			'MS IE 4.0' => array('MSIECrawler','MSProxy'),
			'NexTools' => array('NexTools'), 
			'Offline Explorer' => array('Offline Explorer'), 
			'NetAttache' => array('NetAttache'),
			'PageDown' => array('PageDown'), 
			'ParaSite' => array('ParaSite'), 
			'Searchworks' => array('Searchworks Spider'),
			'SiteMapper' => array('SiteMapper'), 
			'SiteSnagger' => array('SiteSnagger'), 
			'SuperBot' => array('SuperBot'),
			'Teleport Pro' => array('Teleport Pro'), 
			'Web2Map' => array('Web2Map'), 
			'WebAuto' => array('WebAuto'),
			'WebCopier' => array('WebCopier'), 
			'Webdup' => array('Webdup'), 
			'WebFetch' => array('WebFetch'),
			'WebReaper' => array('WebReaper'),
			'Webrobot' => array('Webrobot'),
			'Website eXtractor' => array('Website eXtractor'),
			'WebSnatcher' => array('WebSnatcher'),
			'WebStripper' => array('WebStripper'),
			'WebTwin' => array('WebTwin'),
			'WebVCR' => array('WebVCR'),
			'WebZIP' => array('WebZIP'),
			'World Wide Web Offline Explorer' => array('WWWOFFLE'),
			'Xaldon' => array('Xaldon WebSpider')
		);
	}
	
	public static function searchEngines() {
	
		return array(
			'Mail.Ru' => array('Mail.Ru','q'),
			//1
			'1.cz' => array('1.cz','q','iso-8859-2'),
			'1.cz' => array('1.cz','q','iso-8859-2'),
			//1und1
			'portal.1und1.de' => array('1und1','search'),
			//3271
			'nmsearch.3721.com' => array('3271','p'),
			'seek.3721.com' => array('3271','p'),
			//A9
			'a9.com' => array('A9',''),
			'a9.com' => array('A9',''),
			//Abacho
			'search.abacho.com' => array('Abacho','q'),
			//about
			'search.about.com' => array('About','terms'),
			//Acoon
			'acoon.de' => array('Acoon','begriff'),
			//Acont
			'acont.de' => array('Acont','query'),
			//Alexa
			'alexa.com' => array('Alexa','q'),
			'alexa.com' => array('Alexa','q'),
			//AliceAdsl
			'rechercher.aliceadsl.fr' => array('AliceAdsl','qs'),
			//Allesklar
			'allesklar.de' => array('Allesklar','words'),
			//AllTheWeb
			'alltheweb.com' => array('AllTheWeb','q'),
			//all.by
			'all.by' => array('All.by','query'),
			//Altavista
			'listings.altavista.com' => array('AltaVista','q'),
			'altavista.de' => array('AltaVista','q'),
			'altavista.fr' => array('AltaVista','q'),
			'de.altavista.com' => array('AltaVista','q'),
			'fr.altavista.com' => array('AltaVista','q'),
			'altavista.fr' => array('AltaVista','q'),
			'search.altavista.com' => array('AltaVista','q'),
			'search.fr.altavista.com' => array('AltaVista','q'),
			'se.altavista.com' => array('AltaVista','q'),
			'be-nl.altavista.com' => array('AltaVista','q'),
			'be-fr.altavista.com' => array('AltaVista','q'),
			'it.altavista.com' => array('AltaVista','q'),
			'us.altavista.com' => array('AltaVista','q'),
			'nl.altavista.com' => array('Altavista','q'),
			'ch.altavista.com' => array('AltaVista','q'),
			'altavista.com' => array('AltaVista','q'),
			//APOLLO7
			'apollo7.de' => array('Apollo7','query'),
			'apollo7.de' => array('Apollo7','query'),
			//AOL
			'aolrecherche.aol.fr' => array('AOL','q'),
			'aolrecherches.aol.fr' => array('AOL','query'),
			'aolimages.aol.fr' => array('AOL','query'),
			'recherche.aol.fr' => array('AOL','q'),
			'aolsearch.aol.com' => array('AOL','query'),
			'aolsearcht.aol.com' => array('AOL','query'),
			'find.web.aol.com' => array('AOL','query'),
			'recherche.aol.ca' => array('AOL','query'),
			'aolsearch.aol.co.uk' => array('AOL','query'),
			'search.aol.co.uk' => array('AOL','query'),
			'aolrecherche.aol.fr' => array('AOL','q'),
			'sucheaol.aol.de' => array('AOL','q'),
			'suche.aol.de' => array('AOL','q'),
			'aolbusqueda.aol.com.mx' => array('AOL','query'),
			'search.aol.com' => array('AOL','query'),
			//Aport
			'sm.aport.ru' => array('Aport','r'),
			//Arcor
			'arcor.de' => array('Arcor','Keywords'),
			//Arianna(Libero.it)
			'arianna.libero.it' => array('Arianna','query'),
			//Ask
			'web.ask.com' => array('Ask','ask'),
			'ask.co.uk' => array('Ask','q'),
			'uk.ask.com' => array('Ask','q'),
			'fr.ask.com' => array('Ask','q'),
			'de.ask.com' => array('Ask','q'),
			'es.ask.com' => array('Ask','q'),
			'it.ask.com' => array('Ask','q'),
			'nl.ask.com' => array('Ask','q'),
			'ask.jp' => array('Ask','q'),
			'ask.com' => array('Ask','ask'),
			//Atlas
			'search.atlas.cz' => array('Atlas','q','windows-1250'),
			//Baidu
			'www1.baidu.com' => array('Baidu','wd'),
			'baidu.com' => array('Baidu','wd'),
			//Bellnet
			'suchmaschine.com' => array('Bellnet','suchstr'),
			//Biglobe
			'cgi.search.biglobe.ne.jp' => array('Biglobe','q'),
			//Bluewin
			'search.bluewin.ch' => array('Bluewin','query'),
			//Caloweb
			'caloweb.de' => array('Caloweb','q'),
			//Cegetel(Google)
			'cegetel.net' => array('Cegetel(Google)','q'),
			//Centrum
			'fulltext.centrum.cz' => array('Centrum','q','windows-1250'),
			'morfeo.centrum.cz' => array('Centrum','q','windows-1250'),
			'search.centrum.cz' => array('Centrum','q','windows-1250'),
			//Chello
			'chello.fr' => array('Chello','q1'),
			//ClubInternet
			'recherche.club-internet.fr' => array('ClubInternet','q'),
			//Comcast
			'comcast.net' => array('Comcast','query'),
			//Cometsystems
			'search.cometsystems.com' => array('CometSystems','q'),
			//Copernic
			'metaresults.copernic.com' => array('Copernic',''),
			//DasOertliche
			'dasoertliche.de' => array('DasOertliche','kw'),
			//DasTelefonbuch
			'4call.dastelefonbuch.de' => array('DasTelefonbuch','kw'),
			//Dino
			'dino-online.de' => array('Dino','query'),
			//dir.com
			'fr.dir.com' => array('dir.com','req'),
			//dmoz
			'editors.dmoz.org' => array('dmoz','search'),
			'search.dmoz.org' => array('dmoz','search'),
			'dmoz.org' => array('dmoz','search'),
			'dmoz.org' => array('dmoz','search'),
			//Dogpile
			'search.dogpile.com' => array('Dogpile','q'),
			'nbci.dogpile.com' => array('Dogpile','q'),
			//earthlink
			'search.earthlink.net' => array('Earthlink','q'),
			//Eniro
			'eniro.se' => array('Eniro','q'),
			//Espotting
			'affiliate.espotting.fr' => array('Espotting','keyword'),
			//Eudip
			'eudip.com' => array('Eudip',''),
			//Eurip
			'eurip.com' => array('Eurip','q'),
			//Euroseek
			'euroseek.com' => array('Euroseek','string'),
			//Excite
			'excite.it' => array('Excite','q'),
			'msxml.excite.com' => array('Excite','qkw'),
			'excite.fr' => array('Excite','search'),
			//Exalead
			'exalead.fr' => array('Exalead','q'),
			'exalead.com' => array('Exalead','q'),
			//eo
			'eo.st' => array('eo','q'),
			//Francite
			'antisearch.francite.com' => array('Francite','KEYWORDS'),
			'recherche.francite.com' => array('Francite','name'),
			//Fireball
			'suche.fireball.de' => array('Fireball','query'),
			//Firstfind
			'firstsfind.com' => array('Firstsfind','qry'),
			//Fixsuche
			'fixsuche.de' => array('Fixsuche','q'),
			//Flix
			'flix.de' => array('Flix.de','keyword'),
			//Free
			'search1-2.free.fr' => array('Free','q'),
			'search1-1.free.fr' => array('Free','q'),
			'search.free.fr' => array('Free','q'),
			//Freenet
			'suche.freenet.de' => array('Freenet','query'),
			//Froogle
			'froogle.google.de' => array('Google(Froogle)','q'),
			'froogle.google.com' => array('Google(Froogle)','q'),
			'froogle.google.co.uk' => array('Google(Froogle)','q'),
			//GMX
			'suche.gmx.net' => array('GMX','su'),
			'gmx.net' => array('GMX','su'),
			//goo
			'search.goo.ne.jp' => array('goo','mt'),
			'ocnsearch.goo.ne.jp' => array('goo','mt'),
			//PoweredbyGoogle(addornot?)
			'charter.net' => array('Google','q'),
			'brisbane.t-online.de' => array('Google','q'),
			'eniro.se' => array('Google','q'),
			'eniro.no' => array('Google','q'),
			'miportal.bellsouth.net' => array('Google','string'),
			'home.bellsouth.net' => array('Google','string'),
			'pesquisa.clix.pt' => array('Google','q'),
			'google.startsiden.no' => array('Google','q'),
			'arianna.libero.it' => array('Google','query'),
			'google.startpagina.nl' => array('Google','q'),
			'search.peoplepc.com' => array('Google','q'),
			'google.interia.pl' => array('Google','q'),
			'buscador.terra.es' => array('Google','query'),
			'buscador.terra.cl' => array('Google','query'),
			'buscador.terra.com.br' => array('Google','query'),
			'icq.com' => array('Google','q'),
			'adelphia.net' => array('Google','q'),
			'comcast.net' => array('Google','query'),
			'so.qq.com' => array('Google','word'),
			'misc.skynet.be' => array('Google','keywords'),
			'start.no' => array('Google','q'),
			'verden.abcsok.no' => array('Google','q'),
			'search.sweetim.com' => array('Google','q'),
			'google.interia.pl' => array('Google','q'),
			//Google
			'gogole.fr' => array('Google','q'),
			'gogole.fr' => array('Google','q'),
			'ww.google.fr' => array('Google','q'),
			'google.fr' => array('Google','q'),
			'google.fr.' => array('Google','q'),
			'google.fr' => array('Google','q'),
			'www2.google.com' => array('Google','q'),
			'ww.google.com' => array('Google','q'),
			'gogole.com' => array('Google','q'),
			'go.google.com' => array('Google','q'),
			'google.ae' => array('Google','q'),
			'google.as' => array('Google','q'),
			'google.at' => array('Google','q'),
			'google.az' => array('Google','q'),
			'google.be' => array('Google','q'),
			'google.bg' => array('Google','q'),
			'google.ba' => array('Google','q'),
			'google.bg' => array('Google','q'),
			'google.bi' => array('Google','q'),
			'google.ca' => array('Google','q'),
			'ww.google.ca' => array('Google','q'),
			'google.cc' => array('Google','q'),
			'google.cd' => array('Google','q'),
			'google.cg' => array('Google','q'),
			'google.ch' => array('Google','q'),
			'google.ci' => array('Google','q'),
			'google.cl' => array('Google','q'),
			'google.cn' => array('Google','q'),
			'google.co' => array('Google','q'),
			'google.de' => array('Google','q'),
			'wwwgoogle.de' => array('Google','q'),
			'googleearth.de' => array('Google','q'),
			'googleearth.de' => array('Google','q'),
			'google.dj' => array('Google','q'),
			'google.dk' => array('Google','q'),
			'google.es' => array('Google','q'),
			'google.fi' => array('Google','q'),
			'google.fm' => array('Google','q'),
			'google.gg' => array('Google','q'),
			'googel.fi' => array('Google','q'),
			'googleearth.fr' => array('Google','q'),
			'google.gl' => array('Google','q'),
			'google.gm' => array('Google','q'),
			'google.gr' => array('Google','q'),
			'google.gr' => array('Google','q'),
			'google.hn' => array('Google','q'),
			'google.hr' => array('Google','q'),
			'google.hr' => array('Google','q'),
			'google.ie' => array('Google','q'),
			'google.is' => array('Google','q'),
			'google.it' => array('Google','q'),
			'google.jo' => array('Google','q'),
			'google.kz' => array('Google','q'),
			'google.li' => array('Google','q'),
			'google.lt' => array('Google','q'),
			'google.lu' => array('Google','q'),
			'google.lv' => array('Google','q'),
			'google.ms' => array('Google','q'),
			'google.mu' => array('Google','q'),
			'google.mw' => array('Google','q'),
			'google.md' => array('Google','q'),
			'google.nl' => array('Google','q'),
			'google.no' => array('Google','q'),
			'google.pl' => array('Google','q'),
			'google.sk' => array('Google','q'),
			'google.pn' => array('Google','q'),
			'google.pt' => array('Google','q'),
			'google.ro' => array('Google','q'),
			'google.ru' => array('Google','q'),
			'google.rw' => array('Google','q'),
			'google.se' => array('Google','q'),
			'google.sn' => array('Google','q'),
			'google.sh' => array('Google','q'),
			'google.si' => array('Google','q'),
			'google.sm' => array('Google','q'),
			'google.td' => array('Google','q'),
			'google.tt' => array('Google','q'),
			'google.uz' => array('Google','q'),
			'google.vg' => array('Google','q'),
			'google.com.ar' => array('Google','q'),
			'google.com.au' => array('Google','q'),
			'google.com.bo' => array('Google','q'),
			'google.com.br' => array('Google','q'),
			'google.com.co' => array('Google','q'),
			'google.com.cu' => array('Google','q'),
			'google.com.ec' => array('Google','q'),
			'google.com.eg' => array('Google','q'),
			'google.com.do' => array('Google','q'),
			'google.com.fj' => array('Google','q'),
			'google.com.gr' => array('Google','q'),
			'google.com.gt' => array('Google','q'),
			'google.com.hk' => array('Google','q'),
			'google.com.ly' => array('Google','q'),
			'google.com.mt' => array('Google','q'),
			'google.com.mx' => array('Google','q'),
			'google.com.my' => array('Google','q'),
			'google.com.nf' => array('Google','q'),
			'google.com.ni' => array('Google','q'),
			'google.com.np' => array('Google','q'),
			'google.com.pa' => array('Google','q'),
			'google.com.pe' => array('Google','q'),
			'google.com.ph' => array('Google','q'),
			'google.com.pk' => array('Google','q'),
			'google.com.pl' => array('Google','q'),
			'google.com.pr' => array('Google','q'),
			'google.com.py' => array('Google','q'),
			'google.com.qa' => array('Google','q'),
			'google.com.ru' => array('Google','q'),
			'google.com.sg' => array('Google','q'),
			'google.com.sa' => array('Google','q'),
			'google.com.sv' => array('Google','q'),
			'google.com.tr' => array('Google','q'),
			'google.com.tw' => array('Google','q'),
			'google.com.ua' => array('Google','q'),
			'google.com.uy' => array('Google','q'),
			'google.com.vc' => array('Google','q'),
			'google.com.vn' => array('Google','q'),
			'google.co.cr' => array('Google','q'),
			'google.co.gg' => array('Google','q'),
			'google.co.hu' => array('Google','q'),
			'google.co.id' => array('Google','q'),
			'google.co.il' => array('Google','q'),
			'google.co.in' => array('Google','q'),
			'google.co.je' => array('Google','q'),
			'google.co.jp' => array('Google','q'),
			'google.co.ls' => array('Google','q'),
			'google.co.ke' => array('Google','q'),
			'google.co.kr' => array('Google','q'),
			'google.co.nz' => array('Google','q'),
			'google.co.th' => array('Google','q'),
			'google.co.uk' => array('Google','q'),
			'google.co.ve' => array('Google','q'),
			'google.co.za' => array('Google','q'),
			'google.co.ma' => array('Google','q'),
			'wwwgoogle.cz' => array('Google','q'),
			'goggle.com' => array('Google','q'),
			'google.com' => array('Google','q'),
			
			//GoogleBlogsearch
			'blogsearch.google.de' => array('GoogleBlogsearch','q'),
			'blogsearch.google.fr' => array('GoogleBlogsearch','q'),
			'blogsearch.google.co.uk' => array('GoogleBlogsearch','q'),
			'blogsearch.google.it' => array('GoogleBlogsearch','q'),
			'blogsearch.google.net' => array('GoogleBlogsearch','q'),
			'blogsearch.google.es' => array('GoogleBlogsearch','q'),
			'blogsearch.google.ru' => array('GoogleBlogsearch','q'),
			'blogsearch.google.be' => array('GoogleBlogsearch','q'),
			'blogsearch.google.nl' => array('GoogleBlogsearch','q'),
			'blogsearch.google.at' => array('GoogleBlogsearch','q'),
			'blogsearch.google.ch' => array('GoogleBlogsearch','q'),
			'blogsearch.google.pl' => array('GoogleBlogsearch','q'),
			'blogsearch.google.com' => array('GoogleBlogsearch','q'),
			//Googletranslation
			'translate.google.com' => array('GoogleTranslations','q'),
			//GoogleDirectory
			'directory.google.com' => array('GoogleDirectory',''),
			//GoogleImages
			'images.google.fr' => array('GoogleImages','q'),
			'images.google.be' => array('GoogleImages','q'),
			'images.google.ca' => array('GoogleImages','q'),
			'images.google.co.uk' => array('GoogleImages','q'),
			'images.google.de' => array('GoogleImages','q'),
			'images.google.be' => array('GoogleImages','q'),
			'images.google.ca' => array('GoogleImages','q'),
			'images.google.it' => array('GoogleImages','q'),
			'images.google.at' => array('GoogleImages','q'),
			'images.google.bg' => array('GoogleImages','q'),
			'images.google.ch' => array('GoogleImages','q'),
			'images.google.ci' => array('GoogleImages','q'),
			'images.google.com.au' => array('GoogleImages','q'),
			'images.google.com.cu' => array('GoogleImages','q'),
			'images.google.co.id' => array('GoogleImages','q'),
			'images.google.co.il' => array('GoogleImages','q'),
			'images.google.co.in' => array('GoogleImages','q'),
			'images.google.co.jp' => array('GoogleImages','q'),
			'images.google.co.hu' => array('GoogleImages','q'),
			'images.google.co.kr' => array('GoogleImages','q'),
			'images.google.co.nz' => array('GoogleImages','q'),
			'images.google.co.th' => array('GoogleImages','q'),
			'images.google.co.tw' => array('GoogleImages','q'),
			'images.google.co.ve' => array('GoogleImages','q'),
			'images.google.com.ar' => array('GoogleImages','q'),
			'images.google.com.br' => array('GoogleImages','q'),
			'images.google.com.cu' => array('GoogleImages','q'),
			'images.google.com.do' => array('GoogleImages','q'),
			'images.google.com.gr' => array('GoogleImages','q'),
			'images.google.com.hk' => array('GoogleImages','q'),
			'images.google.com.mx' => array('GoogleImages','q'),
			'images.google.com.my' => array('GoogleImages','q'),
			'images.google.com.pe' => array('GoogleImages','q'),
			'images.google.com.tr' => array('GoogleImages','q'),
			'images.google.com.tw' => array('GoogleImages','q'),
			'images.google.com.ua' => array('GoogleImages','q'),
			'images.google.com.vn' => array('GoogleImages','q'),
			'images.google.dk' => array('GoogleImages','q'),
			'images.google.es' => array('GoogleImages','q'),
			'images.google.fi' => array('GoogleImages','q'),
			'images.google.gg' => array('GoogleImages','q'),
			'images.google.gr' => array('GoogleImages','q'),
			'images.google.it' => array('GoogleImages','q'),
			'images.google.ms' => array('GoogleImages','q'),
			'images.google.nl' => array('GoogleImages','q'),
			'images.google.no' => array('GoogleImages','q'),
			'images.google.pl' => array('GoogleImages','q'),
			'images.google.pt' => array('GoogleImages','q'),
			'images.google.ro' => array('GoogleImages','q'),
			'images.google.ru' => array('GoogleImages','q'),
			'images.google.se' => array('GoogleImages','q'),
		
			'images.google.sk' => array('GoogleImages','q'),
			'images.google.com' => array('GoogleImages','q'),
			//GoogleNews
			'news.google.se' => array('GoogleNews','q'),
			'news.google.com' => array('GoogleNews','q'),
			'news.google.es' => array('GoogleNews','q'),
			'news.google.ch' => array('GoogleNews','q'),
			'news.google.lt' => array('GoogleNews','q'),
			'news.google.ie' => array('GoogleNews','q'),
			'news.google.de' => array('GoogleNews','q'),
			'news.google.cl' => array('GoogleNews','q'),
			'news.google.com.ar' => array('GoogleNews','q'),
			'news.google.fr' => array('GoogleNews','q'),
			'news.google.ca' => array('GoogleNews','q'),
			'news.google.co.uk' => array('GoogleNews','q'),
			'news.google.co.jp' => array('GoogleNews','q'),
			'news.google.com.pe' => array('GoogleNews','q'),
			'news.google.com.au' => array('GoogleNews','q'),
			'news.google.com.mx' => array('GoogleNews','q'),
			'news.google.com.hk' => array('GoogleNews','q'),
			'news.google.co.in' => array('GoogleNews','q'),
			'news.google.at' => array('GoogleNews','q'),
			'news.google.com.tw' => array('GoogleNews','q'),
			'news.google.com.co' => array('GoogleNews','q'),
			'news.google.co.ve' => array('GoogleNews','q'),
			'news.google.lu' => array('GoogleNews','q'),
			'news.google.com.ly' => array('GoogleNews','q'),
			'news.google.it' => array('GoogleNews','q'),
			'news.google.sm' => array('GoogleNews','q'),
			'news.google.com' => array('GoogleNews','q'),
			//Goyellow.de
			'goyellow.de' => array('GoYellow.de','MDN'),
			//Hit-Parade
			'recherche.hit-parade.com' => array('Hit-Parade','p7'),
			'class.hit-parade.com' => array('Hit-Parade','p7'),
			//HotbotviaLycos
			'hotbot.lycos.com' => array('Hotbot(Lycos)','query'),
			'search.hotbot.de' => array('Hotbot','query'),
			'search.hotbot.fr' => array('Hotbot','query'),
			'hotbot.com' => array('Hotbot','query'),
			//1stekeuze
			'zoek.1stekeuze.nl' => array('1stekeuze','terms'),
			//Infoseek
			'search.infoseek.co.jp' => array('Infoseek','qt'),
			//ICQ
			'icq.com' => array('ICQ','q'),
			//Ilse
			'spsearch.ilse.nl' => array('Startpagina','search_for'),
			'be.ilse.nl' => array('IlseBE','query'),
			'search.ilse.nl' => array('IlseNL','search_for'),
			//Iwon
			'search.iwon.com' => array('Iwon','searchfor'),
			//Ixquick
			'ixquick.com' => array('Ixquick','query'),
			'eu.ixquick.com' => array('Ixquick','query'),
			'us.ixquick.com' => array('Ixquick','query'),
			's1.us.ixquick.com' => array('Ixquick','query'),
			's2.us.ixquick.com' => array('Ixquick','query'),
			's3.us.ixquick.com' => array('Ixquick','query'),
			's4.us.ixquick.com' => array('Ixquick','query'),
			's5.us.ixquick.com' => array('Ixquick','query'),
			'eu.ixquick.com' => array('Ixquick','query'),
			//Jyxo
			'jyxo.cz' => array('Jyxo','q'),
			//JungleSpider
			'jungle-spider.de' => array('JungleSpider','qry'),
			//Kartoo
			'kartoo.com' => array('Kartoo',''),
			'kartoo.de' => array('Kartoo',''),
			'kartoo.fr' => array('Kartoo',''),
			//Kataweb
			'kataweb.it' => array('Kataweb','q'),
			//LaToileDuQuébecviaGoogle
			'google.canoe.com' => array('LaToileDuQuébec(Google)','q'),
			'toile.com' => array('LaToileDuQuébec(Google)','q'),
			'web.toile.com' => array('LaToileDuQuébec(Google)','q'),
			//LaToileDuQuébec
			'recherche.toile.qc.ca' => array('LaToileDuQuébec','query'),
			//Live.com
			'live.com' => array('Live','q'),
			//Looksmart
			'looksmart.com' => array('Looksmart','key'),
			//Lycos
			'search.lycos.com' => array('Lycos','query'),
			'vachercher.lycos.fr' => array('Lycos','query'),
			'lycos.fr' => array('Lycos','query'),
			'suche.lycos.de' => array('Lycos','query'),
			'search.lycos.de' => array('Lycos','query'),
			'sidesearch.lycos.com' => array('Lycos','query'),
			'multimania.lycos.fr' => array('Lycos','query'),
			//Mail.ru
			'go.mail.ru' => array('Mailru','q'),
			//Mamma
			'mamma.com' => array('Mamma','query'),
			'mamma75.mamma.com' => array('Mamma','query'),
			'mamma.com' => array('Mamma','query'),
			//Meceoo
			'meceoo.fr' => array('Meceoo','kw'),
			//Mediaset
			'servizi.mediaset.it' => array('Mediaset','searchword'),
			//Metacrawler
			'search.metacrawler.com' => array('Metacrawler','general'),
			//Metager
			'mserv.rrzn.uni-hannover.de' => array('Metager','eingabe'),
			//Metager2
			'metager2.de' => array('Metager2','q'),
			'metager2.de' => array('Metager2','q'),
			//Meinestadt
			'meinestadt.de' => array('Meinestadt.de','words'),
			//Monstercrawler
			'monstercrawler.com' => array('Monstercrawler','qry'),
			//Mozbot
			'mozbot.fr' => array('mozbot','q'),
			'mozbot.co.uk' => array('mozbot','q'),
			'mozbot.com' => array('mozbot','q'),
			//MSN
			'beta.search.msn.fr' => array('MSN','q'),
			'search.msn.fr' => array('MSN','q'),
			'search.msn.es' => array('MSN','q'),
			'search.msn.se' => array('MSN','q'),
			'search.latam.msn.com' => array('MSN','q'),
			'search.msn.nl' => array('MSN','q'),
			'leguide.fr.msn.com' => array('MSN','s'),
			'leguide.msn.fr' => array('MSN','s'),
			'search.msn.co.jp' => array('MSN','q'),
			'search.msn.no' => array('MSN','q'),
			'search.msn.at' => array('MSN','q'),
			'search.msn.com.hk' => array('MSN','q'),
			'search.t1msn.com.mx' => array('MSN','q'),
			'fr.ca.search.msn.com' => array('MSN','q'),
			'search.msn.be' => array('MSN','q'),
			'search.fr.msn.be' => array('MSN','q'),
			'search.msn.it' => array('MSN','q'),
			'sea.search.msn.it' => array('MSN','q'),
			'sea.search.msn.fr' => array('MSN','q'),
			'sea.search.fr.msn.be' => array('MSN','q'),
			'search.msn.com.tw' => array('MSN','q'),
			'search.msn.de' => array('MSN','q'),
			'search.msn.co.uk' => array('MSN','q'),
			'search.msn.co.za' => array('MSN','q'),
			'search.msn.ch' => array('MSN','q'),
			'search.msn.es' => array('MSN','q'),
			'search.msn.com.br' => array('MSN','q'),
			'search.ninemsn.com.au' => array('MSN','q'),
			'search.msn.dk' => array('MSN','q'),
			'search.arabia.msn.com' => array('MSN','q'),
			'search.msn.com' => array('MSN','q'),
			//MyWebSearch
			'kf.mysearch.myway.com' => array('MyWebSearch','searchfor'),
			'ms114.mysearch.com' => array('MyWebSearch','searchfor'),
			'ms146.mysearch.com' => array('MyWebSearch','searchfor'),
			'mysearch.myway.com' => array('MyWebSearch','searchfor'),
			'searchfr.myway.com' => array('MyWebSearch','searchfor'),
			'ki.mysearch.myway.com' => array('MyWebSearch','searchfor'),
			'search.mywebsearch.com' => array('MyWebSearch','searchfor'),
			'mywebsearch.com' => array('MyWebSearch','searchfor'),
			//Najdi
			'najdi.si' => array('Najdi.si','q'),
			//Needtofind
			'ko.search.need2find.com' => array('Needtofind','searchfor'),
			//Netster
			'netster.com' => array('Netster','keywords'),
			//Netscape
			'search-intl.netscape.com' => array('Netscape','search'),
			'netscape.fr' => array('Netscape','q'),
			'suche.netscape.de' => array('Netscape','q'),
			'search.netscape.com' => array('Netscape','query'),
			//Nomade
			'ie4.nomade.fr' => array('Nomade','s'),
			'rechercher.nomade.aliceadsl.fr' => array('Nomade(AliceADSL)','s'),
			'rechercher.nomade.fr' => array('Nomade','s'),
			//NorthernLight
			'northernlight.com' => array('NorthernLight','qr'),
			//Numéricable
			'numericable.fr' => array('Numéricable','query'),
			//Onet
			'szukaj.onet.pl' => array('Onet.pl','qt'),
			//Opera
			'search.opera.com' => array('Opera','search'),
			//Overture
			'overture.com' => array('Overture','Keywords'),
			'fr.overture.com' => array('Overture','Keywords'),
			//Picsearch
			'picsearch.com' => array('Picsearch','q'),
			//Plazoo
			'plazoo.com' => array('Plazoo','q'),
			//Quicksearches
			'data.quicksearches.net' => array('QuickSearches','q'),
			//Qualigo
			'qualigo.de' => array('Qualigo','q'),
			'qualigo.ch' => array('Qualigo','q'),
			'qualigo.at' => array('Qualigo','q'),
			'qualigo.nl' => array('Qualigo','q'),
			//Rambler
			'search.rambler.ru' => array('Rambler','words'),
			//Reacteur.com
			'reacteur.com' => array('Reacteur','kw'),
			//Sapo
			'pesquisa.sapo.pt' => array('Sapo','q'),
			//Search.com
			'search.com' => array('Search.com','q'),
			//Search.ch
			'search.ch' => array('Search.ch','q'),
			//Searchalot
			'searchalot.com' => array('Searchalot','query'),
			//Seek
			'seek.fr' => array('Searchalot','qry_str'),
			//Seekport
			'seekport.de' => array('Seekport','query'),
			'seekport.co.uk' => array('Seekport','query'),
			'seekport.fr' => array('Seekport','query'),
			'seekport.at' => array('Seekport','query'),
			'seekport.es' => array('Seekport','query'),
			'seekport.it' => array('Seekport','query'),
			//Seekport(blogs)
			'blogs.seekport.de' => array('Seekport(Blogs)','query'),
			'blogs.seekport.co.uk' => array('Seekport(Blogs)','query'),
			'blogs.seekport.fr' => array('Seekport(Blogs)','query'),
			'blogs.seekport.at' => array('Seekport(Blogs)','query'),
			'blogs.seekport.es' => array('Seekport(Blogs)','query'),
			'blogs.seekport.it' => array('Seekport(Blogs)','query'),
			//Seekport(news)
			'news.seekport.de' => array('Seekport(News)','query'),
			'news.seekport.co.uk' => array('Seekport(News)','query'),
			'news.seekport.fr' => array('Seekport(News)','query'),
			'news.seekport.at' => array('Seekport(News)','query'),
			'news.seekport.es' => array('Seekport(News)','query'),
			'news.seekport.it' => array('Seekport(News)','query'),
			//Searchscout
			'searchscout.com' => array('SearchScout','gt_keywords'),
			//Searchy
			'searchy.co.uk' => array('Searchy','search_term'),
			//Seznam
			'search1.seznam.cz' => array('Seznam','w'),
			'search2.seznam.cz' => array('Seznam','w'),
			'search.seznam.cz' => array('Seznam','w'),
			//Sharelook
			'sharelook.fr' => array('Sharelook','keyword'),
			'sharelook.de' => array('Sharelook','keyword'),
			//Skynet
			'search.skynet.be' => array('Skynet','keywords'),
			//Suchnase
			'suchnase.de' => array('Suchnase','qkw'),
			//Supereva
			'search.supereva.com' => array('Supereva','q'),
			//Sympatico
			'search.sli.sympatico.ca' => array('Sympatico','q'),
			'search.fr.sympatico.msn.ca' => array('Sympatico','q'),
			'sea.search.fr.sympatico.msn.ca' => array('Sympatico','q'),
			'search.sympatico.msn.ca' => array('Sympatico','q'),
			//Suchmaschine.com
			'suchmaschine.com' => array('Suchmaschine.com','suchstr'),
			//Teoma
			'teoma.com' => array('Teoma','t'),
			//Tiscali
			'rechercher.nomade.tiscali.fr' => array('Tiscali','s'),
			'search-dyn.tiscali.it' => array('Tiscali','key'),
			'tiscali.co.uk' => array('Tiscali','query'),
			'search-dyn.tiscali.de' => array('Tiscali','key'),
			'hledani.tiscali.cz' => array('Tiscali','query','windows-1250'),
			
			//T-Online
			'suche.t-online.de' => array('T-Online','q'),
			//Trouvez.com
			'trouvez.com' => array('Trouvez.com','query'),
			//Trusted-Search
			'trusted--search.com' => array('TrustedSearch','w'),
			//Vinden
			'zoek.vinden.nl' => array('Vinden','query'),
			//Vindex
			'vindex.nl' => array('Vindex','search_for'),
			//Virgilio
			'search.virgilio.it' => array('Virgilio','qs'),
			//Voila
			'search.ke.voila.fr' => array('Voila','rdata'),
			'moteur.voila.fr' => array('Voila','kw'),
			'search.voila.fr' => array('Voila','kw'),
			'beta.voila.fr' => array('Voila','kw'),
			'search.voila.com' => array('Voila','kw'),
			//Volny
			'web.volny.cz' => array('Volny','search','windows-1250'),
			//Wanadoo
			'search.ke.wanadoo.fr' => array('Wanadoo','kw'),
			//Web.de
			'suche.web.de' => array('Web.de(Websuche)','su'),
			'dir.web.de' => array('Web.de(Directory)','su'),
			//Webtip
			'webtip.de' => array('Webtip','keyword'),
			//X-recherche
			'x-recherche.com' => array('X-Recherche','mots'),
			//Yahoo
			'ink.yahoo.com' => array('Yahoo!','p'),
			'ink.yahoo.fr' => array('Yahoo!','p'),
			'fr.ink.yahoo.com' => array('Yahoo!','p'),
			'search.yahoo.co.jp' => array('Yahoo!','p'),
			'search.yahoo.fr' => array('Yahoo!','p'),
			'ar.search.yahoo.com' => array('Yahoo!','p'),
			'br.search.yahoo.com' => array('Yahoo!','p'),
			'de.search.yahoo.com' => array('Yahoo!','p'),
			'ca.search.yahoo.com' => array('Yahoo!','p'),
			'cf.search.yahoo.com' => array('Yahoo!','p'),
			'fr.search.yahoo.com' => array('Yahoo!','p'),
			'espanol.search.yahoo.com' => array('Yahoo!','p'),
			'es.search.yahoo.com' => array('Yahoo!','p'),
			'id.search.yahoo.com' => array('Yahoo!','p'),
			'it.search.yahoo.com' => array('Yahoo!','p'),
			'kr.search.yahoo.com' => array('Yahoo!','p'),
			'mx.search.yahoo.com' => array('Yahoo!','p'),
			'nl.search.yahoo.com' => array('Yahoo!','p'),
			'uk.search.yahoo.com' => array('Yahoo!','p'),
			'cade.search.yahoo.com' => array('Yahoo!','p'),
			'tw.search.yahoo.com' => array('Yahoo!','p'),
			'yahoo.com.cn' => array('Yahoo!','p'),
			'search.yahoo.com' => array('Yahoo!','p'),
			'de.dir.yahoo.com' => array('Yahoo!Répertoires','q'),
			'cf.dir.yahoo.com' => array('Yahoo!Répertoires','q'),
			'fr.dir.yahoo.com' => array('Yahoo!Répertoires',''),
			
			//Yandex
			'yandex.ru' => array('Yandex','text'),
			'yandex.ru' => array('Yandex','text'),
			'search.yaca.yandex.ru' => array('Yandex','text'),
			'ya.ru' => array('Yandex','text'),
			'ya.ru' => array('Yandex','text'),
			'images.yandex.ru' => array('YandexImages','text'),
			//Yellowmap
			'yellowmap.de' => array('Yellowmap',''),
			'yellowmap.de' => array('Yellowmap',''),
			//Wanadoo
			'search.ke.wanadoo.fr' => array('Wanadoo','kw'),
			//Wedoo
			'fr.wedoo.com' => array('Wedoo','keyword'),
			//Web.nl
			'web.nl' => array('Web.nl','query'),
			//Weborama
			'weborama.fr' => array('weborama','query'),
			//WebSearch
			'is1.websearch.com' => array('WebSearch','qkw'),
			'websearch.com' => array('WebSearch','qkw'),
			'websearch.cs.com' => array('WebSearch','query'),
			//Witch
			'witch.de' => array('Witch','search'),
			//WXS
			'wxsl.nl' => array('PlanetInternet','q'),
			//Zoek
			'www3.zoek.nl' => array('Zoek','q'),
			//Zoeken
			'zoeken.nl' => array('Zoeken','query'),
			//Zoohoo
			'zoohoo.cz' => array('Zoohoo','q','windows-1250'),
			'zoohoo.cz' => array('Zoohoo','q','windows-1250'),
			//Zoznam
			'zoznam.sk' => array('Zoznam','s'),
			//Neti.ee
			'neti.ee' => array('neti','q')
		);	
	}
	
	
	
	public static function write() {
		$types = array(
			1	=> 'oslist',
			2	=> 'browsers',
			3	=> 'robot',
			4	=> 'searchengines'
		);	
		$t = array();
		$t[1] = self::OS();
		$t[2] = self::Browsers();
		$t[3] = self::Robots();		
		$t[4] = self::SearchEngines();
		
		
		foreach ($t as $type => $arr) {
			switch ($type) {
				case 1:
				case 2:
					foreach ($arr as $k => $v) {
						$data = array(
							'k'	=> $k,
							'v'	=> $v,
							't'	=> $type
						);
						DB::noerror();
						DB::insert('db',$data);
					}
				break;
				case 3:
					foreach ($arr as $v => $keys) {
						foreach ($keys as $k) {
							$data = array(
								'k'	=> $k,
								'v'	=> $v,
								't'	=> $type
							);
							DB::noerror();
							DB::insert('db',$data);
						}
					}
				break;
				case 4:
					foreach ($arr as $k => $v) {
						$data = array(
							'k'	=> $k,
							'v'	=> $v[0],
							's'	=> $v[1],
							't'	=> $type
						);
						DB::noerror();
						DB::insert('db',$data);
					}
				break;	
			}
		}
	}
}