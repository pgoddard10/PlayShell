<?php

require_once('classes/models/Item_Model.php');

/**
 * Short description of class Item_Controller
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 */
class Item_Controller
{
    private $item_model = null;
    public $all_items = array();

    /**
     * Short description of method __construct
     * @param  String db_file
     */
    function __construct() {
        $this->item_model = new Item_Model();
        $this->populate_all_items();
    }

    /**
     * Short description of method create_new
     *
     * @param 
     * @return Integer
     */
    public function create_new($heritage_id, $name, $location, $url, $active, $modified_by)
    {
        $returnValue = -1;//unknown error
        if($this->item_model->create_new($heritage_id, $name, $location, $url, $active, $modified_by)==0) $returnValue = 0;
        return $returnValue;
    }

    /**
     * Short description of method edit
     *
     * @param  
     * @return Integer
     */
    public function edit($item_id, $heritage_id, $name, $location, $url, $active, $modified_by)
    {
        $returnValue = -1; //unknown error
        $this->item_model->populate_from_db($item_id);
        if($this->item_model->edit($item_id, $heritage_id, $name, $location, $url, $active, $modified_by)==0) $returnValue = 0; //successfully edited visitor
        else $returnValue = -2; //error with query
        return $returnValue;
    }

    /**
     * Short description of method delete
     *
     * @param  item_id
     * @return Integer
     */
    public function delete($item_id)
    {
        $returnValue = -1; //unknown error
        $this->item_model->populate_from_db($item_id);
        if($this->item_model->delete($item_id)==0) $returnValue = 0; //successfully deleted the item
        else $returnValue = -2; //error with query
        return $returnValue;
    }

    /**
     * Short description of method populate_all_itemss
     *
     */
    public function populate_all_items()
    {
        $model = new Item_Model();
        $this->all_items = array();
        $item_ids = $model->get_all_item_ids();
        foreach($item_ids as $id) {
            $item = new Item_Model();
            $item->populate_from_db($id[0]);
            $this->all_items[] = $item;
        }
    }


} /* end of class Item_Controller */

?>