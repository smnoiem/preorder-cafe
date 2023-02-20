<?php
    class Opinion_poll_model{
        private $db_handle; private $host = "localhost"; private $db = "opinion_poll";
        private $uid = "root"; private $pwd = "melody";
        public function __construct(){
            $this->db_handle = mysqli_connect($this->host, $this->uid, $this->pwd);
            if(!$this->db_handle) die("Unable to connect MySql" . mysqli_error());
            if(!mysqli_select_db($this->db_handle, $this->db))
                die("Unable to select database: " . mysqli_error());
        }
        private function execute_query($sql_stmt){
            $result = mysqli_query($db_handle,$sql_stmt); //execute SQL statement
            return !$result ? FALSE : TRUE;
        }
    }
 ?>
