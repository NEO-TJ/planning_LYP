<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		// set default data to view
        $this->data = array();
	}

    public function index()
    {
        echo "<h1>Hello From User</h1>";
    }

}
?>
