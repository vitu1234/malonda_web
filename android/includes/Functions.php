<?php

Class Functions{
    
    private $con;
    public $records;
    public $numlinks;
    public $item_per_page =10;
    
    function __construct(){
        
        require_once dirname(__FILE__).'/connection.php';
        
        
        $database = new Connection();
        
        $this->con = $database->openConnection();
    }

    
    
    
       //count total records
    function countAll($query){
        $stmt = $this->con->prepare($query);
        $stmt->execute();
        return $stmt->rowCount();
    }
    //pagination
    function pagenate($query, $page_number){
        $per_page = $this->item_per_page;
        //$this->item_per_page = $per_page;
        
        $numrecords = $this->countAll($query);
        
        $this->pages = $numrecords;  
        
        $position = (($page_number-1) * $per_page);
        
        $sql_pagination = $this->con->prepare($query." LIMIT ".  $position.",".$per_page);
        //echo "SELECT id,fullname FROM users LIMIT $start,$numperpage";
        $sql_pagination->execute();
        
        return $sql_pagination->fetchAll();
    }
	 
    
    //RETRIEVE SINGLE RECORD
    function retrieveSingle($query){
            $stmt = $this->con->prepare($query);
            $stmt->execute();
            return $stmt->fetch();
            
    }
    
    //RETRIEVE SINGLE RECORD
    function retrieveMany($query){
            $stmt = $this->con->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll();
            
    }
	
     /* function to UPDATE sql data*/
    function updateData($table, $data, $where){
        $cols = array();

        foreach($data as $key=>$val) {
            $cols[] = "$key = '$val'";
        }
        $sql = "UPDATE $table SET " . implode(', ', $cols) . " WHERE $where";
        
        
        $stmt = $this->con->prepare($sql);
        if($stmt->execute()){
            return 1;
        }else{
            return 2;
        }

        
    }
    
     /* function to insert into table */
    function insertData($table, $data) {
        $key = array_keys($data);
        $val = array_values($data);
        $sql = "INSERT INTO $table (" . implode(', ', $key) . ") "
             . "VALUES ('" . implode("', '", $val) . "')";

        $stmt = $this->con->prepare($sql);
        
        if($stmt->execute()){
            return 1;
        }else{
            return 2;
        }
    }
    
    function deleteData($table,$where){
        
        $sql = "DELETE FROM $table WHERE $where";
        
        
        $stmt = $this->con->prepare($sql);
        if($stmt->execute()){
            return 1;
        }else{
            return 2;
        }
    }
    
    function random_code(){
        
        $returned =  substr(base_convert(sha1(uniqid(mt_rand())),16,36),0,4);
//        $returned =  substr(base_convert(sha1(uniqid(mt_rand())),16,36),0,$limit);
        $string = mt_rand(1000,9000).$returned;
        return strtoupper($string);
    }
}

?>