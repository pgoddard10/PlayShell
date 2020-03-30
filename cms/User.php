<?php

class User {

    public $username = "";
    public $staff_id = "";
    public $display_name = "";
    public $roles = "";

	public function hash_password($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    public function set_session(){
        $_SESSION['username'] = $this->username;
    }
}

?>