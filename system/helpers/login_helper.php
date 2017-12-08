<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/***
    * Codeigniter Login Helper
    * For Check login session DRY idea use same in one time and one place
    * link    http://www.teerapuch.com
    * version 0.1
    *
    * How to use
    * 1.place login_helper.php in system/helpers/
    * 2.load in to controller or autoload
    * 3.call function is_logged_in(); in __construct()
    *   any controller you want to check login
    * 4.in check login function must set session for 'is_logged_in'
    *
***/  
function is_logged_in()
{
	// Get Current instance
    $oCi =& get_instance();
    // use $oCi->session instead of $this->session
    $oLogged = $oCi->session->userdata('is_logged_in');
    // check session
    if (!isset($oLogged)) { 
        // if not login
    	$this->load->view('frontend/header');
        $this->load->view('frontend/logout');
        $this->load->view('frontend/footer');
    } else { 
        // if logged in
    	return true; 
    }
}