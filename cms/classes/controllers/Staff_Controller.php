<?php

require_once('classes/models/Role_Model.php');
//require_once('classes/controllers/Staff_Controller.php');
require_once('classes/models/Staff_Model.php');

/**
 * Short description of class Staff_Controller
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 */
class Staff_Controller
{
    private $staff_model = null;
    public $all_staff = null;
    public $role_model = null;

    /**
     * Short description of method __construct
     * @param  String db_file
     */
    function __construct() {
        $this->staff_model = new Staff_Model();
        $this->role_model = new Role_Model();
    }

    /**
     * Short description of method create_new
     *
     * @param  array<> staff_data
     * @return Integer
     */
    public function create_new($first_name, $last_name, $username, $password, $repeat_password, $email, $roles)
    {
        $returnValue = -1;
        if($password != $repeat_password) $returnValue =-2; //password mis-match
        if($this->staff_model->create_new($first_name, $last_name, $username, $password, $repeat_password, $email, $roles)==0) $returnValue = 0;
        return $returnValue;
    }

    /**
     * Short description of method edit
     *
     * @param  array<> staff_data
     * @return Integer
     */
    public function edit($staff_id, $first_name, $last_name, $username, $password, $repeat_password, $email, $active, $roles)
    {
        $returnValue = -1;
        if($password != $repeat_password) $returnValue =-2; //password mis-match
            else {
            //if this person is staff DB manager in DB
                //check how many staffDBmanagers exist in total
                //if total==1
                //$returnValue = -2; //cannot remove the role for last DB manager
            if($this->staff_model->edit($staff_id, $first_name, $last_name, $username, $password, $repeat_password, $email, $active)==0) {
            //if($this->staff_model->edit_roles($roles))
                $returnValue = 0;
            //else $returnValue = -4;
            }
            else $returnValue = -3;
        }
        return $returnValue;
    }

    /**
     * Short description of method deactivate
     *
     * @param  staff_id
     * @return Integer
     */
    public function deactivate($staff_id)
    {
        $returnValue = -1;
        //if this person is staff DB manager in DB
            //check how many staffDBmanagers exist in total
            //if total==1
            //$returnValue = -2; //cannot deactivate last DB manager
        if($this->staff_model->deactivate($staff_id)==0) $returnValue = 0;
        return $returnValue;
    }

    /**
     * Short description of method get_all_staff
     *
     */
    public function populate_all_staff()
    {
        $this->all_staff = null;
        $model = new Staff_Model();
        $usernames = $model->get_all_usernames();
        //print('<pre>'.print_r($usernames,true).'</pre>');
        foreach($usernames as $username) {
            $staff_member = new Staff_Model();
            $staff_member->populate_from_db($username[0]);
            $this->all_staff[] = $staff_member;
        }
        //print('hello: <pre>'.print_r($this->all_staff,true).'</pre>');
    }

    /**
     * Short description of method get_all_roles
     *
     * @return array<Role_model>
     */
    public function get_all_roles()
    {
        $returnValue = null;
        return $returnValue;
    }

} /* end of class Staff_Controller */

?>