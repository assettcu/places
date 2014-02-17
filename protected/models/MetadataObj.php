<?php

class MetadataObj extends FactoryObj
{
  
  public function __construct($placeid=null,$uniquetable=null)
  {
      if(is_null($placeid)) return;
      parent::__construct("placeid",$uniquetable,$placeid);
  }
  
  public function post_load()
  {
    $this->data = array();
    
    $arr = get_object_vars($this);
    foreach($arr as $index=>$val)
    {
      if($this->is_column($index) and $index!="placeid")
      {
        $this->data[$index]["value"] = $val;
        $displayname = $this->load_metadata_display($index);
        $this->data[$index]["display_name"] = $displayname;
        $metatype = $this->load_metadata_metatype($index);
        $this->data[$index]["metatype"] = $metatype;
		$inputtype = $this->load_metadata_inputtype($index);
		$this->data[$index]["inputtype"] = $inputtype;
		$sorder = $this->load_metadata_sorder($index);
		$this->data[$index]["sorder"] = $sorder;
      }
    }
	uasort($this->data, array('MetadataObj','sort_metadata'));
  }
  
  public function sort_metadata($a, $b)
  {
  	if ($a["sorder"] == $b["sorder"]) {
        return 0;
    }
    return ($a["sorder"] < $b["sorder"]) ? -1 : 1;
  }
  
  public function load_metadata_display($index)
  {
    $conn = Yii::app()->db;
    $query = "
      SELECT      display_name
      FROM        {{metadataext}}
      WHERE       metadata_machinecode = :mdcode
      AND         column_name = :cname;
    ";
    $command = $conn->createCommand($query);
    $command->bindParam(":mdcode",$this->table);
    $command->bindParam(":cname",$index);
    
    return $command->queryScalar();
  }
  
  public function load_metadata_metatype($index)
  {
    $conn = Yii::app()->db;
    $query = "
      SELECT      metatype
      FROM        {{metadataext}}
      WHERE       metadata_machinecode = :mdcode
      AND         column_name = :cname;
    ";
    $command = $conn->createCommand($query);
    $command->bindParam(":mdcode",$this->table);
    $command->bindParam(":cname",$index);
	
    return $command->queryScalar();
  }
  
  public function load_metadata_inputtype($index)
  {
    $conn = Yii::app()->db;
    $query = "
      SELECT      inputtype
      FROM        {{metadataext}}
      WHERE       metadata_machinecode = :mdcode
      AND         column_name = :cname;
    ";
    $command = $conn->createCommand($query);
    $command->bindParam(":mdcode",$this->table);
    $command->bindParam(":cname",$index);
    
    return $command->queryScalar();
  }
  
  public function load_metadata_sorder($index)
  {
    $conn = Yii::app()->db;
    $query = "
      SELECT      sorder
      FROM        {{metadataext}}
      WHERE       metadata_machinecode = :mdcode
      AND         column_name = :cname;
    ";
    $command = $conn->createCommand($query);
    $command->bindParam(":mdcode",$this->table);
    $command->bindParam(":cname",$index);
    
    return $command->queryScalar();
  }
  
  
  public function load_all_metadata_ext()
  {
    $conn = Yii::app()->db;
    $query = "
      SELECT      *
      FROM        {{metadataext}}
      WHERE       metadata_machinecode = :mdcode
	  ORDER BY	  sorder ASC;
    ";
    $command = $conn->createCommand($query);
    $command->bindParam(":mdcode",$this->table);
    return $command->queryAll();
  }
  
  public function add_infotype($data)
  {
    $conn = Yii::app()->db;
  	
  	$metad = new MetadataObj(0,"metadata_".$data["placetype"]);
	if(!$metad->is_column($data["column_name"]))
	{	
	  	if($data["inputtype"]=="text") $type = "VARCHAR(255)";
		else if($data["inputtype"]=="textarea") $type = "TEXT";
		
	    $query = "
	      ALTER TABLE {{metadata_".$data["placetype"]."}}
	      ADD		  ".$data["column_name"]." $type;
	    ";
		if(!$conn->createCommand($query)->execute()) return -1;
	}
	
	$query = "
		SELECT		COUNT(*)
		FROM		{{metadataext}}
		WHERE		metadata_machinecode = :metadata_machinecode
		AND			column_name = :column_name;
	";
    $command = $conn->createCommand($query);
    $command->bindValue(":metadata_machinecode","metadata_".$data["placetype"]);
    $command->bindParam(":column_name",$data["column_name"]);
	$count = $command->queryScalar();
	
	if($count>0) return -2;
	
    $query = "
		INSERT INTO		{{metadataext}}
		(  	metadata_machinecode,
			column_name,
			display_name,
			metatype,
			inputtype	
		)
		VALUES
		(
			:metadata_machinecode,
			:column_name,
			:display_name,
			:metatype,
			:inputtype
		);
    ";
    $command = $conn->createCommand($query);
    $command->bindValue(":metadata_machinecode","metadata_".$data["placetype"]);
    $command->bindParam(":column_name",$data["column_name"]);
    $command->bindParam(":display_name",$data["display_name"]);
    $command->bindParam(":metatype",$data["metatype"]);
    $command->bindParam(":inputtype",$data["inputtype"]);
	if(!$command->execute()) return -3;
	
	return 1;
  }
  
  public function edit_infotype($data)
  {
	$conn = Yii::app()->db;
	
	$metad = new MetadataObj(0,"metadata_".$data["placetype"]);
	if(!$metad->is_column($data["column_name"]))
	{	
	  	if($data["inputtype"]=="text") $type = "VARCHAR(255)";
		else if($data["inputtype"]=="textarea") $type = "TEXT";
		
	    $query = "
	      ALTER TABLE {{metadata_".$data["placetype"]."}}
	      CHANGE	  ".$data["old_column_name"]." ".$data["column_name"]." $type;
	    ";
		if(!$conn->createCommand($query)->execute()) return -1;
	}
	
	$query = "
		SELECT		COUNT(*)
		FROM		{{metadataext}}
		WHERE		metadata_machinecode = :metadata_machinecode
		AND			column_name = :column_name;
	";
	$command = $conn->createCommand($query);
	$command->bindValue(":metadata_machinecode","metadata_".$data["placetype"]);
	$command->bindParam(":column_name",$data["old_column_name"]);
	$count = $command->queryScalar();
	
	if($count==0) return $this->add_infotype($data);
	
	$query = "
		UPDATE		{{metadataext}}
		SET			column_name = :column_name,
					display_name = :display_name,
					metatype = :metatype,
					inputtype = :inputtype
		WHERE		metadata_machinecode = :metadata_machinecode
		AND			column_name = :old_column_name
		;
	";
	$transaction = $conn->beginTransaction();
	try {
		$command = $conn->createCommand($query);
		$command->bindValue(":metadata_machinecode","metadata_".$data["placetype"]);
		$command->bindParam(":column_name",$data["column_name"]);
		$command->bindParam(":old_column_name",$data["old_column_name"]);
		$command->bindParam(":display_name",$data["display_name"]);
		$command->bindParam(":metatype",$data["metatype"]);
		$command->bindParam(":inputtype",$data["inputtype"]);
		$command->execute();
		$transaction->commit();
	} catch(Exception $e) {
		$transaction->rollBack();
		return $e;
	}
	
	return 1;
  }

	public function update_sorder($data)
	{
		$conn = Yii::app()->db;
	
		if(!isset($data["sorder"],$data["column_name"],$data["placetype"])) return -1;
		
		$query = "
			UPDATE		{{metadataext}}
			SET			sorder = :sorder
			WHERE		metadata_machinecode = :metadata_machinecode
			AND			column_name = :column_name
			;
		";
		$command = $conn->createCommand($query);
		$command->bindValue(":metadata_machinecode","metadata_".$data["placetype"]);
		$command->bindParam(":column_name",$data["column_name"]);
		$command->bindParam(":sorder",$data["sorder"]);
		if(!$command->execute()) return -2;
		
		return 1;
	}
	
	public function delete_infotype($data)
	{
		$conn = Yii::app()->db;
	
		if(!isset($data["column_name"],$data["placetype"])) return -1;
		
		$query = "
			DELETE FROM		{{metadataext}}
			WHERE			metadata_machinecode = :metadata_machinecode
			AND				column_name = :column_name;
		";
		$command = $conn->createCommand($query);
		$command->bindValue(":metadata_machinecode","metadata_".$data["placetype"]);
		$command->bindParam(":column_name",$data["column_name"]);
		if(!$command->execute()) return -2;
		
		return 1;
	}


}