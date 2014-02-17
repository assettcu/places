<?php

class PlaceTypesObj extends FactoryObj
{
  
  public function __construct($placetypeid=null)
  {
    parent::__construct("placetypeid","placetypes",$placetypeid);
  }
  
  public function pre_load()
  {
      if(isset($this->machinecode) and !isset($this->placetypeid))
      {
          $conn = Yii::app()->db;
          $query = "
            SELECT          placetypeid
            FROM            {{placetypes}}
            WHERE           machinecode = :machinecode
            LIMIT           1;
          ";
          $command = $conn->createCommand($query);
          $command->bindParam(":machinecode",$this->machinecode);
          $this->placetypeid = $command->queryScalar();
      }
  }
  
}