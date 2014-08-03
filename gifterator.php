<?php
/**
 * Plugin Name: Gifterator
 */
 
add_action('admin_menu', 'add_gifterator_menu');
 
function add_gifterator_menu() {
	add_menu_page( 'Prezenty', 'Prezenty', 'edit_pages', 'prezenty', 'build_gifterator_menu', null, 21);
}

function build_gifterator_menu() {
	echo '<p>Awesome menu</p>';
}

?>