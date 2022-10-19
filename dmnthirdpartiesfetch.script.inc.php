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
    along with Dash Ninja.  If not, see <http://www.gnu.org/licenses/>.

 */

DEFINE('DMN_VERSION','2.4.0');

xecho('dmnthirdpartiesfetch v'.DMN_VERSION."\n");

$tp = array();

xecho("Get last fetch time: ");

$content = dmn_api_get('/tablevars',[],$response);
$runcoinmarketcap = false;
if (strlen($content) > 0) {
  $content = json_decode($content,true);
  if (($response['http_code'] >= 200) && ($response['http_code'] <= 299)) {
    $delta = time() - intval($content['data']['tablevars']['marketcapbtc']['LastUpdate']);
    $runcoinmarketcap = $delta >= COINMARKETCAP_RUN_DELTA;
    echo "Success... $delta seconds\n";

  }
  elseif (($response['http_code'] >= 400) && ($response['http_code'] <= 499)) {
    echo "Error (".$response['http_code'].": ".$content['message'].")\n";
  }
}
else {
  echo "Error (empty result) [HTTP CODE ".$response['http_code']."]\n";
}

xecho("Fetching from Kraken: EUR/BTC... ");
$btceur = 1.0;
$btcusd = 1.0;
try {
  $kraken = new \Payward\KrakenAPI('','');
  $dataKraken = $kraken->QueryPublic('Ticker', array('pair' => 'XXBTZEUR'));
  if (is_array($dataKraken) && isset($dataKraken['error']) && (count($dataKraken['error']) == 0)
    && isset($dataKraken['result']) && is_array($dataKraken['result'])
    && isset($dataKraken['result']['XXBTZEUR']) && is_array($dataKraken['result']['XXBTZEUR'])
    && isset($dataKraken['result']['XXBTZEUR']['p']) && is_array($dataKraken['result']['XXBTZEUR']['p'])
    && isset($dataKraken['result']['XXBTZEUR']['p'][1]) ) {
    $btceur = floatval($dataKraken['result']['XXBTZEUR']['p'][1]);
    $tp["eurobtc"] = array("StatValue" => $dataKraken['result']['XXBTZEUR']['p'][1],
                           "LastUpdate" => time(),
                           "Source" => "kraken");
    echo "OK (".$dataKraken['result']['XXBTZEUR']['p'][1]." EUR/BTC)";
  }
  else {
    echo "ERROR";
  }
  echo " USD/BTC... ";
  $dataKraken = $kraken->QueryPublic('Ticker', array('pair' => 'XXBTZUSD'));
  if (is_array($dataKraken) && isset($dataKraken['error']) && (count($dataKraken['error']) == 0)
    && isset($dataKraken['result']) && is_array($dataKraken['result'])
    && isset($dataKraken['result']['XXBTZUSD']) && is_array($dataKraken['result']['XXBTZUSD'])
    && isset($dataKraken['result']['XXBTZUSD']['p']) && is_array($dataKraken['result']['XXBTZUSD']['p'])
    && isset($dataKraken['result']['XXBTZUSD']['p'][1]) ) {
    $btcusd = floatval($dataKraken['result']['XXBTZUSD']['p'][1]);
    $tp["usdbtc"] = array("StatValue" => $dataKraken['result']['XXBTZUSD']['p'][1],
      "LastUpdate" => time(),
      "Source" => "kraken");
    echo "OK (".$dataKraken['result']['XXBTZUSD']['p'][1]." USD/BTC)\n";
  }
}
catch (Exception $e) {
  // Error
}

/*
xecho("Fetching from Cryptsy: ");
$res = file_get_contents('http://pubapi2.cryptsy.com/api.php?method=singlemarketdata&marketid=155');
if ($res !== false) {
  $res = json_decode($res,true);
//  var_dump($res);
  if (($res !== false) && is_array($res) && (count($res) == 2) && array_key_exists('return',$res)
   && is_array($res["return"]) && array_key_exists("markets",$res["return"])
   && is_array($res["return"]["markets"]) && array_key_exists("DRK",$res["return"]["markets"])
   && is_array($res["return"]["markets"]["DRK"]) && array_key_exists("lasttradeprice",$res["return"]["markets"]["DRK"])) {
    $tp["btcdrk"] = array("StatValue" => $res["return"]["markets"]["DRK"]["lasttradeprice"],
                          "LastUpdate" => time(),
                          "Source" => "cryptsy");
    echo "OK (".$res["return"]["markets"]["DRK"]["lasttradeprice"]." BTC/DASH)\n";
  }
  else {
    echo "Failed (JSON)\n";
  }
}
else {
  echo "Failed (GET)\n";
}
*/

xecho("Fetching from Poloniex: ");
$res = file_get_contents('https://poloniex.com/public?command=returnTicker');
$btcdash = 1;
if ($res !== false) {
  $res = json_decode($res,true);
//  var_dump($res);
  if (($res !== false) && is_array($res) && (count($res) > 0) && array_key_exists('BTC_DASH',$res)
      && is_array($res["BTC_DASH"]) && array_key_exists("last",$res["BTC_DASH"])) {
    $btcdash = floatval($res["BTC_DASH"]["last"]);
    $tp["btcdrk"] = array("StatValue" => $res["BTC_DASH"]["last"],
        "LastUpdate" => time(),
        "Source" => "poloniex");
    echo "OK (".$res["BTC_DASH"]["last"]." BTC/DASH)\n";
  }
  else {
    echo "Failed (JSON)\n";
  }
}
else {
  echo "Failed (GET)\n";
}

/*
xecho("Fetching from Bitstamp: ");
$res = file_get_contents('https://www.bitstamp.net/api/ticker/');
if ($res !== false) {
  $res = json_decode($res,true);
  if (($res !== false) && is_array($res) && array_key_exists('timestamp',$res) && array_key_exists('last',$res)) {
    $tbstamp = date('Y-m-d H:i:s',$res['timestamp']);
    $sql[] = sprintf("('usdbtc','".$mysqli->real_escape_string($res['last'])."','".$tbstamp."','bitstamp')");
    $tp["usdbtc"] = array("StatValue" => $res["last"],
                          "LastUpdate" => intval($res['timestamp']),
                          "Source" => "bitstamp");
    echo "OK (".$res['last']." / $tbstamp)\n";
  }
  else {
    echo "Failed (JSON)\n";
  }
}
else {
  echo "Failed (GET)\n";
}
*/

/*
xecho("Fetching from BTC-e: ");
$res = file_get_contents('https://btc-e.com/api/2/btc_usd/ticker');
if ($res !== false) {
  $res = json_decode($res,true);
  if (($res !== false) && is_array($res) && array_key_exists('ticker',$res) && array_key_exists('last',$res['ticker']) && array_key_exists('updated',$res['ticker'])) {
    $tbstamp = date('Y-m-d H:i:s',$res['ticker']['updated']);
    $tp["usdbtc"] = array("StatValue" => $res['ticker']["last"],
                          "LastUpdate" => intval($res['ticker']['updated']),
                          "Source" => "btc-e");
    echo "OK (".$res['ticker']['last']." / $tbstamp)\n";
  }
  else {
    echo "Failed (JSON)\n";
  }
}
else {
  echo "Failed (GET)\n";
}
*/

/*xecho("Fetching from Bitfinex: ");
$res = file_get_contents('https://api.bitfinex.com/v1/pubticker/btcusd');
if ($res !== false) {
  $res = json_decode($res,true);
  if (($res !== false) && is_array($res) && array_key_exists('last_price',$res) && array_key_exists('timestamp',$res)) {
    $tbstamp = date('Y-m-d H:i:s',$res['timestamp']);
    $tp["usdbtc"] = array("StatValue" => $res["last_price"],
        "LastUpdate" => intval($res['timestamp']),
        "Source" => "bitfinex");
    echo "OK (".$res['last_price']." / $tbstamp)\n";
  }
  else {
    echo "Failed (JSON)\n";
  }
}
else {
  echo "Failed (GET)\n";
}*/

/*xecho("Fetching from Paxos: ");
$context = stream_context_create(
  array(
    "http" => array(
      "user_agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:106.0) Gecko/20100101 Firefox/106.0",
    )
  )
);
$res = file_get_contents('https://api.sandbox.paxos.com/v2/markets/BTCUSD/ticker',false,$context);
if ($res !== false) {
  $res = json_decode($res,true);
  if (($res !== false) && is_array($res) && array_key_exists('market',$res) && ($res["market"] === "BTCUSD") && array_key_exists('today',$res) && array_key_exists('snapshot_at',$res)) {
    $timestamp = strtotime($res['snapshot_at']);
    $tbstamp = date('Y-m-d H:i:s',$timestamp);
    $tp["usdbtc"] = array("StatValue" => floatval($res["today"]["volume_weighted_average_price"]),
        "LastUpdate" => intval($timestamp),
        "Source" => "paxos");
    echo "OK (".$res["today"]["volume_weighted_average_price"]." / $tbstamp)\n";
  }
  else {
    echo "Failed (JSON)\n";
  }
}
else {
  echo "Failed (GET)\n";
}*/

// https://bittrex.com/api/v1.1/public/getticker?market=BTC-DASH

xecho("Fetching from CoinMarketCap: ");

if ($runcoinmarketcap) {
// * https://pro-api.coinmarketcap.com/v2/cryptocurrency/quotes/latest?symbol=DASH
  $url = 'https://pro-api.coinmarketcap.com/v2/cryptocurrency/quotes/latest';
//$url = 'https://sandbox-api.coinmarketcap.com/v1/cryptocurrency/listings/latest';

  $parameters = [
    'slug' => 'dash',
    'convert' => 'BTC'
  ];

  $headers = [
    'Accepts: application/json',
    'X-CMC_PRO_API_KEY: '.COINMARKETCAP_API_KEY
  ];
  $qs = http_build_query($parameters); // query string encode the parameters
  $request = "{$url}?{$qs}"; // create the request URL

  $curl = curl_init(); // Get cURL resource
// Set cURL options
  curl_setopt_array($curl, array(
    CURLOPT_URL => $request,            // set the request URL
    CURLOPT_HTTPHEADER => $headers,     // set the headers
    CURLOPT_RETURNTRANSFER => 1         // ask for raw response instead of bool
  ));

  $res = curl_exec($curl); // Send the request, save the response
  curl_close($curl); // Close request
  $resdone = 0;
  if ($res !== false) {
    $res = json_decode($res, true);
    if (($res !== false) && is_array($res)
      && array_key_exists('data', $res) && is_array($res['data']) && (count($res['data']) === 1)
      && array_key_exists(131, $res['data'])
      && array_key_exists('cmc_rank', $res['data'][131]) && array_key_exists('total_supply', $res['data'][131])
      && array_key_exists('quote', $res['data'][131]) && is_array($res['data'][131]['quote']) && (count($res['data'][131]['quote']) === 1)
      && array_key_exists('status', $res) && is_array($res['status']) && array_key_exists('timestamp', $res['status'])) {
      $tbstamp = strtotime($res['status']['timestamp']);
      $res = $res['data'][131];
      if (array_key_exists('cmc_rank', $res)) {
        $tp["marketcappos"] = array("StatValue" => $res["cmc_rank"],
          "LastUpdate" => intval($tbstamp),
          "Source" => "coinmarketcap");
        $resdone++;
      } else {
        echo "Failed (JSON/cmc_rank) ";
      }
      if (array_key_exists('total_supply', $res)) {
        $tp["marketcapsupply"] = array("StatValue" => $res["total_supply"],
          "LastUpdate" => intval($tbstamp),
          "Source" => "coinmarketcap");
        $resdone++;
      } else {
        echo "Failed (JSON/total_supply) ";
      }
      if (array_key_exists('BTC', $res['quote']) && is_array($res['quote']['BTC'])) {
        if (array_key_exists('market_cap', $res['quote']['BTC'])) {
          $tp["marketcapbtc"] = array("StatValue" => $res['quote']['BTC']['market_cap'],
            "LastUpdate" => intval($tbstamp),
            "Source" => "coinmarketcap");
          $resdone++;
          $tp["marketcapusd"] = array("StatValue" => $res['quote']['BTC']['market_cap']*$btcusd,
            "LastUpdate" => intval($tbstamp),
            "Source" => "coinmarketcap");
          $resdone++;
          $tp["marketcapeur"] = array("StatValue" => $res['quote']['BTC']['market_cap']*$btceur,
            "LastUpdate" => intval($tbstamp),
            "Source" => "coinmarketcap");
          $resdone++;
        } else {
          echo "Failed (JSON/BTC/market_cap) ";
        }
        if (array_key_exists('volume_24h', $res['quote']['BTC'])) {
          $tp["volumebtc"] = array("StatValue" => $res['quote']['BTC']['volume_24h'],
            "LastUpdate" => intval($tbstamp),
            "Source" => "coinmarketcap");
          $resdone++;
          $tp["volumeusd"] = array("StatValue" => $res['quote']['BTC']['volume_24h']*$btcusd,
            "LastUpdate" => intval($tbstamp),
            "Source" => "coinmarketcap");
          $resdone++;
          $tp["volumeeur"] = array("StatValue" => $res['quote']['BTC']['volume_24h']*$btceur,
            "LastUpdate" => intval($tbstamp),
            "Source" => "coinmarketcap");
          $resdone++;
          $tp["marketcapchange"] = array("StatValue" => $res["quote"]['BTC']['volume_change_24h'],
            "LastUpdate" => intval($tbstamp),
            "Source" => "coinmarketcap");
          $resdone++;
        } else {
          echo "Failed (JSON/BTC/volume_24h) ";
        }
      }
      if ($resdone > 0) {
        if ($resdone == 9) {
          echo "OK";
        } else {
          echo "Partial";
        }
        echo " ($resdone values retrieved)\n";
      } else {
        echo "NOK\n";
      }
    } else {
      echo "Failed (JSON)\n";
    }
  } else {
    echo "Failed (GET)\n";
  }
}
else {
  echo "Skipping (Time elapsed since last fetch not long enough)\n";
}
$dw = array();

xecho("Fetching budgets list from DashCentral: ");
$res = file_get_contents('https://www.dashcentral.org/api/v1/budget?partner='.DMN_DASHWHALE_PARTNERID);
$proposals = array();
if ($res !== false) {
  $res = json_decode($res,true);
  if (($res !== false) && is_array($res) && array_key_exists('status',$res) && ($res['status'] == 'ok') && array_key_exists('proposals',$res) && is_array($res["proposals"]) ) {
    foreach($res["proposals"] as $proposal) {
      if ($proposal !== false && is_array($proposal) && array_key_exists('hash',$proposal) && is_string($proposal["hash"])) {
        if (preg_match("/^[0-9a-f]{64}$/s", $proposal["hash"]) === 1) {
          $proposals[] = $proposal["hash"];
        }
      }
    }
    echo "OK (".count($proposals)." budgets)\n";
  }
  else {
    echo "Failed (JSON)\n";
  }
}
else {
  echo "Failed (GET)\n";
}

foreach($proposals as $proposal) {
  xecho("Fetching budget $proposal from DashCentral: ");
  $res = file_get_contents('https://www.dashcentral.org/api/v1/proposal?partner='.DMN_DASHWHALE_PARTNERID.'&hash='.$proposal);
  $dwentry = array("proposal" => array(),
                   "comments" => array());
  if ($res !== false) {
    $res = json_decode($res,true);
    if (($res !== false) && is_array($res) && array_key_exists('status',$res) && ($res['status'] == 'ok')
                                           && array_key_exists('proposal',$res) && is_array($res["proposal"])
                                           && array_key_exists('comments',$res) && is_array($res["comments"])) {
      $dwentry["proposal"] = $res["proposal"];
      foreach($res["comments"] as $comment) {
        if ($comment !== false && is_array($comment) && array_key_exists('id',$comment) && is_string($comment["id"])
          && array_key_exists('username',$comment) && is_string($comment["username"])
          && array_key_exists('date',$comment) && is_string($comment["date"])
          && array_key_exists('order',$comment) && is_int($comment["order"])
          && array_key_exists('level',$comment)
          && array_key_exists('recently_posted',$comment) && is_bool($comment["recently_posted"])
          && array_key_exists('posted_by_owner',$comment) && is_bool($comment["posted_by_owner"])
          && array_key_exists('reply_url',$comment) && is_string($comment["reply_url"])
          && array_key_exists('content',$comment) && is_string($comment["content"])
           ) {
          if (preg_match("/^[0-9a-f]{32}$/s", $comment["id"]) === 1) {
            if (!filter_var($comment["reply_url"], FILTER_VALIDATE_URL) === false) {
              $dwentry["comments"][] = $comment;
              echo ".";
            }
            else {
              echo "u";
            }
          }
          else {
            echo "i";
          }
        }
        else {
          echo "e";
        }
      }
      $dw[] = $dwentry;
      echo " OK (".count($dwentry["comments"])." comments)\n";
    }
    else {
      echo "Failed (JSON)\n";
    }
  }
  else {
    echo "Failed (GET)\n";
  }
}

xecho("Submitting to web service: ");
$payload = array("thirdparties" => $tp,
                 "dashwhale" => $dw);
$content = dmn_cmd_post('/thirdparties',$payload,$response);
//var_dump($content);
if (strlen($content) > 0) {
  $content = json_decode($content,true);
  if (($response['http_code'] >= 200) && ($response['http_code'] <= 299)) {
    echo "Success (".$content['data']['thirdparties'].")\n";
  }
  elseif (($response['http_code'] >= 400) && ($response['http_code'] <= 499)) {
    echo "Error (".$response['http_code'].": ".$content['message'].")\n";
  }
}
else {
  echo "Error (empty result) [HTTP CODE ".$response['http_code']."]\n";
}

?>
