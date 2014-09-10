<?php

class PlacesObj extends FactoryObj
{
  
  protected $iterator = 0;
  
  public function __construct($placeid=null)
  {
    parent::__construct("placeid","places",$placeid);
  }
  
  public function pre_load()
  {
      if($this->is_valid_id() and !is_numeric($this->placeid)) {
          $this->placeid = Yii::app()->db->createCommand()
            ->select("placeid")
            ->from("places")
            ->where("placename=:placename",array(":placename"=>$this->placeid))
            ->queryScalar();
      }
  }
  
  public function post_load()
  {
    if(!$this->loaded) return;
    
    $this->placetype = new PlaceTypesObj($this->placetypeid);
    if(!$this->placetype->loaded) return;
    
  }
  
  public function load_metadata()
  {
  	$this->metadata = new MetadataObj($this->placeid,$this->placetype->metadata_machinecode);
  }
  
	public function pre_save()
	{
		// If placetype is by machinecode, find its placetypeid
		if(isset($this->placetype) and !isset($this->placetypeid))
		{
			$this->get_placetype_id($this->placetype->placetypeid);
		}
		// Initialize date created
		if(!isset($this->date_created) and !isset($this->placeid))
		{
			$this->date_created = date("Y-m-d H:i:s");
		}
		// Find the Sort Order by incrementing the highest sorder by one
		if(!$this->loaded and !isset($this->placeid) and !isset($sorder) and isset($this->placetypeid))
		{
			$conn = Yii::app()->db;
			$query = "
			SELECT          sorder
			FROM            {{places}}
			WHERE           placetypeid = :placetypeid
			ORDER BY        sorder DESC
			LIMIT           1;
			";
			$command = $conn->createCommand($query);
			$command->bindParam(":placetypeid",$this->placetypeid);
			$this->sorder = $command->queryScalar() + 1;
		}
	}
  
  public function post_save()
  {
	if($this->placeid > 0) {
		$this->load();
		if(isset($this->metadata) and is_object($this->metadata)) {
		  if(!$this->metadata->save()) {
              $this->set_error($this->metadata->get_error());
		  }
        }
	}
  }

  public function get_placetype_id($type)
  {
      $placetype = new PlaceTypesObj();
      $placetype->machinecode = $type;
      $placetype->load();
      if($placetype->loaded)
          $this->placetypeid = $placetype->placetypeid;
      else
          $this->placetypeid = 0;
  }
  
  public function run_check()
  {
      if($this->placename=="")
        return !$this->set_error("Place name cannot be empty.");
      if($this->placetypeid == 0)
        return !$this->set_error("Must be a type of place.");
      return true;
  }
  
  public function load_parent()
  {
  	$this->parent_ = new PlacesObj($this->parentid);
  }
  
  public function load_first_image()
  {
    if(!empty($this->images) and isset($this->images[0])) return $this->images[0];
    
    $this->images = array();
    
    $conn = Yii::app()->db;
    $query = "
      SELECT      pictureid
      FROM        {{placepictures}}
      WHERE       placeid = :placeid
      ORDER BY    coverphoto DESC, sorder ASC
      LIMIT       1;
    ";
    $command = $conn->createCommand($query);
    $command->bindParam(":placeid",$this->placeid);
    $pictureid = $command->queryScalar();
    
    if(!$pictureid)
    {
      $this->images[] = new PictureObj();
      $this->images[0]->load();
    }
    else
      $this->images[] = new PictureObj($pictureid);
    
    return $this->images[0];
  }
  
  public function load_images()
  {
    $this->images = array();
    $conn = Yii::app()->db;
    $query = "
      SELECT      pictureid
      FROM        {{placepictures}}
      WHERE       placeid = :placeid
      ORDER BY    coverphoto DESC, sorder ASC;
    ";
    $command = $conn->createCommand($query);
    $command->bindParam(":placeid",$this->placeid);
    $result = $command->queryAll();
    
    if(!$result or empty($result)) return $this->images;
    
    foreach($result as $row)
      $this->images[] = new PictureObj($row["pictureid"]);
      
    return $this->images;
  }
  
  public function load_image_location($iterator=null) {
      if(is_null($iterator)) {
          $iterator = $this->iterator;
      }
      if(isset($this->images[$iterator]) and $this->images[$iterator]->loaded) {
          return $this->images[$iterator]->path;
      }
      return "";
  }
  
  public function _metadata($index,$type)
  {
    if($type!="label" and $type!="value") return "";
    if(!isset($this->metadata) or !$this->metadata->loaded) return "";
    
    if($type=="label")
    {
      $displayname = $this->metadata->data[$index]["display_name"];
      $arr = get_object_vars($this);
      foreach($arr as $index=>$val)
      {
        if($this->is_column($index))
        {
          $displayname = str_replace("[".$index."]",$this->$index,$displayname);
        }
      }
    }
    
    return $displayname;
  }
  
  public function get_children($childtype=null)
  {
  	$this->children = array();
    $conn = Yii::app()->db;
    if(is_null($childtype)) {
        $query = "
          SELECT      placeid
          FROM        {{places}}
          WHERE       parentid = :parentid
          ORDER BY    sorder ASC;
        ";
        $command = $conn->createCommand($query);
    }
    else {
        $query = "
          SELECT      placeid
          FROM        {{places}}
          WHERE       parentid = :parentid
          AND         placetypeid = (SELECT placetypeid FROM placetypes WHERE machinecode = :machinecode LIMIT 1)
          ORDER BY    sorder ASC;
        ";
        $command = $conn->createCommand($query);
        $command->bindParam(":machinecode",$childtype);
    }
    $command->bindParam(":parentid",$this->placeid);
    $result = $command->queryAll();
	
	if(!$result or empty($result)) return array();
	foreach($result as $row)
		$this->children[] = new PlacesObj($row["placeid"]);

	return $this->children;
  }
  
  public function load_pictures()
  {
  	$this->pictures = array();
    $conn = Yii::app()->db;
    $query = "
      SELECT      pictureid
      FROM        {{placepictures}}
      WHERE       placeid = :placeid
      ORDER BY    sorder ASC;
    ";
    $command = $conn->createCommand($query);
    $command->bindParam(":placeid",$this->placeid);
    $result = $command->queryAll();
  	
	if(!$result or empty($result)) return array();
	foreach($result as $row)
		$this->pictures[] = new PictureObj($row["pictureid"]);
	
	return $this->pictures;
  }
  
  public function render_first_image($width="auto",$height="auto",$type="")
  {
  	if(!isset($this->pictures)) $this->load_pictures();
	if(empty($this->pictures)) {
	    $this->render_no_image($width,$height);
        return;
	}
    
	$curimg = $this->pictures[0];
	if($type=="thumb")
	{
		$path = $this->get_thumb_path($curimg);
	}
	else
	{
		$path = getcwd().$curimg->path;
	}
  	$imager = new Imager($path);
	$imager->resize($width,$height);
	return print $imager->render();
  }
  
  public function render_no_image($width="auto",$height="auto")
  {
    $path = LOCAL_IMAGE_LIBRARY."no_image_available.png";
    $imager = new Imager($path);
    $imager->resize($width,$height);
    $imager->add_attribute("alt", "No Image Available");
    return print $imager->render();
  }
  
  public function render_pictures($thumb=false)
  {
  	if(!isset($this->pictures)) $this->load_pictures();
	if(!empty($this->pictures)) {
		$count = 0;
		foreach($this->pictures as $picture) {
		    $count++;
			if($thumb) {
				$path = $this->get_thumb_path($picture);
			}
			else {
				$path = getcwd().$picture->path;
			}
			$imager = new Imager($path);
            $imager->add_attribute("alt", $this->placename." ".$count);
			$imager->add_attribute("class","slideshow-img");
			$imager->add_attribute("data-big",Yii::app()->baseUrl.$picture->path);
			$imager->add_attribute("data-title",$picture->caption);
			$imager->add_attribute("data-description",str_replace("'","",$picture->description));
			$imager->render();
		}
	} else {
		echo "No pictures to show.";
	}
	return;
  }
  
  public function get_thumb_path($curimg,$type="relpath")
  {
	$path = explode("/",$curimg->path);
	$path = array_filter($path);
	$file = array_pop($path);
	$imagedir = array_shift($path);
	$path_ = "";
	if(!empty($path)) $path_ = implode("/",$path);
	$thumbdir = getcwd()."/".$imagedir."/thumbs/".$path_;
	if(!is_dir($thumbdir)) mkdir($thumbdir,0700,true);
	
	if($type=="href")
		$thumbpath = Yii::app()->baseUrl."/".$imagedir."/thumbs/".$path_."/".$file;
	else
		$thumbpath = getcwd()."/".$imagedir."/thumbs/".$path_."/".$file;
	
	return $thumbpath;
  }
  
  public function has_pictures()
  {
	if(!empty($this->pictures)) return true;
	
    $conn = Yii::app()->db;
    $query = "
      SELECT      COUNT(*)
      FROM        {{placepictures}}
      WHERE       placeid = :placeid
      ORDER BY    sorder ASC;
    ";
    $command = $conn->createCommand($query);
    $command->bindParam(":placeid",$this->placeid);
    
    return ($command->queryScalar()>0);
  }
  
  public function get_parent()
  {
      if($this->parentid == 0) {
          return null;
      }
      return new PlacesObj($this->parentid);
  }
  
  public function has_parent()
  {
      return ($this->parentid != 0);
  }
  
  public function has_space($placetype) {
      return (Yii::app()->db->createCommand()
        ->select("COUNT(*)")
        ->from("places")
        ->where("placetypeid = (SELECT placetypeid FROM placetypes WHERE machinecode = :machinecode LIMIT 1) AND parentid = :parentid",
            array(":machinecode"=>$placetype,":parentid"=>$this->placeid)
        )
        ->queryScalar() !=0);
  }
  
  public function has_metadata_for($person) {
      return (Yii::app()->db->createCommand()
        ->select("COUNT(*)")
        ->from("metadataext")
        ->where("metatype = :metatype AND metadata_machinecode = :metadata_machinecode",
            array(":metatype"=>$person,":metadata_machinecode"=>"metadata_".$this->placetype->machinecode)
        )
        ->queryScalar() !=0);
  }
  
  public function has_location(){
      if($this->placetype->machinecode != "building") {
          return false;
      }
      return (Yii::app()->db->createCommand()
        ->select("COUNT(*)")
        ->from("metadata_building")
        ->where("latitude != :latitude AND longitude != :longitude AND placeid = :placeid", array(
            "longitude" => "0.000000",
            "latitude" => "0.000000",
            "placeid" => $this->placeid
        ))
        ->queryScalar() != 0
     );
  }
  
}