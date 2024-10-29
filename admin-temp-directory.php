<?php
/*
Plugin Name: Admin Temp Directory
Description: Výpis obsahu vybraného adresáře a možnost stahování souborů. <strong>Vyvinuto a určeno pro pure-heart.zaantar.eu</strong>
Version: 1.2
Author: Zaantar
Author URI: http://zaantar.eu
License: GPL2
*/

/*
    Copyright 2010 Zaantar (email: zaantar@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* ************************************************************************* *\
    WORDPRESS ADMIN
\* ************************************************************************* */


add_action( 'admin_menu','atd_add_admin_menu' );

function atd_add_admin_menu() {
	add_options_page( __( 'Admin Temp Dir Options', ATD_TEXTDOMAIN ), __( 'Admin Temp Dir', ATD_TEXTDOMAIN ), 'manage_options', __FILE__, 'atd_options_page' );    
    add_submenu_page('index.php', atd_get_option( 'menu_title' ), atd_get_option( 'menu_title' ), 'read', 'temp-directory', 'atd_page');
}


function atd_page() {
	?>
    <div class="wrap">
        <h2><?php echo atd_get_option( 'menu_title' ); ?></h2>
		<?php
			if( current_user_can( 'manage_options' ) ) {
				?>
				<h3><?php _e( 'Admin directory', ATD_TEXTDOMAIN ); ?></h3>
				<?php 
				atd_print_superadmin_dir();
			}				
			if( current_user_can( atd_get_option( 'cap_dir_cap' ) ) ) {
				?>
				<h3><?php echo atd_get_option( 'cap_dir_title' ); ?></h3>
				<?php atd_print_toporg_dir(); ?>
				<h3><?php _e( 'Common Directory', ATD_TEXTDOMAIN ); ?></h3>
				<?php
			}
			atd_print_common_dir();
		?>
	</div>
	<?php
}


function atd_get_base_dir() {
	$upload_dir = wp_upload_dir();
	return $upload_dir['basedir'];
}


function atd_get_base_url() {
	$upload_dir = wp_upload_dir();
	return $upload_dir['baseurl'];
}


function atd_print_superadmin_dir() {
	$superadmin = atd_get_option( 'atd_admin_dir' );
	$dir = atd_get_base_dir().$superadmin;
	$url = atd_get_base_url().$superadmin;
	atd_print_dir( $dir, $url );
}


function atd_print_toporg_dir() {
	$toporg = atd_get_option( 'atd_cap_dir' );
	$dir = atd_get_base_dir().$toporg;
	$url = atd_get_base_url().$toporg;
	atd_print_dir( $dir, $url );
}


function atd_print_common_dir() {
	$common = atd_get_option( 'atd_common_dir' );
	$dir = atd_get_base_dir().$common;
	$url = atd_get_base_url().$common;
	atd_print_dir( $dir, $url );
}


define( 'ATD_OPTIONS', 'admin_temp_dir' );

function atd_options_page() {
	?>
	<div id="wrap">
		<h2><?php _e( 'Admin Temp Directory settings', ATD_TEXTDOMAIN ); ?></h2>
		<form method="post" action="options.php">
			<?php
				settings_fields( ATD_OPTIONS );
				do_settings_sections( ATD_OPTIONS );
			?>
			<p class="submit">
				<input type="submit" value="<?php _e( 'Save', ATD_TEXTDOMAIN ); ?>" />
			</p>
		</form>
	</div>
	<?php
}


add_action( 'admin_init', 'atd_register_setting' );


function atd_register_setting() {
	add_settings_section( ATD_OPTIONS, __( 'Main settings', ATD_TEXTDOMAIN ), 'atd_main_section_text', ATD_OPTIONS );
	add_settings_field( 'atd_admin_dir', __( 'Admin directory' , ATD_TEXTDOMAIN ), 'atd_field_admin_dir', ATD_OPTIONS, ATD_OPTIONS );
	add_settings_field( 'atd_cap_dir', __( 'Directory for users with the special capability' , ATD_TEXTDOMAIN ), 
		'atd_field_cap_dir', ATD_OPTIONS, ATD_OPTIONS );
	add_settings_field( 'atd_common_dir', __( 'Directory for common users' , ATD_TEXTDOMAIN ), 'atd_field_common_dir', ATD_OPTIONS, ATD_OPTIONS ); 
	add_settings_field( 'menu_title', __( 'ATD menu title' , ATD_TEXTDOMAIN ), 'atd_field_menu_title', ATD_OPTIONS, ATD_OPTIONS ); 
	add_settings_field( 'cap_dir_title', __( '\'Capability directory\' title' , ATD_TEXTDOMAIN ), 'atd_field_cap_dir_title', ATD_OPTIONS, ATD_OPTIONS ); 
	add_settings_field( 'cap_dir_cap', __( 'Special capability' , ATD_TEXTDOMAIN ), 'atd_field_cap_dir_cap', ATD_OPTIONS, ATD_OPTIONS ); 
	register_setting( ATD_OPTIONS, ATD_OPTIONS );
}


function atd_main_section_text() {
	return '<p>'.printf( __( 'Please input only existing directory paths relative to %s.', ATD_TEXTDOMAIN ), '<code>'.atd_get_base_dir().'</code>' ).'</p>';
}


function atd_field_admin_dir() {
	$options = get_option( ATD_OPTIONS );
	?>
	<input id="atd_admin_dir" name="<?php echo ATD_OPTIONS; ?>[atd_admin_dir]" type="text" value="<?php echo $options['atd_admin_dir']; ?>" />
	<?php
}


function atd_field_cap_dir() {
	$options = get_option( ATD_OPTIONS );
	?>
	<input id="atd_cap_dir" name="<?php echo ATD_OPTIONS; ?>[atd_cap_dir]" type="text" value="<?php echo $options['atd_cap_dir']; ?>" />
	<?php
}


function atd_field_common_dir() {
	$options = get_option( ATD_OPTIONS );
	?>
	<input id="atd_common_dir" name="<?php echo ATD_OPTIONS; ?>[atd_common_dir]" type="text" value="<?php echo $options['atd_common_dir']; ?>" />
	<?php
}


function atd_field_menu_title() {
	$options = get_option( ATD_OPTIONS );
	?>
	<input id="menu_title" name="<?php echo ATD_OPTIONS; ?>[menu_title]" type="text" value="<?php echo $options['menu_title']; ?>" />
	<?php
}


function atd_field_cap_dir_title() {
	$options = get_option( ATD_OPTIONS );
	?>
	<input id="cap_dir_title" name="<?php echo ATD_OPTIONS; ?>[cap_dir_title]" type="text" value="<?php echo $options['cap_dir_title']; ?>" />
	<?php
}


function atd_field_cap_dir_cap() {
	$options = get_option( ATD_OPTIONS );
	?>
	<input id="cap_dir_cap" name="<?php echo ATD_OPTIONS; ?>[cap_dir_cap]" type="text" value="<?php echo $options['cap_dir_cap']; ?>" />
	<?php
}

/*function atd_options_validate( $input ) {
	return $input;
}*/


function atd_get_option( $option_name ) {
	$options = get_option( ATD_OPTIONS );
	return $options[$option_name];
}


register_activation_hook(__FILE__, 'atd_add_defaults');


function atd_add_defaults() {
	$tmp = get_option( ATD_OPTIONS );
	if( !is_array( $tmp ) ) {
		$defaults = array(
			'atd_admin_dir' => '/atd/admin',
			'atd_cap_dir' => '/atd/cap',
			'atd_common_dir' => '/atd/common',
			'menu_title' => 'Important information',
			'cap_dir_title' => '',
			'cap_dir_cap' => 'special_capability'
		);
		update_option( ATD_OPTIONS, $defaults );
	}
}

/*
__( , ATD_TEXTDOMAIN )
<?php _e( '', ATD_TEXTDOMAIN ); ?> 
*/


/*****************************************************************************\
		I18N
\*****************************************************************************/


define( 'ATD_TEXTDOMAIN', 'admin-temp-dir' );


add_action( 'init', 'atd_load_textdomain' );

function atd_load_textdomain() {
	$plugin_dir = basename(dirname(__FILE__));
	load_plugin_textdomain( ATD_TEXTDOMAIN, false, $plugin_dir.'/languages' );
}


/* ************************************************************************* *\
    PRINT DIRECTORY CONTENT
    from http://www.liamdelahunty.com/tips/php_list_a_directory.php
\* ************************************************************************* */

function atd_print_dir( $path, $url ) {
	// open this directory 
	//echo $path.'<br/>';
	$myDirectory = opendir( $path );

	// get each entry
	while($entryName = readdir($myDirectory)) {
		$dirArray[] = $entryName;
	}

	//print_r( $dirArray );

	// close directory
	closedir($myDirectory);

	//	count elements in array
	$indexCount	= count($dirArray);
	//echo ("$indexCount files<br>\n");

	// sort 'em
	sort($dirArray);

	// print 'em
	?>
	<table class="widefat" cellspacing="0">
		<thead>
		    <tr>
		        <th scope="col" class="manage-column"><?php _e( 'File', ATD_TEXTDOMAIN ); ?></th>
		        <th scope="col" class="manage-column"><?php _e( 'Type', ATD_TEXTDOMAIN ); ?></th>
		        <th scope="col" class="manage-column"><?php _e( 'Size [B]', ATD_TEXTDOMAIN ); ?></th>
			</tr>
		</thead>
		<tfoot>
		    <tr>
		        <th scope="col" class="manage-column"><?php _e( 'File', ATD_TEXTDOMAIN ); ?></th>
		        <th scope="col" class="manage-column"><?php _e( 'Type', ATD_TEXTDOMAIN ); ?></th>
		        <th scope="col" class="manage-column"><?php _e( 'Size [B]', ATD_TEXTDOMAIN ); ?></th>
			</tr>
		</tfoot>
		<?php
			// loop through the array of files and print them all
			for( $index=0; $index < $indexCount; $index++ ) {
				if (substr("$dirArray[$index]", 0, 1) != "."){ // don't list hidden files
					?>
					<tr>
						<td><a href="<?php echo $url.'/'.$dirArray[$index]; ?>"><?php echo $dirArray[$index]; ?></a></td>
						<td><?php echo filetype( $path.'/'.$dirArray[$index] ); ?></td>
						<td><?php echo filesize( $path.'/'.$dirArray[$index] ); ?></td>
					</tr>
					<?php
				}
			}
		?>
	</table>
	<?php
}

?>
