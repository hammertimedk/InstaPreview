<?php defined('C5_EXECUTE') or die("Access Denied.");


class Collection extends Concrete5_Model_Collection {


		/* Updated this function to ensure that we always get a brand new version of a collection for InstaPreview 
		 - Somewhat flaky at the moment - sometimes preview collection is not approved for some reason, must investigate...
		*/
		function getVersionToModify() {
		
		 if ($_REQUEST["instapreview"]) {
			$nc = $this->cloneVersion($versionComments); //$versionComments? Look in original method, looks like junk
			$nc->getVersionObject()->approve(false); //Approve it immediately
			return $nc;
		 } else {
		  return parent::getVersionToModify(); //Otherwise call into stock C5 code
		 }
		
		}



}