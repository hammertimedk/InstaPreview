<?php defined('C5_EXECUTE') or die("Access Denied.");
/* 	InstaPreview
	Another potentially breaky override - hooks into the save method for all blocks
	
	(c) 2014 Arni Johannesson - Kramerica Industries (kramerican.dk)
	MIT License
*/
		
class BlockController extends Concrete5_Library_BlockController {

		/**
		 * Most of this code is duplicated from the lib we are overriding.
		 * 
		 */
		public function save($args) {
			parent::save($args); //Call into stock C5 code
			
			//Instapreview code - Block has saved content now - let's publish the new page version
			if ($_REQUEST["instapreview"]) {
			  $c = Page::getByID($_REQUEST["cID"]);
 			
			  //Approve the preview version - we roll back by ajaxing version_handler after the preview closes
			  $v = CollectionVersion::get($c, "RECENT");
			  $v->approve(false);
			 
			}
			
		}
		
}		