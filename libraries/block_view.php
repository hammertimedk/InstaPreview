<?php defined('C5_EXECUTE') or die("Access Denied.");
/**
 InstaPreview
 Had to override the core Block View render method in order to check for overrides to block elements 
 in this package folder. Look in package controller to see how this override is done.
 
 This type of override is considered breakage, is the ugliest of the overrides I had to do here, and will result in this package not being accepted to the C5 marketplace unfortunately.
 
 I think that the way that footer/header are included in the block add/edit modals is potentially flawed. But then again,
 when does anybody except me have the need to override/modify that stuff ;O)
 
	(c) 2014 Arni Johannesson - (hammerti.me / kramerican.dk)
	MIT License 
*/

class BlockView extends Concrete5_Library_BlockView {


/** 
		 * Renders a particular view for a block or a block type
		 * @param Block | BlockType $obj
		 * @param string $view
		 * @param array $args
		 */
		public function render($obj, $view = 'view', $args = array()) {
			if ($this->hasRendered) {
				return false;
			}
			$this->blockObj = $obj;
			$customAreaTemplates = array();
			
			if ($obj instanceof BlockType) {
				$bt = $obj;
				$base = $obj->getBlockTypePath();
			} else {
				$bFilename = $obj->getBlockFilename();
				$b = $obj;
				$base = $b->getBlockPath();
				$this->block = $b;
				$this->c = $b->getBlockCollectionObject();
				if ($bFilename == '' && is_object($this->area)) {
					$customAreaTemplates = $this->area->getCustomTemplates();
					$btHandle = $b->getBlockTypeHandle();
					if (isset($customAreaTemplates[$btHandle])) {
						$bFilename = $customAreaTemplates[$btHandle];
					}
				}

			}				
			
			$btHandle = $obj->getBlockTypeHandle();
			
			if (!isset($this->controller)) {
				if ($obj instanceof Block) {
					$this->controller = $obj->getInstance();
					$this->controller->setBlockObject($obj);
				} else {
					$this->controller = Loader::controller($obj);
				}
			}
			if (in_array($view, array('view', 'add', 'edit', 'composer'))) {
				$_action = $view;
			} else {
				$_action = 'view';
			}
			
			$u = new User();
			
			$outputContent = false;
			$useCache = false;
			$page = Page::getCurrentPage();
			
			if ($view == 'view') {
				if (ENABLE_BLOCK_CACHE && $this->controller->cacheBlockOutput() && ($obj instanceof Block)) {
					if ((!$u->isRegistered() || ($this->controller->cacheBlockOutputForRegisteredUsers())) &&
						(($_SERVER['REQUEST_METHOD'] != 'POST' || ($this->controller->cacheBlockOutputOnPost() == true)))) {
							$useCache = true;
					}
					if ($useCache) {
						$outputContent = $obj->getBlockCachedOutput($this->area);
					}
				}
			}
			if ($outputContent == false) {
				$this->controller->setupAndRun($_action);
			}
			extract($this->controller->getSets());
			extract($this->controller->getHelperObjects());
			$headerItems = $this->controller->headerItems;
			extract($args);
			
			if ($this->controller->getRenderOverride() != '') { 
				$_filename = $this->controller->getRenderOverride() . '.php';
			} 
			
			if ($view == 'scrapbook') {
				$template = $this->getBlockPath(FILENAME_BLOCK_VIEW_SCRAPBOOK) . '/' . FILENAME_BLOCK_VIEW_SCRAPBOOK;
				if (!file_exists($template)) {
					$view = 'view';
				}
			}
			
			if (!in_array($view, array('composer','view', 'add', 'edit', 'scrapbook'))) {
				// then we're trying to render a custom view file, which we'll pass to the bottom functions as $_filename
				$_filename = $view . '.php';
				$view = 'view';
			}
			
			switch($view) {
				case 'scrapbook':
					$header = Environment::get()->getPath(DIRNAME_ELEMENTS . '/block_header_view.php', null);
					$footer = Environment::get()->getPath(DIRNAME_ELEMENTS . '/block_footer_view.php', null);										
					break;
				case 'composer':
				case 'view':				
					if (!$outputContent) {
						if (!isset($_filename)) {
							$_filename = FILENAME_BLOCK_VIEW;
						}					
						$bvt = new BlockViewTemplate($obj);
						if ($bFilename) {
							$bvt->setBlockCustomTemplate($bFilename); // this is PROBABLY already set by the method above, but in the case that it's passed by area we have to set it here
						} else if ($_filename != FILENAME_BLOCK_VIEW) {
							$bvt->setBlockCustomRender($_filename); 
						}
						$template = $bvt->getTemplate();
					}
					
					if ($view == 'composer') {
						$displayEditLink = true;
						$header = Environment::get()->getPath(DIRNAME_ELEMENTS . '/block_header_composer.php', null);
						$footer = Environment::get()->getPath(DIRNAME_ELEMENTS . '/block_footer_composer.php', null);
						$cpFilename = $obj->getBlockComposerFilename();
						if ($cpFilename) {
							$cmpbase = $this->getBlockPath(DIRNAME_BLOCK_TEMPLATES_COMPOSER . '/' . $cpFilename);
							if (file_exists($cmpbase . '/' . DIRNAME_BLOCK_TEMPLATES_COMPOSER . '/' . $cpFilename)) {
								$template = $base . '/' . DIRNAME_BLOCK_TEMPLATES_COMPOSER . '/' . $cpFilename;
								$displayEditLink = false;
							}
						}
						
						if ($displayEditLink) {
							$cmpbase = $this->getBlockPath(FILENAME_BLOCK_COMPOSER);
							if (file_exists($cmpbase . '/' . FILENAME_BLOCK_COMPOSER)) {
								$template = $cmpbase . '/' . FILENAME_BLOCK_COMPOSER;
								$displayEditLink = false;
							}
						}
						
					} else {
						$header = Environment::get()->getPath(DIRNAME_ELEMENTS . '/block_header_view.php', null);
						$footer = Environment::get()->getPath(DIRNAME_ELEMENTS . '/block_footer_view.php', null);										
					}
					break;
				case 'add':
					if (!isset($_filename)) {
						$_filename = FILENAME_BLOCK_ADD;
					}
					$header = Environment::get()->getPath(DIRNAME_ELEMENTS . '/block_header_add.php', null);
					$footer = Environment::get()->getPath(DIRNAME_ELEMENTS . '/block_footer_add.php', 'instapreview');
					break;
				case 'edit':
					if (!isset($_filename)) {
						$_filename = FILENAME_BLOCK_EDIT;
					}
					$header = Environment::get()->getPath(DIRNAME_ELEMENTS . '/block_header_edit.php', null);
					$footer = Environment::get()->getPath(DIRNAME_ELEMENTS . '/block_footer_edit.php', 'instapreview');
					break;
			} 		
			
			if (!isset($template)) {
				$base = $this->getBlockPath($_filename);
				$template = $base . '/' . $_filename;
			}
						
			if (isset($header)) {
				include($header);
			}
			if ($outputContent) {
				print $outputContent;			
			} else if ($template) {
				
				ob_start();
				include($template);
				$outputContent = ob_get_contents();
				ob_end_clean();					
				print $outputContent;
				
				if ($useCache) {
					$obj->setBlockCachedOutput($outputContent, $this->controller->getBlockTypeCacheOutputLifetime(), $this->area);
				}
			}
			if (isset($footer)) {
				include($footer);
			}

			$this->template = $template;
			$this->header = $header;
			$this->footer = $footer;
			
			
		}

}