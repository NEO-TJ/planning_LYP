<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// --------------------------------------------------- Asset Tool.
  // Asset url.
  if ( ! function_exists('asset_url')){
    function asset_url($asset_name, $module_name = NULL) {
      $oBject =& get_instance();
      $base_url = $oBject->config->item('base_url');

      $asset_location = $base_url.'assets/';

      if(!empty($module_name)) {
          $asset_location .= 'modules/'.$module_name.'/';
      }

      $asset_location .= $asset_name;

      return $asset_location;
    }
  }
  // Asset css location
  if ( ! function_exists('my_css_asset')){
    function my_css_asset($asset_name, $module_name = NULL, $attributes = array()) {
      $attribute_str = _parse_asset_html($attributes);
    
      return '<link href="'.asset_url($asset_name, $module_name).'" rel="stylesheet" type="text/css"'.$attribute_str.' />';
    }
  }
  // Asset images location
  if ( ! function_exists('my_image_asset')){
    function my_image_asset($asset_name, $module_name = '', $attributes = array()) {
      $attribute_str = _parse_asset_html($attributes);
      return '<img src="'.asset_url($asset_name, $module_name).'"'.$attribute_str.' />';
    }
  }
  // Asset javaScript location
  if ( ! function_exists('my_js_asset')){
    function my_js_asset($asset_name, $module_name = NULL) {
      return '<script type="text/javascript" src="'.asset_url($asset_name, $module_name).'"></script>';
    }
  }
// --------------------------------------------------- End Asset Tool.



// --------------------------------------------------- Create $ Get pagination html code.
	function getPaginationHtml($totalRow=0, $paginationLimit=50, $startRecNoOfPage=0) {
    $CI =& get_instance();
    $CI->load->library('pagination');

		$config = array();
		$config['full_tag_open'] = "<ul class='pagination'>";
		$config['full_tag_close'] ="</ul>";
		$config['num_tag_open'] = "<li>";
		$config['num_tag_close'] = "</li>";
		$config['cur_tag_open'] = "<li class='disabled'><li class='active'><a href='#'>";
		$config['cur_tag_close'] = "<span class='sr-only'></span></a></li>";
		$config['next_tag_open'] = "<li>";
		$config['next_tagl_close'] = "</li>";
		$config['prev_tag_open'] = "<li>";
		$config['prev_tagl_close'] = "</li>";
		$config['first_tag_open'] = "<li>";
		$config['first_tagl_close'] = "</li>";
		$config['last_tag_open'] = "<li>";
		$config['last_tagl_close'] = "</li>";
		$config["base_url"] = "";
		$config["first_url"] = "#/0";
		$config["total_rows"] = $totalRow;
		$config["per_page"] = $paginationLimit;
		$config["uri_segment"] = 3;

		$config['setCurPage'] = $startRecNoOfPage;
		$CI->pagination->initialize($config);

		$paginationLinks = $CI->pagination->create_links();

		return $paginationLinks;
  }
// --------------------------------------------------- End Create $ Get pagination html code.

// --------------------------------------------------- Sql Create Limit and Offset.
  function createSqlLimitOffset($limit=NULL, $offset=NULL) {
    $sqlLimit = "";
    if(($limit !== NULL) && ($limit > 0)) {
      $offset = ( (($offset !== NULL) && ($offset >= 0)) ? $offset : 0);
      $sqlLimit = " LIMIT " . $offset . ", " . $limit;
    }

    return $sqlLimit;
  }
// --------------------------------------------------- End Sql Create Limit and Offset.