<?php
/**
 * Plugin Name: Gifterator
 */


add_shortcode('prezenty','show_present_list');

//[prezenty]
function show_present_list() {
	
	wp_enqueue_script('reservation', plugins_url('/reservation.js', __FILE__));
	
	global $wpdb;
	
	$savedPresents = $wpdb->get_results("
											SELECT *
											FROM wp_presents");
											
	
	
	$count = count($savedPresents);
		   
	for($i = 0; $i < $count; $i ++) {
		
		if (!$savedPresents[$i] -> reserved)
		{
			$returnString .='<div class="col-md-4 col-sm-6 col-xs-12">' . get_present($savedPresents[$i]) .'</div>';
		}
	}
			
	?>
	
	<div id="presentModal" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title">Rezerwacja</h4>
      </div>
      <div class="modal-body">
        <p>Jeśli naprawdę chcesz zarezerwować ten prezent</p>
        <p>Wprowadź haslo i wciśnij "Zarezerwuj"</p>
        <form method="post" id="form-reserve">
        <input type="hidden" name="presentId">
			<input type="text" name="presentpass">  
			<input type="button" class="btn btn-danger" value="Zarezerwuj" id="btn-reserve">     
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
	
	<?php
	
	if(!empty($_GET['message'])) {
		
		if ($_GET['message'] == 'success')
			$returnString = '<h1 class="bg-success">Dziękujemy za rezerwacje</h1>' . $returnString;
			
		if ($_GET['message'] == 'wrongpass')
			$returnString = '<h1 class="bg-danger">Niepoprawne Haslo</h1>' . $returnString;
		
	}
	
	if(!empty($_POST)) {
		
		if(!empty($_POST['presentId']) && !empty($_POST['presentpass'])) {
		
			if($_POST['presentpass'] == 'tralala') {
					
					$wpdb -> update('wp_presents', array('reserved' => true), array("id" => $_POST['presentId']));
       	
       	}      		
       }
	}
	
	return $returnString;
}

function get_present($present) {
	
	if(empty($present)) {
		return;
	}
	
	$returnString = '<div class="panel panel-default">
							<div class="panel-body">
							<img src="' . $present -> image . '" class="img-responsive" alt="">
							<p>' . $present -> description . '</p>
							<button type="button" class="btn btn-default" data-presentId="'. $present -> id .'" >Zarezerwować</button>
							</div>
							</div>';
							
	return $returnString;				
}

add_action('admin_menu', 'add_gifterator_menu');
 
function add_gifterator_menu() {
	add_menu_page( 'Prezenty', 'Prezenty', 'edit_pages', 'prezenty', 'build_gifterator_menu', null, 21);
}

function build_gifterator_menu() {
	
	?>
	<div class="wrap">
		<form method="post">
			<h2>Forma dodawania prezentów</h2>		
		
			<table class="form-table">
				<tr >
					<th scope="row"><label for="present_image">Zdjęcie prezentu</label></th>
					<td><input type="text" id="present_image" name="present_image" class="regular-text"></td>
				</tr>
				<tr >
					<th scope="row"><label for="present_description">Opis prezentu</label></th>
					<td><textarea name="present_description" rows="7" cols="50"></textarea></td>
				</tr>		
			</table>
			
			<p class="submit"><input type="submit" class="button button-primary" name="submit" value="Zapisz prezent"></p>			
		</form>		
	</div>
	
	<?php
	
	global $wpdb;
	
	get_saved_presents();
	
	if(isset($_POST['present_description']) && !empty($_POST['present_description'])) {

		$presentImage = $_POST['present_image'];
		
		$presentDescription = $_POST['present_description'];
		
		$saveData = array(
			'image' => $presentImage,
			'description' => $presentDescription,
			'reserved' => false
		);
		
		$wpdb -> insert('wp_presents', $saveData);
	}
	
	if (!empty($_GET['present_id']))
	{
		$wpdb->delete( 'wp_presents', array( 'id' => $_GET['present_id'] ));
		
	}
	
}

function get_saved_presents() {
	global $wpdb;
	
	$savedPresents = $wpdb->get_results("
											SELECT *
											FROM wp_presents");
											
	?>
	
	<table class="wp-list-table widefat fixed posts">
		<thead>
			<tr>
				<th class="manage-column">Zdjęcie</th>
				<th class="manage-column">Opis</th>
				<th class="manage-column">Zarezerwowany?</th>
				<th class="manage-column">Usuwanie</th>
			</tr>
		</thead>
		<tbody id="the-list">
		<?php
		
		$i = 0;
		
		foreach($savedPresents as $present)
		{?>
			<tr class="<?php echo get_css_class($i)?>">
			<td><img src="<?php echo $present-> image?>" alt="" width="150" height="150"></td>
			<td><?php echo $present -> description ?></td>
			<td><?php echo get_resetvation_status($present -> reserved); ?></td>
			<td><a href="<?php echo menu_page_url('prezenty', false) . '&present_id='. $present -> id; ?>">Usuń</a></td>
			</tr>
			<?php
			
			$i += 1;
		
		}
 	 	?>
		</tbody>
	</table>
<?php
	
}

function get_css_class($i) {
	
	if($i % 2 == 0) {
		return 'alternate';
	}
}

function get_resetvation_status($reserved) {
	if($reserved) {
		return '<span style="color:green"><strong>Zarezerwowany</strong></span>';
	}
	
	return '<span style="color:red"><strong>Nie zarezerwowany</strong></span>';
}

function create_presents_table() {
	
	global $wpdb;

	$charset_collate = '';

	if ( ! empty( $wpdb->charset ) ) {
  		$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
	}

	if ( ! empty( $wpdb->collate ) ) {
  		$charset_collate .= " COLLATE {$wpdb->collate}";
	}
	
	$table_name = $wpdb->prefix . 'presents';

	$sql = "CREATE TABLE $table_name (
  		id mediumint(9) NOT NULL AUTO_INCREMENT,
  		image text NULL,
  		description longtext NOT NULL,
  		reserved tinyint(1) DEFAULT 0 NOT NULL,
  		UNIQUE KEY id (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );	
}

register_activation_hook( __FILE__, 'create_presents_table' );

?>