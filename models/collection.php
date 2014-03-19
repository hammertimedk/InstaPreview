<?php defined('C5_EXECUTE') or die("Access Denied.");


class Collection extends Concrete5_Model_Collection {


		/* Updated this function to ensure that we always get a brand new version of a collection for InstaPreview 
		*/
		function getVersionToModify() {
		
		 if ($_REQUEST["instapreview"]) {
			$nc = $this->cloneVersion($versionComments);
			return $nc;
		 } else {
		  return parent::getVersionToModify(); //Otherwise call into stock C5 code
		 }
		
		}



}