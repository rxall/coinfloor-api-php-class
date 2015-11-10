## Coinfloor API PHP Class

A barebones PHP class to interact with the Coinfloor API. All successful calls will return a json_decode() object of the response.
The full specification can be found at [https://github.com/coinfloor/API/blob/master/BIST.md](https://github.com/coinfloor/API/blob/master/BIST.md)

#### Getting Started
```sh 
require_once("Config.php");
require_once("ClassCoinfloorAPI.php");

$cf = new Coinfloor\cfRequest($coinfloor_config['user_id'], 
                              $coinfloor_config['api_key'],
                              $coinfloor_config['passphrase']);
```
A template of Config.php has been included in the repo.

#### Methods
[CURRENCY] is the shortname of any of the supported currencies.   

**Ticker**

```sh
//getTicker([CURRENCY]);
$cf->getTicker("GBP");
```
**Order Book**
```sh
//getOrderBook([CURRENCY]);
$cf->getOrderBook("GBP");
```
**Transactions**
```sh
//getTransactions([CURRENCY]);
$cf->getTransactions("GBP");
```
**Account Balance**
```sh
//getBalance([CURRENCY]);
$cf->getBalance("GBP");
```
**User Transactions**
```sh
//getUserTransactions([CURRENCY], [OFFSET] OPTIONAL, [LIMIT] OPTIONAL, [SORT] OPTIONAL);
$cf->getUserTransactions("GBP", null, 2, 'asc');
```
**Open Orders**
```sh
//getOpenOrders([CURRENCY]);
$cf->getOpenOrders("GBP");
```
**Cancel Order**
```sh
//cancelOrder([CURRENCY], [ORDER_ID]);
$cf->cancelOrder("GBP", 123456);
```
**Buy Limit Order**
```sh
//buyLimitOrder([CURRENCY], [AMOUNT], [PRICE], [TTL] OPTIONAL);
$cf->buyLimitOrder("GBP", 2, 200);
```
**Sell Limit Order**
```sh
//sellLimitOrder([CURRENCY], [AMOUNT], [PRICE], [TTL] OPTIONAL);
$cf->sellLimitOrder("GBP", 2, 200);
```
**Buy Market Order**   
Submit either a [QUANTITY] of XBT to purchase or a [TOTAL] values worth in the chosen currency.
```sh
//buyMarketOrder([CURRENCY], [QUANTITY] OPTIONAL, [TOTAL] OPTIONAL);
$cf->buyMarketOrder("GBP", 2);
```
**Sell Market Order**   
Submit either a [QUANTITY] of XBT to sell or a [TOTAL] values worth in the chosen currency.
```sh
//sellMarketOrder([CURRENCY], [QUANTITY] OPTIONAL, [TOTAL] OPTIONAL);
$cf->sellMarketOrder("GBP", null, 400);
```
**Estimate Buy Market Order**   
Submit either a [QUANTITY] of XBT to purchase or a [TOTAL] values worth in the chosen currency.
```sh
//estimateBuyMarketOrder([CURRENCY], [QUANTITY] OPTIONAL, [TOTAL] OPTIONAL);
$cf->estimateBuyMarketOrder("GBP", 2);
```
**Estimate Sell Market Order**   
Submit either a [QUANTITY] of XBT to sell or a [TOTAL] values worth in the chosen currency.
```sh
//estimateSellMarketOrder([CURRENCY], [QUANTITY] OPTIONAL, [TOTAL] OPTIONAL);
$cf->estimateSellMarketOrder("GBP", null, 400);
```

#### Example Usage
```sh 
#!/usr/local/bin/php
<?php

require_once("Config.php");
require_once("ClassCoinfloorAPI.php");

$cf = new Coinfloor\cfRequest($coinfloor_config['user_id'], 
                              $coinfloor_config['api_key'],
                              $coinfloor_config['passphrase']);

try { 
    print $cf->getBalance("GBP")->xbt_balance;
} catch(Coinfloor\CoinfloorAPIFailureException $e) {
    print $e->getMessage();
} catch(Coinfloor\CoinfloorAPIInvalidJSONException $e) {
    print $e->getMessage();
} catch(\Exception $e){
    print get_class( $e ) . ': ' . $e->getMessage();
}
```