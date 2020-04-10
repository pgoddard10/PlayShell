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
     * Short description of method publish
     *
     * @param  item_id
     * @return Integer
     */
    public function publish()
    {
        $returnValue = -1; //unknown error
        
        //initialisation
        if(!file_exists(PUBLISHED_CONTENT)) {
            $fp = fopen(PUBLISHED_CONTENT, 'w');
            fwrite($fp, "");
            fclose($fp);
            chmod(PUBLISHED_CONTENT,0666);
        }
        
        $fp = fopen(PUBLISHED_CONTENT, 'w');
        if(fwrite($fp, $this->JSONify_All_Items())) $returnValue = 0;
        fclose($fp);

        return $returnValue;
    }

    
    /**
     * Short description of method JSONify_All_Items
     *
     * @return void
     */
    public function JSONify_All_Items()
    {
        $data = array();
        if(count($this->all_items)<=0) echo '{"data": []}'; //if array is empty, provide empty JSON for datatables to read correctly.
        else {
            foreach($this->all_items as $item=>$details) {
                $individual_item = array();
                
                $individual_item['item_id'] = $details->item_id;
                if(strlen($details->url) > 1) $individual_item['name_with_url'] = '<a href="'.$details->url.'" target="_blank">' . $details->name .'</a>';
                else $individual_item['name_with_url'] = $details->name;
                $individual_item['name_without_url'] = $details->name;
                $individual_item['heritage_id'] = $details->heritage_id;
                $individual_item['location'] = $details->location;
                $individual_item['created'] = date("d/m/Y \a\\t H:i", strtotime($details->created));
                $last_modified = date("d/m/Y \a\\t H:i", strtotime($details->last_modified));
                if(strlen($details->modified_by) > 1) $last_modified = $last_modified. ' by ' . $details->modified_by;
                else $last_modified = $last_modified. ' by [deleted staff member]';
                $individual_item['last_modified'] = $last_modified;
                if($details->active==1)
                    $individual_item['active'] = 'Yes';
                else
                    $individual_item['active'] = 'No';

                $individual_item['content'] = $details->content;
                    
                $items_as_json = json_encode($details, JSON_HEX_APOS);
                $individual_item['buttons'] = "<a href='#' data-toggle='modal' data-id='$items_as_json' class='editItemModalBox btn-circle btn-sm btn-primary' data-target='#editModalCenter'><i class='fas fa-edit'></i></a>";
                $individual_item['buttons'] = $individual_item['buttons'] . " <a href='#' data-toggle='modal' data-id='$items_as_json' class='deleteItemModalBox btn-circle btn-sm btn-primary' data-target='#deleteItemModalCenter'><i class='fas fa-trash'></i></a>";
                $data["data"][] = $individual_item;
            }
            return json_encode($data, JSON_PRETTY_PRINT );
        }
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