<?php

require_once('classes/models/Visitor_Model.php');

/**
 * Short description of class Visitor_Controller
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 */
class Visitor_Controller
{
    private $visitor_model = null;
    public $all_visitors = array();

    /**
     * Short description of method __construct
     * @param  String db_file
     */
    function __construct() {
        $this->visitor_model = new Visitor_Model();
        $this->populate_all_visitors();
    }

    /**
     * Short description of method create_new
     *
     * @param 
     * @return Integer
     */
    public function create_new($first_name, $last_name, $email, $address_1, $address_2, $address_3, $address_4, $address_postcode)
    {
        $returnValue = -1;//unknown error
        if($this->visitor_model->create_new($first_name, $last_name, $email, $address_1, $address_2, $address_3, $address_4, $address_postcode)==0) $returnValue = 0;
        return $returnValue;
    }

    /**
     * Short description of method edit
     *
     * @param  
     * @return Integer
     */
    public function edit($visitor_id, $first_name, $last_name, $email, $address_1, $address_2, $address_3, $address_4, $address_postcode)
    {
        $returnValue = -1; //unknown error
        $this->visitor_model->populate_from_db($visitor_id);
        if($this->visitor_model->edit($visitor_id, $first_name, $last_name, $email, $address_1, $address_2, $address_3, $address_4, $address_postcode)==0) $returnValue = 0; //successfully edited visitor
        else $returnValue = -2; //error with query
        return $returnValue;
    }

    /**
     * Short description of method delete
     *
     * @param  visitor_id
     * @return Integer
     */
    public function delete($visitor_id)
    {
        $returnValue = -1; //unknown error
        $this->visitor_model->populate_from_db($visitor_id);
        if($this->visitor_model->delete($visitor_id)==0) $returnValue = 0; //successfully deleted visitor
        else $returnValue = -2; //error with query
        return $returnValue;
    }

    /**
     * Short description of method populate_all_visitors
     *
     */
    public function populate_all_visitors()
    {
        $model = new Visitor_Model();
        $this->all_visitors = array();
        $visitor_ids = $model->get_all_visitor_ids();
        foreach($visitor_ids as $id) {
            $visitor = new Visitor_Model();
            $visitor->populate_from_db($id[0]);
            $this->all_visitors[] = $visitor;
        }
    }

} /* end of class Visitor_Controller */

?>