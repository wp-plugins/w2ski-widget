<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the dashboard.
 */

class W2Ski_Widget {
    
    protected $widget_frame = '';

    public function __construct() {

		$this->define_admin_hooks();
		$this->define_public_hooks();

	}
    
     public function get_widget_frame(){
        return $this->widget_frame;
    }
    
    public function check_for_key(){
        
        $options = get_option( 'w2ski_settings' );
        if ( $options['w2ski_text_field_0'] == '' ){
            return false;
        }
        else return true;    
    }
    
    private function check_for_link(){
        $options = get_option( 'w2ski_settings' );
        if ( isset( $options['w2ski_text_field_1'] ) ){
            return false; //means that the checkbox is ticked to remove the link
        }
        else return true;
    }
    
    public function set_widget_frame( $nokey_but_link = 'nondefault' ){
        $widget_frame_part1 = "<iframe style='float:left;' src='http://www.where2ski.net/widget/?sig=";
        $widget_frame_part2 = "' width='300px' height='400px' frameBorder='0'></iframe>";
        if ( $this->check_for_link() == false ){ $widget_frame_link = ""; }
            else { $widget_frame_link = "<a href='http://where2ski.net' style='float:left; clear:both'>Checkout Where2Ski.net</a> "; }
        
        $the_key = new W2Ski_key;
        if ( $nokey_but_link == 'default'){ $widget_public_key = '999999999'; }
        else { $widget_public_key = $the_key->fetch_API_key(); }
        
        $this->widget_frame = $widget_frame_part1 . $widget_public_key . $widget_frame_part2 . $widget_frame_link;
    }
    
    
        /*
        * Create a shortcode that will output the necessary code. 
        * The code should have this format
        * <iframe src=“http://www.where2ski.net/widget/?sig=xxxxxxxxxx” width=“300px” height=“400px” frameBorder=“0”></iframe>
        */
    public function w2ski_shortcode( $atts ){
        $the_key = new W2Ski_key;
         if ( $the_key->fetch_API_key() == false ) {
             if ( $this->check_for_link() == true ) {
                 $this->set_widget_frame ('default');
                 return $this->get_widget_frame();     
             } else { $widget_message = 'Please add API key';
                      return $widget_message;  }
             
         }
        else{
            $this->set_widget_frame();
            return $this->get_widget_frame();
        }
    }

    /**
     * 
     * Creating the plugin settings page where the user
     * can add the API key from the website owner
     */
    
    public function w2ski_add_admin_menu(){
        add_options_page( 'Where2Ski Widget Configuration', 'W2Ski Widget', 'manage_options', 'w2ski_widget', array( $this, 'w2ski_settings_page'));
    }
    
    function w2ski_settings_page(){
        ?>
            <form action='options.php' method='post' name="w2sform" id="w2sopt">
                <h2> Where2Ski Widget Settings </h2>
            <?php 
                settings_fields ( 'w2ski_widget' );
                do_settings_sections( 'w2ski_widget' );        
                submit_button();
            ?>
        </form>
    <?php
                
            
    }
    
    function w2ski_settings_init(){
            register_setting(  'w2ski_widget', 'w2ski_settings' );
        
           
        
            add_settings_section(
              'w2ski_pluginPage_section',
                __('Widget Activation', 'w2s-stp'),
                array( $this, 'w2ski_settings_section_callback'),
                'w2ski_widget'
            );
        
            add_settings_field(
                'w2ski_text_field_1',
                __( 'Remove link credit', 'w2s-stp'),
                array( $this, 'w2ski_text_field_1_render' ),
                'w2ski_widget',
                'w2ski_pluginPage_section'
            );
        
            add_settings_field(
                'w2ski_text_field_0',
                __( 'Activation key', 'w2s-stp'),
                array( $this, 'w2ski_text_field_0_render' ),
                'w2ski_widget',
                'w2ski_pluginPage_section'
            );
    }
    
    function w2ski_settings_section_link_callback(){
        
        
    }
    
    function w2ski_settings_section_callback(){
        
        $link_text = '<p>Please support this plugin by allowing a credit link after the widget. Thank you for your support</p>';
        echo __($link_text, 'w2s-stp');
        
        
        $code_text = '<p class="w2s-opt">You need to contact the Where2Ski.net website admin and get an activation code from him.</p>';
        echo __($code_text, 'w2s-stp');
        
        
        
    }
    
    function w2ski_text_field_1_render(){
            $options = get_option( 'w2ski_settings' );
            if ( !isset( $options['w2ski_text_field_1'] )) {
                $options['w2ski_text_field_1'] = 0;
            }
        
            $html = '<input type="checkbox" name="w2ski_settings[w2ski_text_field_1]" value="1"' . checked( 1, $options['w2ski_text_field_1'], false ) . '/>';
           echo $html;
    }
    
    function w2ski_text_field_0_render(){
            $options = get_option( 'w2ski_settings' );
            ?>
            <input type='text' class="w2s-opt" name='w2ski_settings[w2ski_text_field_0]' value='<?php echo $options['w2ski_text_field_0'];?>'> <?php 
            if ($this->check_for_link() == true) { 
                $support_text = "<p class='w2s-opt2'>There is no need for a key because you are supporting us :)</p>";
                echo $support_text; }
            
        
    }
    
    
    
    private function define_public_hooks(){
        
        add_shortcode('w2ski', array($this, 'w2ski_shortcode') );
    }
    
    private function define_admin_hooks(){
        
        add_action( 'admin_menu' , array($this, 'w2ski_add_admin_menu') );
        add_action( 'admin_menu', array($this, 'w2ski_settings_init') );
        add_action( 'admin_enqueue_scripts', array($this, 'w2ski_load_admin_js') );
        add_action( 'admin_enqueue_scripts', array($this, 'w2ski_load_admin_css') );
        add_action( 'widgets_init', array($this, 'w2ski_load_widget') );
    }
    
    public function w2ski_load_admin_js(){
        wp_register_script( 'jquery_validator', 'http://cdn.jsdelivr.net/jquery.validation/1.13.1/jquery.validate.min.js', false, null, true );
        wp_register_script( 'custom_w2s_js', plugins_url('/scripts/w2ski-validator.js', __FILE__), false, null, true );

        wp_enqueue_script( 'jquery_validator' );
        wp_enqueue_script( 'custom_w2s_js' );
    }
    
    public function w2ski_load_admin_css(){
        wp_register_style( 'custom_w2s_css', plugins_url('/css/w2s.css', __FILE__), false, null);
        
        if ($this->check_for_link() == true ) { wp_enqueue_style('custom_w2s_css'); }
    }
    
    function w2ski_load_widget() {
        if ( ($this->check_for_key() == true) || ($this->check_for_link() == true) ) { register_widget( 'W2S_Widget' ); }
        else return;
        
    }
   
}

class W2Ski_key {
    
    protected $W2SKI_API;
    
     public function __construct(){
         $this->init();
     }
    
    public function get_W2SKI_API(){
        return $this->W2SKI_API;
    }
    public function set_W2SKI_API( $API_KEY ){
        $this->W2SKI_API = $API_KEY;
    }
    public function get_W2SKI_API_KEYVALUE(){
        //the first field from options page which is used for entering the key by the user 
        return sanitize_text_field( $this->W2SKI_API['w2ski_text_field_0'] );
    }
    
    public function init(){
        $this->set_W2SKI_API ( get_option( 'w2ski_settings') );
    }
    
    public function fetch_API_key(){
         if ( $this->get_W2SKI_API() == ''){
            return false; //no API key issset
        }
        else{
            return $this->get_W2SKI_API_KEYVALUE();
        }
    }    
    
    
}

/**
 * Adds Foo_Widget widget.
 */
class W2S_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'w2s_widget', // Base ID
			__( 'W2Ski Widget', 'text_domain' ), // Name
			array( 'description' => __( 'Place the W2Ski widget', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
        $the_widget_frame = new W2Ski_Widget;    
        $the_widget_frame->set_widget_frame();
        echo $the_widget_frame->get_widget_frame();
     
        
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'New title', 'text_domain' );
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}

} // class Foo_Widget


