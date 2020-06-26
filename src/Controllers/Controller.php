<?php

namespace WP2StaticMvEnhancedDeployment;

class Controller {
		/**
			 * This Add-on's initialization routine
			 *
			 * Runs on frequently, so avoid resource intensive routines in here.
			 */
		public function run() : void {
				// function to run after deployment (ie, cache invalidation, Slack notification)
				add_action(
						'wp2static_post_deploy_trigger',
						[ $this, 'post_deployment_action' ],
						15,
						2
				);
		}
		
		
		public function post_deployment_action($enabled_deployer) : void {
				$plugin_dir = WP2STATIC_MV_ENHANCEMENT_ADDON_PATH;
				$wordpress_dir = get_home_path();
				
				//mvDelTree( $wordpress_dir . 'test');			//This is a test for the mvDelTree function.
				
				//unlink current "live" folder
				if(is_dir($wordpress_dir . 'htdocs')) {
						mvDelTree( $wordpress_dir . 'htdocs');
				}
				
				//mv current deployment folder
				rename($wordpress_dir . 'myhtfiles2', $wordpress_dir . 'htdocs');
				
				//mv scripts for static site (for example for posting forms..
				copy($plugin_dir . 'mindfav-prepared-scripts-for-static-site/.htaccess', $wordpress_dir . 'htdocs/.htaccess');
				copy($plugin_dir . 'mindfav-prepared-scripts-for-static-site/process_form.php', $wordpress_dir . 'htdocs/process_form.php');
		}
}

///////////////////////////////////////////////////////////////////////////////////////
// Delete folder structure recursively
// Thanks to German Latorre's stack overflow in this thread:
// https://stackoverflow.com/questions/3349753/delete-directory-with-files-in-it
///////////////////////////////////////////////////////////////////////////////////////
function mvDelTree($dir) { 
		$files = array_diff(scandir($dir), array('.', '..')); 

		foreach ($files as $file) { 
				(is_dir("$dir/$file")) ? mvDelTree("$dir/$file") : unlink("$dir/$file"); 
		}

		return rmdir($dir); 
} 
		
		
	