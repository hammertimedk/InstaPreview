<?php defined('C5_EXECUTE') or die(_("Access Denied."));
/**
 * Concrete5 Package "Instant Preview"
 * @author Arni Johannesson (www.kramerican.dk / www.hammerti.me)
 * MIT License
 *
 */
class InstaPreviewPackage extends Package {

	protected $pkgHandle = 'instapreview';
	protected $appVersionRequired = '5.6.2';
	protected $pkgVersion = '1.0';

	public function getPackageDescription() {
		return t("InstaPreview: Instantly preview any block when adding or editing.");
	}

	public function getPackageName() {
		return t("InstaPreview");
	}
	
	public function getPackageHandle(){
		return 'instapreview';
	}

	/* http://www.concrete5.org/community/forums/customizing_c5/override-core-block-controllers-within-package/#499864 */
	public function on_start() {
	 $objEnv = Environment::get();
	 $objEnv->overrideCoreByPackage('libraries/block_view.php', $this);
	 $objEnv->overrideCoreByPackage('libraries/block_controller.php', $this);
	 $objEnv->overrideCoreByPackage('models/collection.php', $this);
	}		
	
	public function upgrade() {
	/*	Nothing here yet as this is version 1
	
		parent::upgrade();
		$pkg= Package::getByHandle($this->pkgHandle);
	*/
	}

	public function install() {
		$pkg = parent::install();
	
	}
	
	/* Make sure we clean up DB and cache file */
	public function uninstall() {
	 parent::uninstall();
	 
	}
}