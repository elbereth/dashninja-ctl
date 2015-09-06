<?php

/*
    This file is part of Dash Ninja.
    https://github.com/elbereth/dashninja-ctl

    Dash Ninja is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Dash Ninja is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Foobar.  If not, see <http://www.gnu.org/licenses/>.

 */

if (!defined('DMN_SCRIPT') || !defined('DMN_CONFIG') || (DMN_SCRIPT !== true) || (DMN_CONFIG !== true)) {
  die('Not executable');
}

define('DMN_VERSION','1.0.1');

xecho('dmnblockdegapper v'.DMN_VERSION."\n");

xecho('Retrieving nodes for this hub: ');
$result = dmn_cmd_get('/nodes',array(),$response);
if ($response['http_code'] == 200) {
  echo "Fetched...";
  $nodes = json_decode($result,true);
  if ($nodes === false) {
    echo " Failed to JSON decode!\n";
    die(200);
  }
  elseif (!is_array($nodes) || !array_key_exists('data',$nodes) || !is_array($nodes['data'])) {
    echo " Incorrect data!\n";
    die(202);
  }
  $nodes = $nodes['data'];
  echo " OK (".count($nodes)." entries)\n";
}
else {
  echo "Failed [".$response['http_code']."]\n";
  if ($response['http_code'] != 500) {
    $result = json_decode($result,true);
    if ($result !== false) {
      foreach($result['messages'] as $num => $msg) {
        xecho("Error #$num: $msg\n");
      }
    }
  }
  die(201);
}

xecho('Retrieving last month block: ');
$result = dmn_api_get('/blocks',array("interval"=>"P1M"),$response);
if ($response['http_code'] == 200) {
  echo "Fetched...";
  $blocks = json_decode($result,true);
  if ($blocks=== false) {
    echo " Failed to JSON decode!\n";
    die(200);
  }
  elseif (!is_array($blocks) || !array_key_exists('data',$blocks) || !is_array($blocks['data']) || !array_key_exists('blocks',$blocks['data']) || !is_array($blocks['data']['blocks'])) {
    echo " Incorrect data!\n";
    die(202);
  }
  $blocks = $blocks['data']['blocks'];
  echo " OK (".count($blocks)." entries)\n";
}
else {
  echo "Failed [".$response['http_code']."]\n";
  if ($response['http_code'] != 500) {
    $result = json_decode($result,true);
    if ($result !== false) {
      foreach($result['messages'] as $num => $msg) {
        xecho("Error #$num: $msg\n");
      }
    }
  }
  die(201);
}

xecho("Finding gaps:\n");

$prevblock = -1;
$gaps = array();
foreach($blocks as $blockindex => $block) {
  if ($prevblock == -1) {
  }
  elseif (($prevblock-1) != $block['BlockId']) {
    if (($prevblock - $block['BlockId']) > 2) {
      xecho("Gap found, missing blocks ".($block['BlockId']+1)." to ".($prevblock-1)."\n");
      $gaps[] = ($block['BlockId']+1)." ".($prevblock-1);
    }
    else {
      xecho("Gap found, missing block ".($prevblock-1)."\n");
      $gaps[] = ($prevblock-1);
    }

  }
  $prevblock = $block['BlockId'];
}

if (count($gaps) == 0) {
  xecho('No gaps found! (Yeah \o/)'."\n");
}
else {
  xecho("De-gapping (".count($gaps)." gaps):\n");
  foreach($gaps as $id => $gap) {
    xecho(sprintf("#%'.03d",$id+1)." ($gap): ");
    $output = array();
    $result = 0;
    $lastline = exec(DMN_DIR."dashblockretrieve p2pool $gap",$output,$result);
    if ($result == 0) {
      echo "OK";
    }
    else {
      echo "Error ($lastline)";
    }
    echo "\n";
  }
}

?>
