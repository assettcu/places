<?php

class PictureObj extends FactoryObj
{
  
  private $post_loading = false;
  
  public function __construct($pictureid=null)
  {
    parent::__construct("pictureid","placepictures",$pictureid);
  }
  
  public function pre_save()
  {
      if(!isset($this->pictureid) and !isset($this->date_added))
      {
          $this->date_added = date("Y-m-d H:i:s");
      }
	  if(!isset($this->sorder))
	      $this->sorder = $this->get_last_sorder()+1;
  }
  
  public function post_load()
  {
    if(!$this->loaded and !$this->post_loading)
    {
      $this->post_loading = true;
      $conn = Yii::app()->db;
      $query = "
        SELECT      pictureid
        FROM        {{".$this->table."}}
        WHERE       placeid = 0
        LIMIT       1;
      ";
      $this->pictureid = $conn->createCommand($query)->queryScalar();
      $this->load();
      
      return true;
    }
    
    return false;
  }
    
  public function get_last_sorder()
  {
      $conn = Yii::app()->db;
      $query = "
        SELECT      sorder
        FROM        {{".$this->table."}}
        WHERE       placeid = :placeid
        ORDER BY    sorder DESC
        LIMIT       1;
      ";
      $command = $conn->createCommand($query);
      $command->bindParam(":placeid",$this->placeid);
      return $command->queryScalar();
  }  
      
    public function render_boxfit_y($size)
    {
        $imager = new Imager(getcwd().$this->path);
        $imager->resize($size,"auto");
    
        return $imager->render(); 
    }
  
  public function render_boxfit($size)
  {
    $imager = new Imager(getcwd().$this->path);
    if($imager->width >= $imager->height)
        $imager->resize($size,"auto");
    else
    {
        $imager->resize("auto",$size);
    }
    
    return $imager->render(); 
  }
    
  public function render($width="auto",$height="auto",$type="resize")
  {
    $imager = new Imager(getcwd().$this->path);
    $imager->$type($width,$height);
    
    return $imager->render();
  }
  
  public function load_image_href()
  {
    $imager = new Imager(getcwd().$this->path);
    return $imager->imagehttp;
  }
 
  public function crop($size,$target)
  {
  	$imager = new Imager(getcwd().$this->path);
	$imager->crop($target,$size,"auto");
  }
  
  public function get_thumb()
  {
	$path = explode("/",$this->path);
	$path = array_filter($path);
	$file = array_pop($path);
	$imagedir = array_shift($path);
	$path_ = "";
	if(!empty($path)) $path_ = implode("/",$path);
	$thumbdir = getcwd()."/".$imagedir."/thumbs/".$path_;
	if(!is_dir($thumbdir)) mkdir($thumbdir,0700,true);
	$thumbpath = getcwd()."/".$imagedir."/thumbs/".$path_."/".$file;
	$href = Yii::app()->baseUrl."/".$imagedir."/thumbs/".$path_."/".$file;
	
	if(!is_file($thumbpath)){
		$this->crop("500",$thumbpath);
	}
	
	return $href;
  }
  
  public function get_thumb_path()
  {
	$path = explode("/",$this->path);
	$path = array_filter($path);
	$file = array_pop($path);
	$imagedir = array_shift($path);
	$path_ = "";
	if(!empty($path)) $path_ = implode("/",$path);
	$thumbdir = getcwd()."/".$imagedir."/thumbs/".$path_;
	if(!is_dir($thumbdir)) mkdir($thumbdir,0700,true);
	$thumbpath = getcwd()."/".$imagedir."/thumbs/".$path_."/".$file;
	
	return $thumbpath;
  }
  
  public function make_thumb($OVERWRITE=false)
  {
	$thumbpath = $this->get_thumb_path();
	if(!is_file($thumbpath) or $OVERWRITE){
		$this->crop("500",$thumbpath);
	}
  }
  
  public function has_file()
  {
      if($this->loaded and is_file(getcwd().$this->path)) {
          return true;
      }
      return false;
  }
  
  public function pre_delete()
  {
	if($this->has_file()) {
		@unlink(getcwd().$this->path);
	}
  }
  
}


?>