<?php

class RSSPanel extends ModelAdminPanel {
	
	public $feed = '';

	
	function setFeed($feed = NULL){
		$this->feed = $feed;	
	}
	
	function RSSItems(){
		require_once(THIRDPARTY_PATH.'/simplepie/simplepie.inc');
		$feed = new SimplePie();
		$feed->set_feed_url($this->feed); 
		$feed->init();
      	$feed->handle_content_type(); 
		if ($feed->data) {
			$items = new DataObjectSet();
			foreach ($feed->get_items() as $item) {
				$items->push(new ArrayData(array(
					'Content'	=> $item->get_content(),
					'Date'		=> $item->get_date(),
					'Title'		=> $item->get_title(),
					'Link'		=> $item->get_permalink()
				)));
			}
			return $items;
		} 
	}
}