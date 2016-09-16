<?php

/**
* AntiSpam database
* Ajaxel CMS v5.0
* Author: Alexander Shatalov <admin@ajaxel.com>
* http://ajaxel.com

* Protect comments from spammers
*/


$spam = array();

$spam['remote_host'] = array('keywordspy.com', 'keywordspy.com', 'clients\.your-server\.de$', '^rover\-host\.com$', '^host\.lotosus\.com$', 'clients\.your\-server\.de$', '^rover\-host\.com$', '^host\.lotosus\.com$', '^rdns\.softwiseonline\.com$', 's([a-z0-9]+)\.websitehome\.co\.uk$', '\.opentransfer\.com$', 'arkada\.rovno\.ua$', '^host\.');

$spam['email'] = array('aaron@yahoo.com', 'asdf@yahoo.com', 'a@a.com', 'bill@berlin.com', 'capricanrulz@hotmail.com', 'dominic@mail.com', 'fuck@you.com', 'heel@mail.com', 'jane@mail.com', 'neo@hotmail.com', 'nick76@mailbox.com', '12345@yahoo.com', 'poster78@gmail.com', 'ycp_m23@hotmail.com', 'grey_dave@yahoo.com', 'grren_dave55@hotmail.com', 'dave_morales@hotmail.com', 'tbs_guy@hotmail.com', 'test@test.com');

$spam['email_regex'] = array('seo@gmail.com', '@keywordspy.com', 'fuckyou', 'spam', 'spambot@', 'anonymous@', 'root@');

$spam['comment'] = array('.150m.com', '.250m.com', '.free-site-host.com', '.freehostia.com', '.phpbbserver.com', '.t35.com', '100% satisfied', '>anal ', '[...] [...]', 'accept credit cards', 'accutane', 'acomplia', 'act now!', 'additional income', 'adipex', 'adult movie', 'adult video', 'affordable', 'agriimplements.com', 'all natural', 'all new', 'amazing', ' anal ', 'anal sex', 'animal sex', 'animatedfavicon.com', 'apply online', 'asd', 'bestiality', 'billing address', 'bit.ly', 'blackjack', 'blogs.ign.com', 'blowjob', 'blow job', 'buy cheap', 'buy direct', 'buy now', ' pills', 'c1alis', 'call free', 'call girl', 'cancel at any time', 'cards accepted', 'car insurance', 'cash advance', 'cashloan', 'celebrities', 'celebrity', 'cents on the dollar', 'check or money order', 'choice-direct.com', 'chooseautoinsurer.com', 'christiantorrents.ru', 'cialis', 'cignusweb.com', 'clickaudit.com', 'click below', 'click here', 'click to remove', 'clitoris', 'college student', 'commentposter.com', 'comments poster', 'compare rates', 'congratulations', 'consolidation student', 'credit card', 'cumshots', 'data entry india', 'dear friend', 'designer handbags', 'desnuda', 'diet pill', 'dildo', 'discount', 'do it today', 'drassyassut', 'drug rehab', 'dysfunction', 'earn money', 'ejaculate', 'ejaculating', 'ejaculation', 'envisionforce', 'ephedr1n', 'ephedr1ne', 'ephedra', 'ephedrin', 'ephedrine', 'erectile', 'erectile dysfunction ', 'erection', 'erotic', 'ertocom.nl', 'escort service', 'experl.com', 'extra income', 'facebook.com/', 'findcarinsur.com', 'findyourinsur.com', 'foreclosure', 'for free', 'for only ($)', 'free and free', 'free membership', 'free offer', 'free preview', 'free website', 'fucking', 'full refund', 'gambling', 'gay', 'giving away', 'grillpartssteak.com', 'groups.google.com', 'groups.google.us', 'groups.yahoo.com', 'guarantee', 'health care', 'healthcare', 'health insurance', 'hentai', 'herbalife', 'heterosexual', 'home insurance', 'homeinsurdeals.com', 'viagra', 'incest porn', 'increase sales', 'increase traffic', 'india offshore', 'information you requested', 'injury lawyer', 'insurance', 'internastional', 'internet marketer', 'internet marketing', 'investment / no investment', 'investment decision', 'johnbeck.com', 'johnbeck.net', 'johnbeck.tv', 'johnbeckseminar.com', 'johnbeckssuccessstories.com', 'kamagra', 'kankamforum.net', 'keywordspy.com', 'lesbian', 'lev1tra', 'levitra', 'lifecity.info', 'lifecity.tv', 'life insurance', ' loan', 'loan consolidation', 'marketing solutions', 'masterbate', 'masterbating', 'masterbation', 'mastersofseo.com', 'masturbate', 'masturbating', 'masturbation', 'matthewkoster.com', 'medica', 'medics', 'members.lycos.co.uk', 'message contains', 'mmoinn.com', 'modulesoft', 'month trial offer', 'mysmartseo.com', 'naked', 'name brand', 'netcallidus.com', 'no-obligation', 'no cost', 'no gimmicks', 'no hidden costs', 'nude', 'offshore india', 'one-time', 'one time', 'online pharmacy', 'online poker', 'opportunity', 'order now', 'orders shipped by priority mail', 'order today', 'orgasm', 'orl.be', 'penis', 'penis enlargement', 'phentermine', 'photoshop', 'phpdug', 'poker online', 'porn', 'pornotube', 'porntube', 'post-comments.com', 'potential earnings', 'power kite', 'pre-approved', 'prescription', 'print out and fax', 'profits', 'pron', 'propec1a', 'propecia', 'property vault', 'prostitute', 'pussy', 'rape', 'rape porn', 'real-url.org', 'real thing', 'registry-error-cleaner.com', 'remedy-shopping.com', 'removal instructions', 'remove', 'rimonabant', 'risk free', 'rsschannelwriter.com', 'sales', 'satisfaction guaranteed', 'save $', 'save up to', 'search engine marketer', 'search engine marketing', 'search engine ranking', 'search engines', 'see for yourself', 'serious cash', 'sex movie', 'sex tape', 'sexual service', 'sex video', 'shredderwarehouse.com', 'sitemapwriter.com', 'snipurl.com', 'social bookmark', 'social media consultant', 'social media consulting', 'social media marketer', 'social media optimization', 'social media optimizer', 'social poster', 'solution', ' soma ', ' soma.', 'special promotion', 'steroid', 'student ', 'student credit card', 'student health insurance', 'submit-trackback.com', 'submitbookmark.com', 'sunitawedsamit.com', 'teen porn', 'the following form', 'this is not spam', 'this is something special', 'tinyurl.com', 'topinsurdeals.com', 'torture porn', 'trackback submitter', 'tramadol', 'travel deals', 'turbo tax', 'ultram', 'unsolicited', 'unsubscribe', 'v1agra', 'vagina', 'valium', 'visit now', 'webmaster', 'webseomasters.com', 'website promotion', 'web site promotion', 'weight loss pill', 'widecircles.com', 'win now', 'wordpressautocomment.com', 'work at home', 'www.google.com/notebook/public/', 'xanax', 'xxx', 'youporn', 'youporn736.vox.com', 'youtube-poster.com', 'zimulti', 'zithromax', 'zoekmachine optimalisatie','cheap jordans','diet ','cracked','capsul','matusik','trener','osobisty','wpis','jersey','louis','vuitton','hoodia','pollen','baidu','diigo.com','lauren','burberry shop','heels','louboutin','cheap new','lulu','jakke','meizitang','kors ',' charms','[link=','gockc.net','onlinesale','celine','dr dre','juicer','ninja ','daidaihua','weight loss','nike','shoes');

$spam['ip'] = array('^78\.129\.202\.', '^123\.237\.144\.', '^123\.237\.147\.', '^194\.8\.7([45])\.', '^193\.37\.152\.', '^193\.46\.236\.', '^92\.48\.122\.([0-9]|[12][0-9]|3[01])$', '^116\.71\.', '^192\.168\.', '^78.129.202.', '^123.237.144.', '^123.237.147.', '^78\.129\.202\.', '^123\.237\.144\.', '^123\.237\.147.', '^194\.8\.7([45])\.', '^193\.37\.152.', '^193\.46\.236\.', '^92\.48\.122\.([0-9]|[12][0-9]|3[01])$', '^116\.71\.', '^192\.168\.', '^109\.230\.216\.');

?>