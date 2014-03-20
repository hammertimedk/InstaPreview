<?php defined('C5_EXECUTE') or die("Access Denied.");


class Collection extends Concrete5_Model_Collection {


		/* Updated this function to ensure that we always get a brand new version of a collection for InstaPreview 
		*/
		function getVersionToModify() {
		
		 if ($_REQUEST["instapreview"]) {
		 
		  if (CollectionVersion::get($this, 'ACTIVE')->getVersionComments() == "Preview Version") {
		   //We should not find ourselves here - but our currently active version is already the preview version
		   //This can happen if a block craps out, or does some magic which causes this method to be run twice before a tunnel
		   error_log($this->getVersionObject()->getVersionID()." Preview version Already exists", 0);
		  } else {
		 
			$nc = $this->cloneVersion("Preview Version");
			$nc->getVersionObject()->approve(false); //Approve it immediately
				
			$versionid = CollectionVersion::get($nc, 'ACTIVE')->getVersionID();
			
		  }
			
			return $nc;
		 } else {
		  return parent::getVersionToModify(); //Otherwise call into stock C5 code
		 }
		
		}



}