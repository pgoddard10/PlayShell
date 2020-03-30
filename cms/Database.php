<?php

class Database {
	public $db_file = "";
	
    function __construct($db_file = "") {
        $this->db_file = $db_file;
    }
}

// class Database {

//     public function get_username_and_password() {
//         $user_details['username'] = "pi";
//         $user_details['password'] = '$2y$10$CnBgnliJM54qDiMdEsW8i.1cY5VpsdwwawNEO.VDcbmPaZ5Hx5SMS'; //raspberry
//         return $user_details;
//     }

//     public function get_user_details($username) {
//         //search by username
//         $user_details['first_name'] = "Paul";
//         $user_details['roles'] = array(1,2,3,4,5); //role IDs
//         return $user_details;
//     }
// }
?>