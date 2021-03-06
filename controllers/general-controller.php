<?php
require_once('config.php');
class generalform {
    public static $connection = null;
    public static function validatePOST($required=array()){
        $valid=true;
        foreach($required as $watch){
            if(!isset($_POST[$watch]) || (isset($_POST[$watch]) && empty($_POST[$watch])) ){
               $valid=false; 
            }
        }
        return $valid;
    }

	public static function redirect($location,$arg){
		$host  = $_SERVER['HTTP_HOST'];
		$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		$location= trim($location,'/');
		
		$params=""; $i=0;
		if(count($arg)>0){
			foreach($arg as $key=>$val){
				$params.=($i>0?"&":"?")."{$key}={$val}";	
				$i++;
			}
		}
		
		
		$url = "http://$host$uri/$location.php$params";
		header("Location: $url");
		exit();
	}


    /* messaging */
    public static function setMessage($message,$level="warn"){
		session_start();
        switch($level) {
            case "warn":
                $_SESSION['warnings']=$message;
                break;
            case "err":
                $_SESSION['errors']=$message;
                break;    
            case "success":
                $_SESSION['success']=$message;
                break;
        }
    }
    public static function getMessage(){
		@session_start();
        if(isset($_SESSION['errors']))echo '<h3 class="error">'.$_SESSION['errors']."</h3>";
        if(isset($_SESSION['warnings']))echo '<h3 class="warning">'.$_SESSION['warnings']."</h3>";
        if(isset($_SESSION['success']))echo '<h3 class="success">'.$_SESSION['success']."</h3>";
        if(isset($_SESSION['errors']))unset($_SESSION['errors']);
        if(isset($_SESSION['warnings']))unset($_SESSION['warnings']);
		if(isset($_SESSION['success']))unset($_SESSION['success']);
    }



    public static function makeDbConnection($db=null){
        self::$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, ($db==null?DB_NAME:$db));
        if (!self::$connection) {
           die('Could not connect: ' . mysqli_connect_error());
        }
        return self::$connection;
    }
    public static function closeDbConnection(){
        mysqli_close(self::$connection);
    }

    public static function getDb($db=null){
        if(self::$connection==null){
           self::$connection = self::makeDbConnection($db==null?DB_NAME:$db);
        }
        return self::$connection;
    }


    public static function getEntry($id){
	
		$db = generalform::getDb(DB_NAME);
		$table = 'formdata';
		if(!isset($_POST['searchOn']) && $id>0){
			$query = "SELECT * FROM `".$table."` WHERE ".sprintf(" `id`='%s' ",$id);
		}
		$result = $db->query($query) or die($db->error.__LINE__);
	
		$query_results = array ();
		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$query_results=$row;
			}
		}
        return $query_results;
    }

	public static function createFormField($key,$field,$entry){
		$name=$key.'_'.$field['id'];
		$value=isset($entry->$name)?$entry->$name:'';
		$html="";
		switch ($field['type']) {
			case 'date':
				$html .= "<label>".$field['title'].": <br/>";
				$html .= "<input type='date' name='".$name."' value='".$value."' /></lable>";
				break;
			case 'text':
				$html .= "<label>".$field['title'].": <br/>";
				$html .= "<input type='text' name='".$name."' value='".$value."' /></lable>";
				break;
			case 'radio':
				$html .= "<label>".$field['title'].": <br/>";
				foreach($field['option'] as $k =>$v){
					$html .= "<input type='radio' name='".$name."' ". ($value==$k ?"checked":"")." value='".$k."' /> ".$v."</lable>";
				}
				break;
			case 'select':
				$html .= "<label>".$field['title'].": <br/>";
				$html .= "<select name='".$name."'>";
				foreach($field['option'] as $k =>$v){
					$html .= "<option ". ($value==$k ?"selected":"")." value='".$k."' > ".$v."</option>";
				}
				$html .= "</select></lable>";
				break;
			case 'textarea':
				$html .= "<label>".$field['title'].": <br/>";
				$html .= "<textarea name='".$name."'>".$value."</textarea></lable>";
				break;
				break;
		}
		return $html;
	}

}



?>