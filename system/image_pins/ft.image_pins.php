 <?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2011, EllisLab, Inc.
 * @license		http://expressionengine.com/user_guide/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * Image Pins Fieldtype
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Fieldtype
 * @author		Matt Shearing
 * @link		http://www.adigital.co.uk
 */

class Image_pins_ft extends EE_Fieldtype {
    //fieldtype name and version number shown in add-ons/fieldtypes page
    var $info = array(
        'name'        => 'Image Pins',
        'version'    => '1.0'
    );
	
	//array of data points we are using within this field
    var $fields = array('image_src', 'pins', 'alt_text');

    // --------------------------------------------------------------------
    
    /**
     * Display Field on Publish
     *
     * @access    public
     * @param    existing data
     * @return    field html
     *
     */
    function display_field($data)
    {
	    //specify third party theme folder url
	    $theme_folder_url = defined('URL_THIRD_THEMES') ? URL_THIRD_THEMES : ee()->config->slash_item('theme_folder_url') . 'third_party/';
	    //add our fieldtypes folder name onto the end
		$theme_folder_url = $theme_folder_url. 'image_pins/';
		//use the theme folder url and add our javascript file to the cp head
		ee()->cp->add_to_head('<script src="' . $theme_folder_url.'js/functions.js"></script>');
		//use the theme folder url and add our css file to the cp head
		ee()->cp->add_to_head('<link rel="stylesheet" href="' . $theme_folder_url.'css/main.css" type="text/css" media="screen" /> ');
		
		//if data exists for the field
		if ( ! empty($data))
        {
	        //explode the data and organise in variables
            list($image_src, $pins, $alt_text) = explode('|', $data);
        }
        //if data does not exist
        else
        {
	        //loop through each field
            foreach($this->fields as $key)
            {
	            //clear the field
                $$key = '';
            }
        }
        
        //load our language file
        ee()->lang->loadfile('image_pins');
        //specify our field_id
        $form = '<span class="pin-hidden" id="pin_field_id">'.$this->field_id.'</span>';
        //create a table
        $form .= '<table class="pin-fields">';
        
        //loop through each of our fields
        foreach($this->fields as $key)
        {
	        //add a table row
            $form .= '<tr';
            //if we are looking at our pins
            if ($key == "pins") {
	            //hide the row
	            $form .= ' class="pin-hidden"';
            }
            //create a table cell
            $form .= '><td>';
            //use the language file to generate our field label
            $form .= lang($key, $key.'_field_id_'.$this->field_id);
            //next cell
            $form .= '</td><td>';
            //generate our form input field with the current value
            $form .= form_input($key.'_field_id_'.$this->field_id, $$key);
            //end our cell and table row
            $form .= '</td></tr>';
        }
		
		//end of our table
        $form .= '</table>';
        
        $prefix = 'image_pins_';
        
        $channel_id		= (isset($this->settings[$prefix.'channel_id']) && $this->settings[$prefix.'channel_id'] != '') ? $this->settings[$prefix.'channel_id'] : '';
        
        //if we have an image source
        if (! empty($image_src)) {
	        //create a div which is styled and targetted by our JQuery
	        $form .= '<div id="draggables" class="draggables">';
	        //put our image into the div
	        $form .= '<img width="100%" src="'.$image_src.'">';
	        //query the database for our map pins channel
	        $result = ee()->db->select("title, entry_id")
							->from("exp_channel_titles")
							->where(array("channel_id" => $channel_id, "status" => "open"))
							->get();
			//loop through our results
			foreach ($result->result() as $newkey => $newvar) {
				//create marker pin using entry_id and title
				$form .= '<div class="draggable_pins" data-draggable="yes" id="'.$newvar->entry_id.'"><div class="pin-hover">'.$newvar->title.'</div></div>';
			}
			//close the draggables div
		    $form .= '</div>';
		}
        
        //load our pins into an old values field targetted by our JQuery
        $form .= '<div id="old_values" class="pin-hidden">'.$pins.'</div>';
        //return all of the above to our CP form
        return $form;  
    }

    // --------------------------------------------------------------------
        
    /**
     * Replace tag
     *
     * @access    public
     * @param    field data
     * @param    field parameters
     * @param    data between tag pairs
     * @return    replacement text
     *
     */
    function replace_tag($data, $params = array(), $tagdata = FALSE)
    {
	    //if no template param has been specified
	    if (empty($params["pin_template"]))
	    {
		    //show error message on the front end
		    ee()->output->show_user_error('image_map', 'Error: Set the pin_template parameter!');
	    }
	    //if data exists in the field
		if ( ! empty($data))
        {
	        //explode the data and organise into variables
            list($image_src, $pins, $alt_text) = explode('|', $data);
            //load our image
            $ret = '<img src="'.$image_src.'" alt="'.$alt_text.'">';
            //create an unordered list with id for jQuery targetting
            $ret .= '<ul id="image_pins">';
            //json_decode our pins to create an array with child objects
            $pins = json_decode($pins);
            //loop through our array of pins
            foreach ($pins as $key => $var) {
	            //if the pin has a css position
	            if ($var->left != "auto" && $var->top != "auto") {
		            //if there is no user defined class
		            if (empty($params["pin_class"])) {
			            //return a pin with some default styling
			            $ret .= '<li style="left: '.$var->left.'; top: '.$var->top.'; position: absolute; width: 30px; height: 30px; list-style: none; border-radius: 50%; background: #d95353; box-shadow: 0 0 10px rgba(0, 0, 0, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.3);">';
			        //if there is a user defined class available
		            } else {
			            //return a pin using the defined class
			            $ret .= '<li class="'.$params["pin_class"].'" style="left: '.$var->left.'; top: '.$var->top.'; position: absolute;">';
		            }
		            //embed our defined template with the pins entry id
		            $ret .='{embed="'.$params["pin_template"].'" pin_id="'.$key.'"}</li>';
	            }
            }
            //end our unordered list
            $ret .= '</ul>';
            //if no user defined class
            if (empty($params["pin_class"])) {
	            //target the default pin and hide its children
	            $ret .= '<script>$("#image_pins").find("li").each(function(){$(this).children().hide();});</script>';
            }
        }
        //return our pins and image to the template
        return $ret;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Prep data for saving
     *
     * @access    public
     * @param    submitted field data
     * @return    string to save
     */
    function save($data)
    {
	    //create an empty array
		$pins = array();
        
        //loop through each field
        foreach($this->fields as $key)
        {
	        //add each field to the pins array
            $pins[] = ee()->input->post($key.'_field_id_'.$this->field_id);
        }
        
        //implode the array to save as a string in a single field
        return implode('|', $pins);
    }
    
    // --------------------------------------------------------------------
    
    /**
	 * Display settings screen
	 *
	 * @access	public
	 */
	function display_settings($data)
	{
		$prefix = 'image_pins_';
		
		ee()->lang->loadfile('image_pins');
		$channel_id = ( ! isset($data[$prefix.'channel_id'])) ? '' : $data[$prefix.'channel_id'];


		ee()->table->set_heading(array(
			'data' => lang('ft_channel_id'),
			'colspan' => 2
		));

		$this->_row(
			lang('ft_channel_id', $prefix.'channel_id').form_error($prefix.'channel_id'),
			form_dropdown($prefix.'channel_id', $this->_channel_id_options(), $channel_id, 'id="'.$prefix.'channel_id"')
		);

		return ee()->table->generate();
	}
	
	protected function _row($cell1, $cell2 = '', $valign = 'center')
	{
		if ( ! $cell2)
		{
			ee()->table->add_row(
				array('data' => $cell1, 'colspan' => 2)
			);
		}
		else
		{
			ee()->table->add_row(
				array('data' => '<strong>'.$cell1.'</strong>', 'width' => '170px', 'valign' => $valign),
				array('data' => $cell2, 'class' => 'id')
			);
		}
	}
	
	private function _channel_id_options()
	{
		ee()->load->model('file_upload_preferences_model');

		$channel_ids[''] = lang('');
		
		//query the database for our map pins channel
        $result = ee()->db->select("channel_id, channel_title")
						->from("exp_channels")
						->get();
		//loop through our results
		foreach ($result->result() as $newkey => $newvar) {
			$channel_ids[$newvar->channel_id] = $newvar->channel_title;
		}

		return $channel_ids;
	}
	
	function save_settings($data)
	{
		return array(
			'image_pins_channel_id'	=> ee()->input->post('image_pins_channel_id')
		);
	}
}


/* End of file ft.image_pins.php */
/* Location: /add-ons/system/image_pins/ft.image_pins.php */