<?php

final class MyClass extends My {
	public function __construct(&$Index) {
		parent::__construct($Index);
		$this->version = '1';
	}
	
	public function init() {
		
	}
	
	public function head() {
		if (!IS_ADMIN) $this->Index->addCSSA('ui/selene/'.JQUERY_CSS);
		parent::head();
	}


	public function json() {
		switch (url(0)) {
			case 'something':
			
			break;
			default:
				parent::json(true);
			break;
		}
		$this->end();
	}
	
	/**
	* Called right before index template file
	*/
	final public function index() {
		$this->arrMenu = Factory::call('menu')->getByPosition(array('top'));	
		$this->set('arrMenu', $this->arrMenu);
		$this->set('url0', url(0));
		$this->set('url1', url(1));
	}
	
	
	
	public function data($type, $toOptions = -1, $optSettings = 'dropdown') {
		list ($type, $key, $keyAsVal, $lang) = Html::startArray($type);
		switch ($type) {
			case 'year_range':
				$ret = Html::arrRange(2010, 2040);
			break;
		}
		return Html::endArray($ret, $keyAsVal, $lang, $key, $toOptions, $optSettings);		
	}

	
	public function page() {
		$data = array();

		switch (url(0)) {
			case 'user':
				if (!Factory::call('user')->getContent()) {
					$this->Index->mainarea = false;
				}
			break;
			case 'tag':
				$this->Index->setVar('page_title',lang('Tag results'));
				$this->Index->setVar('title',lang('Tag results'));
				if (!Factory::call('content')->setLimit(10)->getTag(get('tag'))) {
					$this->Index->mainarea = false;
				}
			break;
			case 'search':
				$this->Index->setVar('page_title',lang('Search results'));
				$this->Index->setVar('title',lang('Search results'));
				if (!Factory::call('content')->setLimit(10)->getSearch(get('search'))) {
					$this->Index->mainarea = false;
				}
			break;
			case 'sitemap':
				Factory::call('content')->getSitemap();
				$this->Index->tree[0] = array(
					'title'	=> lang('Sitemap'),
					'url'	=> '?sitemap'
				);
				$this->Index->setVar('title', lang('Sitemap'));
			break;
			case 'contact':
				$this->contact();
			break;
			case URL_KEY_HOME:
				$this->Index->displayFile('main.tpl');				
			break;
			default:
				if (!$this->Index->staticPage(url(0),url(1)) && 
					!Factory::call('content')->setLimit(20)->setOrder('dated DESC, sort, id DESC')->setFilter('')->getContent()) {
					$this->Index->mainarea = false;
				}
			break;
		}		
	}
		
}