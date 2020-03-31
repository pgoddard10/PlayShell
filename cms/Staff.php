<?php

class Staff {

    public $username = "";
    public $staff_id = "";
    public $first_name = "";
    public $last_name = "";
    public $email = "";
    public $display_name = "";
    public $roles = "";
    public $active = 0;

	public function hash_password($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    public function set_session(){
        $_SESSION['username'] = $this->username;
    }
}

?>