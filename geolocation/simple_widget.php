<?php 
abstract class WP_Simple_Widget extends WP_Widget {
	private $_options;
	function WP_Simple_Widget($widget_options, $form_options) {
		$this->_options = $form_options;
		parent::WP_Widget( $widget_options['id'], $widget_options['title'], array( 'description' => $widget_options['description'] ) );
	}
	
	/***
	* Returns a hash of options
	* Required:
	* id
	* title
	* description
	*/
	
	abstract function render($instance);
	
	

	function form($instance) {
		foreach ($this->_options as $option => $options) {
			?>
			<p>
				<label for="<?php echo $this->get_field_id($option); ?>"><?php _e($option . ':'); ?></label>
				<?php if ($options['type'] == 'select') { ?>
				<select class="widefat" id="id="<?php echo $this->get_field_id($option); ?>" name="<?php echo $this->get_field_name($option); ?>" type="text">
				<?php foreach ($options['choices'] as $label => $value) { ?>
					<option value="<?php echo $value; ?>" <?php if ($value == $instance[$option]) echo 'selected'; ?>><?php echo $label; ?></option>
				<?php } ?>
				</select>
				<?php } else { ?> 
				<input class="widefat" id="<?php echo $this->get_field_id($option); ?>" name="<?php echo $this->get_field_name($option); ?>" type="text" value="<?php echo $instance[$option] ?>" />
				<?php } ?>
			</p>
			<?
		}
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		foreach ($this->_options as $option => $options) {
			$instance[$option] = $options['strip'] ? strip_tags($new_instance[$option]) : $new_instance[$option];
			if ($options['number']) $instance[$option] = intval($instance[$option]);
		}
		return $instance;
	}

	function widget($args, $instance) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;
		if ( $title ) echo $before_title . $title . $after_title;

		$this->render($instance);
		echo $after_widget;
	}

}

?>