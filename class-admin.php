<?php
 
 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 
 class AdvertsClickTrackerAdmin{
	
	public $tab;
	public $activeTab;
	public $tabName = 'url';
	public $orderDisplay='orderDisplay';
	public $slideshowSpeed='slideshowSpeed';
	public $perPage='perPage';
	public $columns = 'columns';
	public $view = 'view';
	public $desc ='Description';
	public $url ='URL';


		public function createPostType_Cat(){
		  register_post_type( 'webd_adverts',
			array(
			  'labels' => array(
				'name' => __( 'Adverts' ,$this->plugin),
				'singular_name' => __( 'Advert',$this->plugin),
				'search_items' =>  __( 'Search Adverts' ,$this->plugin),
				'all_items' => __( 'All Adverts' ,$this->plugin),
				'parent_item' => __( 'Parent Advert',$this->plugin),
				'parent_item_colon' => __( 'Parent Advert:',$this->plugin ),
				'edit_item' => __( 'Edit Advert',$this->plugin ), 
				'update_item' => __( 'Update Advert' ,$this->plugin),
				'add_new_item' => __( 'Add New Advert' ,$this->plugin ),
				'add_new'            => __( 'New Advert', $this->plugin ),
				'new_item_name' => __( 'New Advert Name',$this->plugin ),
				'new_item'           => __( 'New Advert', $this->plugin ),
				'menu_name' => __( 'Adverts',$this->plugin )	,
			
			  ),
			  'description' => 'Adding and editing my Adverts',
			  'public' => true,
			  'has_archive' => true,
			  'menu_icon'   => 'dashicons-external',
			  'rewrite' => array('slug' => 'webd_adverts'),
			  'supports' => array( 'title', 'thumbnail' ),
				'show_in_rest'       => true,
				'rest_base'          => 'webd_adverts',
				'rest_controller_class' => 'WP_REST_Posts_Controller',	
				'capability_type' => 'page',
				'hierarchical' => true,
				'public' => false,  // it's not public, it shouldn't have it's own permalink, and so on
				'publicly_queryable' => true,  // you should be able to query it
				'show_ui' => true,  // you should be able to edit it in wp-admin
				'exclude_from_search' => true,  // you should exclude it from search results
				'show_in_nav_menus' => false,  // you shouldn't be able to add it to menus
				'has_archive' => false,  // it shouldn't have archive page
				'rewrite' => false,  // it shouldn't have rewrite rules
			)
		  );	  
		}
		
		public function createTables(){
			
			global $wpdb;
			$charset_collate = $wpdb->get_charset_collate();
			$table_name = $wpdb->prefix . "adverts"; 
			$table_name2 = $wpdb->prefix . "advert_clicks";
			$sql = "CREATE TABLE $table_name (
			  id mediumint(9) NOT NULL AUTO_INCREMENT,
			  date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			  post_id mediumint(9) NOT NULL,
			  clicks mediumint(9) NOT NULL,
			  PRIMARY KEY  (id)
			) $charset_collate;";
			$sql2 = "CREATE TABLE $table_name2 (
			  id mediumint(9) NOT NULL AUTO_INCREMENT,
			  post_id mediumint(9) NOT NULL,
			  clicks mediumint(9) NOT NULL,
			  PRIMARY KEY  (id)
			) $charset_collate;";
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
			dbDelta( $sql2 );			
		}	
	
	
	public function adminHeader(){ ?>
		<h2><img src='<?php echo plugins_url( 'images/'.$this->slug.'.png', __FILE__ ); ?>' style='width:100%' />	
		<?php
		//print "<h1>".$this->name."</h1>";
	}
	
	public function proModal(){ ?>
		<div id="AdvertsClickTrackerModal">
		  <!-- Modal content -->
		  <div class="modal-content">
			<div class='clearfix'><span class="close">&times;</span></div>
			<div class='clearfix verticalAlign'>
				<div class='columns2'>
					<center>
						<img style='width:90%' src='<?php echo plugins_url( 'images/'.$this->slug.'-pro.png', __FILE__ ); ?>' style='width:100%' />
					</center>
				</div>
				
				<div class='columns2'>
					<h3>Go PRO and get more important features!</h3>
					<p><i class='fa fa-check'></i> Get a widget to display your content</p>
					<p><i class='fa fa-check'></i> Widget can be displayed as <strong>popup!</strong></p>
					<p><i class='fa fa-check'></i> Categorize your adverts & display per category</p>
					<p><i class='fa fa-check'></i> Set expire date for adverts - display those that haven't expired</p>
					<p><i class='fa fa-check'></i> Set Order of adverts for displaying</p>
					<p><i class='fa fa-check'></i> Display adverts in a slideshow - from the shortcode or the widget</p>
					<p class='bottomToUp'><center><a target='_blank' class='proUrl' href='<?php print $this->proUrl; ?>'>GET IT HERE</a></center></p>
				</div>
			</div>
		  </div>
		</div>		
		<?php
	}
	
	public function adminSettings(){
			$this->adminTabs();	
			?>
			<div class='clearfix'>
			<div class='column-23'>
			
			<form method="post" id='<?php print $this->plugin; ?>Form'  
			action= "<?php echo admin_url( "admin.php?page=".$this->slug ); ?>">
			<?php
			
			if( $this->activeTab == 'info' ) {
				//settings_fields( 'gallery-options' );
				//do_settings_sections( 'gallery-options' );
				?>
				<h2>INFO GUIDE</h2>
				<div id='accordion'>
					<h3>DISPLAY THE ADVERTS  VIA SHORTCODES <i class='fa fa-angle-down'></i></h3>
					<div>
						<p >
						DISPLAY ADS <i class='fa fa-angle-right'></i>  
						<strong>[displayAds ]</strong>
						<hr/>
						</p>
						<p >
						DISPLAY ONLY 1 ADVERT<i class='fa fa-angle-right'></i>  
						<strong>[displayAds title='Advert Name' ]</strong>
						<hr/>
						</p>
						<p >
						DISPLAY ADS IN SPECIFIC ORDER (eg.random) -override default- <i class='fa fa-angle-right'></i>  
						<strong>[displayAds orderby='rand']</strong>
						<hr/>
						</p>						
						<p>
						DEFINE NUMBER OF COLUMNS IN GRID eg(3 columns in each row) -override default- <i class='fa fa-angle-right'></i>  
						<strong>[displayAds column = 'columns3']</strong>, choose between column1,columns2,columns3,columns4, columns5, columns6
						<hr/>
						</p>						
						<p>
						<p class=''>
						DISPLAY ADS + OTHER CONTENT (eg. adverts + pages) <i class='fa fa-angle-right'></i> <span class=' proVersion proSpan'>- PRO VERSION ONLY - </span>
						<strong>[displayAds column='column-3' type='webd_adverts,page'] </strong>
						<hr/>
						</p>							
						<p class=''>
						DISPLAY PER CATEGORY (eg. cat 2) <i class='fa fa-angle-right'></i> <span class='proVersion proSpan'>- PRO VERSION ONLY - </span>
						<strong>[displayAds  category='cat 2']</strong>
						<hr/>
						</p>
						<p class=''>
						DISPLAY ADS THAT EXPIRE AFTER (eg. 25/09/2018) <i class='fa fa-angle-right'></i> <span class='proVersion proSpan'>- PRO VERSION ONLY - </span>
						<strong>[displayAds expire='20180925' column='columns-3' ]</strong>
						<p><strong style='color:red'>By default plugin will display Adverts that have not expired.</strong>. With this feature you can override the default.</p>
						<hr/>
						</p>
						<p class=''>
						DISPLAY ADS  IN SLIDESHOW <i class='fa fa-angle-right'></i> <span class='proVersion proSpan'>- PRO VERSION ONLY - </span>
						<strong>[displayAds column='column-1' type='webd_adverts,page' view='slideshow'] </strong>
						<p>This can be used if you want to Override default setting for shortcode view when adding multiple shortcodes in your content.</p>
						<hr/>
						</p>
					</div>	
					<h3 class='proVersion'>DISPLAY THE ADVERTS VIA WIDGET <i class='fa fa-angle-down'></i> <span class='proVersion proSpan'>- PRO VERSION ONLY - </span> </h3>
					<div>
						<p>
							<b>Go to Appearance --> Widgets, select 'WebD Advertise Pro'</b>
						</p>
						<ol>
							<li>Select a View between modal, list, slideshow</li>
								<ul>
									<li>If modal, select modal width</li>
									<li>If modal, select close button color</li>
									<li>If modal, select if modal should appear to users pressing closing button</li>
									<li>If modal, select duration after page load modal should appear</li>
								</ul>
							
							<li>Select title for the widget</li>
							<li>Select title alignment</li>
							<li>Select title wrapper, between h1,h2,h3,h4,h5,p,b</li>
							<li>Select adverts that belong to category to display those only</li>
							<li>Select advert title to display only that</li>
							<li>Select Content Background Color</li>
							<li>Select Content  Color</li>
							<li>Select Content Alignment</li>
							<li>Select Number of Adverts to Display (-1 for all)</li>
							<li>Select Number of Columns to Display(column-1 for 1, columns-2 for 2 etc, up to 6) </li>
							<li>Select content Animation </li>
						</ol>
					</div>						
				</div>
				
				<?php
			}else{ ?>
				<h3>These are the defaults for adverts display. They can be overriden in the shortcode [displayAds].</h3>
				<?php
				settings_fields( 'general-options' );
				do_settings_sections( 'general-options' );
			}			
			wp_nonce_field($this->plugin);
			submit_button();
			
			?></form>
			<div class='<?php print $this->plugin."Video"; ?>' style='display:none;'>
				<iframe width="90%" height="500" src="https://www.youtube.com/embed/GxSXOditGR0?rel=0" frameborder="0" allowfullscreen></iframe>
			</div>
			
			<div class='result'><?php $this->processSettings(); ?> </div>
			
			</div>
			<div class='column-13 rightToLeft'>
				<div class='proRight'>
				
					<center><a target='_blank' href='<?php print $this->proUrl; ?>'>
					<img style='width:50%' src='<?php echo plugins_url( 'images/'.$this->slug.'-pro.png', __FILE__ ); ?>' style='width:100%' /></a></center>
					<h3>Go PRO and get more important features!</h3>
					<p><i class='fa fa-check'></i> Get a widget to display your content</p>
					<p><i class='fa fa-check'></i> Widget can be displayed as <strong>popup!</strong></p>
					<p><i class='fa fa-check'></i> Categorize your adverts & display per category</p>
					<p><i class='fa fa-check'></i> Set expire date for adverts - display those that haven't expired</p>
					<p><i class='fa fa-check'></i> Set Order of adverts for displaying</p>
					<p><i class='fa fa-check'></i> Display adverts in a slideshow - from the shortcode or the widget</p>
					<p class='bottomToUp'><center><a target='_blank' class='proUrl' href='<?php print $this->proUrl; ?>'>GET IT HERE</a></center></p>
					
				</div>
			</div>
			</div>
	<?php
			
	}

	public function adminTabs(){
			$this->tab = array( 'general' => 'General','info' => 'Guide');
			if($_GET['tab'] ){
				$this->activeTab = $_GET['tab'] ;
			}else $this->activeTab = 'general';
			echo '<h2 class="nav-tab-wrapper" >';
			foreach( $this->tab as $tab => $name ){
				$class = ( $tab == $this->activeTab ) ? ' nav-tab-active' : '';
				echo "<a class='nav-tab".$class." contant' href='?post_type=webd_adverts&page=".$this->slug."&tab=".$tab."'>".$name."</a>";
			}?>
				<a class='nav-tab  <?php print $this->plugin; ?>Toggler' href='#<?php print $this->plugin; ?>Video'>Video</a>
				<a class='nav-tab  proVersion' href='#'>GO PRO</a>
			<?php
			echo '</h2>';		
	}

	
	public function adminFooter(){ ?>	
		
		<hr>
		<br/>
		<center><a target='_blank' class='proUrl' href='<?php print $this->proUrl; ?>'>GET PRO VERSION</a></center>
		<?php 
	}
	
	public function orderDisplay(){
		?>			
				<select  placeholder='Order Display' name="<?php print $this->plugin.$this->orderDisplay;?>" id="<?php print $this->plugin.$this->orderDisplay;?>"  >
						<?php if(!empty ($_POST[$this->plugin.$this->orderDisplay]) ){
							?><option value='<?php echo esc_attr( $_POST[$this->plugin.$this->orderDisplay] ); ?>'><?php echo esc_attr( $_POST[$this->plugin.$this->orderDisplay] ); ?></option><?php
						}elseif(!empty(get_option($this->plugin.$this->orderDisplay)) ){ ?>
								<option value='<?php echo esc_attr(get_option($this->plugin.$this->orderDisplay)); ?>'><?php echo esc_attr(get_option($this->plugin.$this->orderDisplay)) ; ?></option>
						<?php }else ?><option value='none'>Select...</option>
							<option value='ID'>ID</option>
							<option value='title'>title</option>
							<option value='name'>name</option>
							<option value='date'>date</option>
							<option value='rand'>rand</option>
				</select>			
		<?php
	}
	
	public function viewDisplay(){
		?>
			<input type="text" name="<?php print $this->plugin.$this->view;?>" id="<?php print $this->plugin.$this->view;?>" class='proVersion' placeholder='pro version' />	
		<?php
	}

	public function columnsDisplay(){
		?>			
				<select  placeholder='Column Number in Grid' name="<?php print $this->plugin.$this->columns;?>" id="<?php print $this->plugin.$this->columns;?>"  >
						<?php if(!empty ($_POST[$this->plugin.$this->columns]) ){
							?><option value='<?php echo esc_attr( $_POST[$this->plugin.$this->columns] ); ?>'><?php echo esc_attr( $_POST[$this->plugin.$this->columns] ); ?></option><?php
						}elseif(!empty(get_option($this->plugin.$this->columns)) ){ ?>
								<option value='<?php echo esc_attr(get_option($this->plugin.$this->columns)); ?>'><?php echo esc_attr(get_option($this->plugin.$this->columns)) ; ?></option>
						<?php }else ?><option value='none'>Select...</option>
							<option value='column1'>column1</option>
							<option value='columns2'>columns2</option>
							<option value='columns3'>columns3</option>
							<option value='columns4'>columns4</option>
							<option value='columns5'>columns5</option>
							<option value='columns6'>columns6</option>
				</select>			
		<?php
	}
	
	public function slideshowSpeed(){
		?>
			<input type="text" name="<?php print $this->plugin.$this->slideshowSpeed;?>" id="<?php print $this->plugin.$this->slideshowSpeed;?>" class='proVersion' placeholder='pro version' />	
		<?php
	}
	public function perPage(){
		?>
			<input type="number" name="<?php print $this->plugin.$this->perPage;?>" id="<?php print $this->plugin.$this->perPage;?>"  value="<?php if(!empty ($_POST[$this->plugin.$this->perPage]) ){
				?><?php echo esc_attr( $_POST[$this->plugin.$this->perPage] ); ?><?php
			}elseif(!empty(get_option($this->plugin.$this->perPage)) ){ ?><?php echo esc_attr(get_option($this->plugin.$this->perPage)); ?><?php } ?>" placeholder='Adverts to Display' />
		<?php
	}
	
	public function adminPanels(){
		add_settings_section("general", "", null, "general-options");
		add_settings_field('orderDisplay',"Order Display", array($this, 'orderDisplay'),  "general-options", "general");
		add_settings_field('viewDisplay',"Adverts View (grid available, slideshow in PRO)", array($this, 'viewDisplay'),  "general-options", "general");
		add_settings_field('slideshowSpeed',"Slideshow Speed", array($this, 'slideshowSpeed'),  "general-options", "general");
		add_settings_field('columnsDisplay',"Column Number in Grid", array($this, 'columnsDisplay'),  "general-options", "general");
		add_settings_field('perPage',"No.of Adverts to Display", array($this, 'perPage'),  "general-options", "general");		
	
		//register_setting("general", $this->plugin.$this->tabName);
	}
	
	public function processSettings(){
		
		if($_SERVER['REQUEST_METHOD'] == 'POST' && current_user_can('administrator') ){
		
			check_admin_referer( $this->plugin );
			check_ajax_referer($this->plugin);	
			if($_REQUEST[$this->plugin.$this->orderDisplay]){
				update_option($this->plugin.$this->orderDisplay,sanitize_text_field($_REQUEST[$this->plugin.$this->orderDisplay]));
				echo "<p class='success'>".sanitize_text_field($_REQUEST[$this->plugin.$this->orderDisplay])." was added.</p>";
			}

			if($_REQUEST[$this->plugin.$this->perPage]){
				update_option($this->plugin.$this->perPage,sanitize_text_field($_REQUEST[$this->plugin.$this->perPage]));
				echo "<p class='success'>".sanitize_text_field($_REQUEST[$this->plugin.$this->perPage])." was added.</p>";
			}
			
			if($_REQUEST[$this->plugin.$this->columns]){
				update_option($this->plugin.$this->columns,sanitize_text_field($_REQUEST[$this->plugin.$this->columns]));
				echo "<p class='success'>".sanitize_text_field($_REQUEST[$this->plugin.$this->columns])." was added.</p>";
			}
		}
	}

	public function metaBox($post){
		add_meta_box("advertDescription", __('Description',$this->plugin), array($this,"descCreate" ) , "webd_adverts", "normal", "high"); 		
		add_meta_box("advertUrl", __('Info',$this->plugin), array($this,"urlCreate" ) , "webd_adverts", "normal", "high"); 
	}	
	
	public function descCreate($post){
		$desci = get_post_custom($post->ID);
        $desc = $desci[$this->plugin.'desc'][0]; 		
		wp_editor( htmlspecialchars_decode(esc_textarea($desc)) ,$this->plugin.'desc' , $settings = array('textarea_name'=>$this->plugin.'desc' )  );
	}
		
	public function urlCreate($post){
        $custom = get_post_custom($post->ID);
        $url = $custom[$this->plugin."url"][0];      
        ?>   <input required placeholder='url' name='<?php print $this->plugin."url";?>' value='<?php print esc_attr($url); ?>' /><br/>    <?php	


        $expdate = $custom[$this->plugin."expdate"][0]; 		
        ?>   <a href='#' class='proVersion'> <input placeholder='Expire Date'  class="proVersion" readonly placeholder='Expire Date' name='<?php print esc_attr($this->plugin."expdate");?>' value='<?php print $expdate; ?>' /> </a>

		<?php
	}

	
	public function saveFields(){
			
		global $post;
		if (!empty($_POST[$this->plugin.'desc']) || $_POST[$this->plugin.'desc']=='') {
			$desc=htmlspecialchars(sanitize_textarea_field($_POST[$this->plugin.'desc']) );
			 update_post_meta($post->ID, $this->plugin.'desc', $desc);	       
		}	
		if (!empty($_POST[$this->plugin.'url']) || $_POST[$this->plugin.'url']=='') {
			$url=htmlspecialchars(sanitize_text_field($_POST[$this->plugin.'url']) );
			 update_post_meta($post->ID, $this->plugin.'url', $url);	       
		}		
	}


	public function advert_click_counter() {
		if ( isset( $_POST['nonce'] ) &&  isset( $_POST['post_id'] ) && wp_verify_nonce( $_POST['nonce'], 'advert_click_counter' ) ) {			
				global $wpdb;
				$table= $wpdb->prefix ."adverts";
				$table2= $wpdb->prefix ."advert_clicks";				

				$store_arr["post_id"]= sanitize_text_field($_POST['post_id']);
				$store_arr["clicks"]= 1;
				$store_arr["date"] = current_time('mysql', false);

				$store_arr2["post_id"] =sanitize_text_field($_POST['post_id']);
				$store_arr2["clicks"]= 1;
											
				$wpdb->insert( $table, $store_arr);
				$post_id = $wpdb->get_row( "SELECT * FROM $table2 WHERE post_id = '".$_POST['post_id']."' " );				
				if($post_id->clicks > 0) {
					$clicks = $post_id->clicks + 1;					
						$post_id =  $_POST['post_id'];
						$wpdb->query( $wpdb->prepare("UPDATE $table2 
									SET clicks = %s 
								 WHERE post_id = %s",$clicks, $post_id)
						);				
				}else{
					$wpdb->insert( $table2, $store_arr2);					
				}			
		}else print "Problem!";		
		exit();
	}	
	
	public function advertClickEvent() {
		global $post;
		//if( isset( $post->ID ) ) {
	?>
		<script type="text/javascript" >
		jQuery(function ($) {

			$( '#link_count a' ).on( 'click', function() {
				var ajax_options = {
					action: 'advert_click_counter',
					nonce: '<?php echo wp_create_nonce( 'advert_click_counter'); ?>',
					ajaxurl: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
					post_id: $( this ).attr( "id" )
				};			
				
				var href = $( this ).attr( "href" );
				var redirectWindow = window.open(href, '_blank');   
				$.post( ajax_options.ajaxurl, ajax_options, function(data) {
					redirectWindow.location;
					//alert(data);
				});
				return false;
			});
		});
		</script>
	<?php
	   // }
	}	

	public function deleteAds(){
		// We check if the global post type isn't ours and just return
		global $post_type;  
		global $wpdb;
		if ( $post_type != 'webd_adverts' ) return;

		$table = $wpdb->prefix .'adverts';
		$wpdb->delete( $table, array( 'post_id' => $postid ) );
		
		$table2 = $wpdb->prefix .'advert_clicks';
		$wpdb->delete( $table2, array( 'post_id' => $postid ) );		
	}
	
	public function deleteAdStats(){
		if(isset($_POST['deleteAdStats']) ){
			global $wpdb;
			$table = $wpdb->prefix .'adverts';
			$table2 = $wpdb->prefix .'advert_clicks';
			$delete = $wpdb->query("TRUNCATE TABLE $table");
			$delete2 = $wpdb->query("TRUNCATE TABLE $table2");
		}
	}
	
	public function displayAds($atts){
			ob_start();
						if(!empty(get_option($this->plugin.$this->orderDisplay) )){
							$orderDisplay = esc_attr(get_option($this->plugin.$this->orderDisplay));
						}else $orderDisplay = 'rand';
						if(!empty(get_option($this->plugin.$this->perPage) )){
							$perPage = esc_attr(get_option($this->plugin.$this->perPage));
						}else $perPage = 'rand';						
						if(!empty(get_option($this->plugin.$this->columns) )){
							$columns = esc_attr(get_option($this->plugin.$this->columns));
						}else $columns = '';						
						$atts = shortcode_atts(
							array(
								'orderby'	   => $orderDisplay,
								'order' => 'ASC',
								'posts_per_page'	=> $perPage,
								'column'			=> $columns,
								'title'				=> '',
								'image_size'		=> '',						
							), $atts, 'displayAds' );
				
							global $post;
							
								$post_type = explode(",", $atts['type']);																				
								$posts = get_posts(array(
									'post_type'			=> 'webd_adverts',
									'orderby'			=> sanitize_text_field($atts['orderby']),
									'order' 			=> sanitize_text_field($atts['order']),
									'posts_per_page'	=> (int)$atts['posts_per_page'],
									'title'				=> sanitize_text_field($atts['title']),							  
								));										
							?>						
							<?php  if( $posts ): ?>
							
								<div class='clearfix'>
								<?php foreach( $posts as $post): // variable must be called $post (IMPORTANT) ?>
									<?php setup_postdata($post); ?>

									<div class='<?php print $atts['column']; ?>' id='link_count'>
										<div class='  rowfix <?php print $atts['image_size']; ?>'>
										<?php if($post->post_type =='webd_adverts'){ ?>
											<p><a class='noloading' id='<?php print get_the_ID(); ?>' href="<?php print esc_attr(get_post_meta( get_the_ID(), $this->plugin.'url' , true)); ?>">
												
												<?php the_post_thumbnail($atts['image_size']); ?>
												<h4 class='title'><?php the_title(); ?></h4>
												<?php print esc_attr(get_post_meta( get_the_ID(), $this->plugin."desc" , true)); ?>
												
											</a></p>										
											
										<?php } ?>
									
										</div>
									</div>
								<?php endforeach; ?>
								</div>
								<?php wp_reset_postdata(); ?>
							<?php endif; 
							
				 $output = ob_get_clean();
				 return $output;  
	}
	
	public function adStats(){		
			 if( current_user_can('administrator')){

			 global $wpdb;
			  $table_name = $wpdb->prefix ."adverts";
			  $table_name2 = $wpdb->prefix ."advert_clicks";
			   
			   $clicks = $wpdb->get_results( "SELECT * FROM $table_name2  " ); ?>
			   
			  <?php if($wpdb->num_rows > 0) { ?>
				<form method='POST'>
					<input type='hidden' name='deleteAdStats' value='1' />
					<input type='submit' value='<?php _e('Delete All Ad Stats',$this->plugin); ?>' />					
				</form>	<br/><hr/>
				
				<input type="text" id="myInput" onkeyup="myFunction()" placeholder="<?php _e('Search for adverts..',$this->plugin); ?>"><br/>
				<ul class='clearfix' id="accordion">
					<?php 
					foreach ($clicks as $row){ 
						print "<li><h3>".get_the_title( $row->post_id )." <i> Clicks: ". esc_attr($row->clicks)." </i></h3>";
						print "<div>";
						$ads = $wpdb->get_results( "SELECT  COUNT(*) as c, DATE_FORMAT(date , '%M %Y') as d,date FROM $table_name  WHERE post_id ='".$row->post_id."' GROUP BY DATE_FORMAT(date , '%M %Y') ORDER BY date DESC" );
						
						foreach ($ads as $row1){ ?>
							<?php
								global $post;
								print esc_attr($row1->d) ." : " .esc_attr($row1->c)."<br/>";
							?>
						<?php } 
						
						$adsl = $wpdb->get_results( "SELECT  DATE_FORMAT(date , '%D %M %Y - %H:%i:%s') as d FROM $table_name  WHERE post_id ='".$row->post_id."' ORDER BY date DESC " );					
						print "<p id='".$row->post_id."' class='collapse'>";
						foreach ($adsl as $row){ 				
								print esc_attr($row->d)."<br/>";					
						} 				
						print "</p>";						
						?></div></li> <?php
					}
					print "</ul>";
			    }else print "<p>".__('No statistics found...',$this->plugin)."</p>" ?>				
					<script>
					function myFunction() {
						// Declare variables
						var input, filter, ul, li, a, i;
						input = document.getElementById('myInput');
						filter = input.value.toUpperCase();
						ul = document.getElementById("accordion");
						li = ul.getElementsByTagName('li');

						// Loop through all list items, and hide those who don't match the search query
						for (i = 0; i < li.length; i++) {
							a = li[i].getElementsByTagName("h3")[0];
							if (a.innerHTML.toUpperCase().indexOf(filter) > -1) {
								li[i].style.display = "";
							} else {
								li[i].style.display = "none";
							}
						}
					}
					</script>			   
		<?php }
	}

 }