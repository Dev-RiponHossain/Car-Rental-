<?php
include('includes/config.php');
if(isset($_POST["division"])){
  $division = $_POST["division"];
  $sql = "SELECT name FROM districts WHERE division_id = (SELECT id FROM divisions WHERE name = :division)";
  $query = $dbh->prepare($sql);
  $query->bindParam(':division', $division, PDO::PARAM_STR);
  $query->execute();
  $districts = $query->fetchAll(PDO::FETCH_OBJ);
  echo '<option value="">Select District</option>';
  foreach($districts as $dist){
    echo "<option value=\"{$dist->name}\">{$dist->name}</option>";
  }
}
?>
