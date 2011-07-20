<?php

  interface EascFeature {
    
    public function isMandatory();

    public function handle(EascFeature $feature);
  }
?>

