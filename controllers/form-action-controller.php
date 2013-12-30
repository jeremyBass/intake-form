<?php
function proccessPost(){
	
	$params=array();
	$entryid=$_POST['id'];
    foreach($_POST as $key=>$value){
           $$key=$value;
		   $params[$key]=$value;
    }
	echo 'form was filled out<br/>';
	$creation_date 	= strtotime("now");
	
	//filter internship interactions
	//should be a model for this
	
	//var_dump($_POST['internship']);
	
	
	
	$internship=$params['internship'];
	$internships=array();
	foreach($internship as $id=>$entry){
		if(isset($entry['date']) && $entry['date']!="" && $entry['remove']!=1){
			$internships[]=array(
				'date'=>"".$entry['date'],
				'placed'=>"1",
			);
		}
	}
	$params['internship']=$internships;
	
	
	
	$counseling=$params['counseling'];
	$counselings=array();
	foreach($counseling as $id=>$entry){
		if(isset($entry['date']) && $entry['date']!="" && $entry['remove']!=1){
			$counselings[]=array(
				'date'=>"".$entry['date'],
				'mock_interviews'=>"".isset($entry['mock_interviews'])?$entry['mock_interviews']:"",
				'career_counseling'=>"".isset($entry['career_counseling'])?$entry['career_counseling']:"",
			);
		}
	}
	$params['counseling']=$counselings;	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	//var_dump($params);
	
    $jsonObj = json_encode ($params); // this is all of the form POST data serialized
	$form_object	= $jsonObj;
	
	//var_dump($form_object);die();
	
	
	//make your db connection then
	// Create connection

	/*$db = mysqli_connect('localhost', 'root', 'blank');
	if (!$db) {
	   die('Could not connect: ' . mysqli_connect_error());
	}*/
    $db = generalform::getDb();
    
    
    
	echo 'Connected successfully<br/>';
	$dbname = 'Intake';
     
	//is type a boolane? and is equal to false is the same as === false
	if ($db->select_db($dbname) === false) {
		$sql="CREATE DATABASE ".$dbname;
		if (mysqli_query($db,$sql)){
		  echo "Database ".$dbname." created successfully<br/>";
		  mysqli_close($db);//close it cause why not
		  $db = mysqli_connect('localhost', 'root', 'blank',$dbname);
		} else {
		  echo "Error creating database: " ;
		  die();
		}
	}
	//check for table
	// Select 1 from table_name will return false if the table does not exist.   
    
    $table = 'formdata';
	$sql = "SHOW TABLES LIKE '".$table."'";
    if(@mysqli_num_rows(mysqli_query($db,$sql))==1) {
          echo "Table exists";
	} else { 
		//if table not exists create
		echo "Table does not exist-- going to create table<br/>";
		echo "".""."";
		$sql = "CREATE TABLE ".$table." (
				`id` int NOT NULL AUTO_INCREMENT,
				`uh_id` varchar(8),		     	
				`last_name`  varchar(255)	NOT NULL,
				`first_name` varchar(255)	NOT NULL,
				`dob` varchar(255) NOT NULL,
				`address` varchar(255) NOT NULL,
				`city` varchar(255)     NOT NULL,
				`state` varchar(255) NOT NULL,
				`zip` varchar(10)  NOT NULL,
				`form_object` text,
				`creation_date` timestamp,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=latin1
		";
		if (mysqli_query($db,$sql)) {
		  echo "table ".$table." created successfully<br/>";
		} else {
		  echo "Error creating table: run it again<br/>" ;
		  echo $db->error;
		}
	}
	//,$creation_date,,,$first_name,$birth_date,$address,$city,$zip
	//now insert row :D yes it will
	//do a select of the data, if it exist then do nothing
	//if not there then 
	
	$update = isset($_POST['id'])&&$_POST['id']>0;
	if($update){
		$sql = "UPDATE ".$table." SET 
		uh_id='%s',first_name='%s',last_name='%s',dob='%s',address='%s',city='%s',state='%s',zip='%s',form_object='%s'";
	}else{
		$sql = "INSERT INTO ".$table." 
			(uh_id,first_name,last_name,dob,address,city,state,zip,form_object)
			VALUES ('%s','%s','%s','%s','%s','%s','%s','%s','%s')";
	}
	$sql = sprintf(
		$sql,
		$db->real_escape_string($uh_id),
		$db->real_escape_string($first_name),
		$db->real_escape_string($last_name),
		$db->real_escape_string($dob),
		$db->real_escape_string($address),	
		$db->real_escape_string($city),
		$db->real_escape_string($state),
		$db->real_escape_string($zip),
		$db->real_escape_string($form_object)
	);
	isset($_POST['id'])&&$_POST['id']>0?$sql.=sprintf(" WHERE `id`=%s",$entryid):"";


	if (mysqli_query($db,$sql)){
	  $mess="Inserted row successfully for ".$first_name." ".$last_name."<br/>";//.$sql;
	  generalform::setMessage($mess,"success");
	} else {
	  $mess="Error inserting rows: run it again<br/>" ;
	  $mess.=$db->error;
	  generalform::setMessage($mess,"err");
	}

	generalform::closeDbConnection();
	generalform::redirect('form', isset($_POST['id'])&&$_POST['id']>0?array('id'=>($_POST['id']) ):"" );
	
}


$postValid = generalform::validatePOST( array("last_name",'first_name','dob','address','city','state','zip') );
if( $postValid ){
    proccessPost();
}else{
	// we will have a conflict of the new post and the old data
	// to get around this we need to first have the queried data
	//in the array, then loop over the post and merge it with the record
	// when we do this we can overwrite old data from the query with the
	// post .
	
	//do query if $_GET['id'] exist
	
	// if id exists then loop over post
	// while looping over post assign to the 
	// row IE: if( isset($_POST['dob']) )$row['dob'] = $_POST['dob'];
	// while looping over the data and reassigning it, then create vars on
	// the fly IE: $$key= $row['dob'];
	// which then produces $dob="the row value";
	if(isset($_GET['id'])){
        $entry= generalform::getEntry($_GET['id']);
		$formData = json_decode ($entry['form_object']);
		$id=$_GET['id'];
        foreach($formData as $key=>$value){
               $$key=$value;
        }
		$entry=$formData;
    }
	
    

    
    
    if(count($_POST)>0){
        foreach($_POST as $key=>$value){
               $$key=$value;
        }
        generalform::setMessage("There were missing required fields","err");
    }
    include_once('veiws/pages/form.php');
}//end of validation if statement 

