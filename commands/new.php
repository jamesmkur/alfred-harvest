<?php

  $query = trim($argv[1]);
  $dir = "../../../Workflow Data/com.neilrenicker.harvest/";

  if ( substr_count( $query, '→' ) == 0 ):

    if (file_exists($dir . 'projects.json')) {
      $data_raw = file_get_contents($dir . 'projects.json');
      $data = json_decode($data_raw, true);
    } else {
      require('get_daily.php');
      $data = json_decode($response, true);
    }

    $xml = "<?xml version=\"1.0\"?>\n<items>\n";

    foreach ($data["projects"] as $project){
      $name    = htmlspecialchars($project["name"]);
      $client  = htmlspecialchars($project["client"]);
      $id      = $project["id"];

      if ( !$query ) {
        $xml .= "<item valid=\"no\" uid=\"harvestnew-$id\" autocomplete=\"$id → \">\n";
        $xml .= "<title>$name, $client</title>\n";
        $xml .= "<subtitle>Project: $id</subtitle>\n";
        $xml .= "<icon>icons/add.png</icon>\n";
        $xml .= "</item>\n";
      } elseif ( stripos($name . $client, $query) !== false ) {
        $xml .= "<item valid=\"no\" uid=\"harvestnew-$id\" autocomplete=\"$id → \">\n";
        $xml .= "<title>$name, $client</title>\n";
        $xml .= "<subtitle>Project: $id</subtitle>\n";
        $xml .= "<icon>icons/add.png</icon>\n";
        $xml .= "</item>\n";
      }
    }

    $xml .= "</items>";

    echo $xml;

  elseif ( substr_count( $query, '→' ) == 1 ):

    $strings = explode( " →", $query);
    $project_id = $strings[0];
    $newQuery = $strings[1];

    if (file_exists($dir . 'projects.json')) {
      $data_raw = file_get_contents($dir . 'projects.json');
      $data = json_decode($data_raw, true);
    } else {
      require('get_daily.php');
      $data = json_decode($response, true);
    }

    foreach ( $data["projects"] as $project ){

      if ( $project["id"] == $project_id ) {
        $project_tasks = $project["tasks"];
        $project_name = htmlspecialchars($project["name"]);
        $project_client = htmlspecialchars($project["client"]);
        $project_name_encoded = str_replace(" ", "_", htmlspecialchars($project_name));
      }
    }

    $xml = "<?xml version=\"1.0\"?>\n<items>\n";

    foreach ($project_tasks as $task){
      $task_name = htmlspecialchars($task["name"]);
      $task_id = $task["id"];

      if ( !$newQuery ) {
        $xml .= "<item arg=\"$project_id|$task_id|$project_name_encoded\" uid=\"harvesttasks-$task_id\">\n";
        $xml .= "<title>$task_name</title>\n";
        $xml .= "<subtitle>$project_client, $project_name</subtitle>\n";
        $xml .= "<icon>icons/go.png</icon>\n";
        $xml .= "</item>\n";
      } elseif ( stripos(" " . $task_name, $newQuery) !== false ) {
        $xml .= "<item arg=\"$project_id|$task_id|$project_name_encoded\" uid=\"harvesttasks-$task_id\">\n";
        $xml .= "<title>$task_name</title>\n";
        $xml .= "<subtitle>$project_client, $project_name</subtitle>\n";
        $xml .= "<icon>icons/go.png</icon>\n";
        $xml .= "</item>\n";
      }
    }

    $xml .= "</items>";
    echo $xml;

  endif;

?>