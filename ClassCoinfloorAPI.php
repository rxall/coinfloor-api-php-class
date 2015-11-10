<?php
namespace Coinfloor;

class cfRequest
{
    const base_uri = "https://webapi.coinfloor.co.uk:8090/bist/XBT/";
    
    private $user_id;
    private $api_key;
    private $passphrase;
    
    public function __construct($user_id, $api_key, $passphrase)
    {
        $this->user_id = $user_id;
        $this->api_key = $api_key;
        $this->passphrase = $passphrase;
    }
    
    public function callAPI($currency, $endpoint, $request_method = "GET", $post_data = null)
    {
        $authentication_header = $this->user_id. "/" . 
                                 $this->api_key . ":" . 
                                 $this->passphrase;                     
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::base_uri . $currency . "/" . $endpoint . "/");
        curl_setopt($ch, CURLOPT_USERPWD, $authentication_header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);       
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);       
        curl_setopt($ch, CURLOPT_CAINFO, CACERT_LOCATION);
        
        if ($request_method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);

            if ($post_data !== null) {
                $post_data_built = http_build_query($post_data, '', '&');
                $headers[] = "Content-Length: " . strlen($post_data_built);   
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data_built);
            } else {
                $headers[] = "Content-Length: 0";    
                $headers[] = "Expect: ";       
            } 
        }      
        
        $headers[] = "Accept: application/json";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        
        if(curl_errno($ch)){
            throw new CoinfloorAPIFailureException(curl_error($ch));
        }
        
        curl_close($ch);
        $decoded = json_decode($response);
        
        if ($decoded === null){
            throw new CoinfloorAPIInvalidJSONException("JSON Decode failed: $response");
        }
        
        return $decoded;
    }
 
    /////////////////////////////////////
    //        Public Endpoints         //
    /////////////////////////////////////
 
    public function getTicker($currency)
    {
        return $this->callAPI($currency, "ticker");
    }

    public function getOrderBook($currency)
    {
        return $this->callAPI($currency, "order_book");
    }
    
    public function getTransactions($currency)
    {
        return $this->callAPI($currency, "transactions");
    }
    
    /////////////////////////////////////
    //     Authenticated Endpoints     //
    /////////////////////////////////////    
    
    public function getBalance($currency)
    {
        return $this->callAPI($currency, "balance", "POST");
    }
    
    public function getUserTransactions($currency, $offset = null, $limit = null, $sort = null)
    {    
        if($offset !== null) { $post_array["offset"] = $offset; }
        if($limit !== null) { $post_array["limit"] = $limit; }
        if($sort !== null) { $post_array["sort"] = $sort; }
        
        if ($post_array === null) {
            return $this->callAPI($currency, "user_transactions", "POST");        
        } else {
            return $this->callAPI($currency, "user_transactions", "POST", $post_array);
        }
     }
    
    public function getOpenOrders($currency)
    {
        return $this->callAPI($currency, "open_orders", "POST");
    }
    
    public function cancelOrder($currency, $order_id)
    {        
        $post_array["id"] = $order_id;
        return $this->callAPI($currency, "cancel_order", "POST", $post_array);
    }
    
    public function buyLimitOrder($currency, $amount, $price, $ttl = null)
    {
        if ($ttl !== null) { $post_array["ttl"] = $ttl; }
        
        $post_array["amount"] = $amount;
        $post_array["price"] = $price;
        return $this->callAPI($currency, "buy", "POST", $post_array);        
    }
    
    public function sellLimitOrder($currency, $amount, $price, $ttl = null)
    {
        if ($ttl !== null) { $post_array["ttl"] = $ttl; }
        
        $post_array["amount"] = $amount;
        $post_array["price"] = $price;
        return $this->callAPI($currency, "sell", "POST", $post_array);
    }
    
    public function buyMarketOrder($currency, $quantity = null, $total = null)
    { 
        if ($quantity !== null) { $post_array["quantity"] = $quantity; }
        if ($total !== null) { $post_array["total"] = $total; }
        
        return $this->callAPI($currency, "buy_market", "POST", $post_array);
    }
    
    public function sellMarketOrder($currency, $quantity = null, $total = null)
    {
        if ($quantity !== null) { $post_array["quantity"] = $quantity; }
        if ($total !== null) { $post_array["total"] = $total; }
        
        return $this->callAPI($currency, "sell_market", "POST", $post_array);         
    }
    
    public function estimateBuyMarketOrder($currency, $quantity = null, $total = null)
    {
        if ($quantity !== null) { $post_array["quantity"] = $quantity; }
        if ($total !== null) { $post_array["total"] = $total; }
        
        return $this->callAPI($currency, "estimate_buy_market", "POST", $post_array);  
    }

    public function estimateSellMarketOrder($currency, $quantity = null, $total = null)
    {
        if ($quantity !== null) { $post_array["quantity"] = $quantity; }
        if ($total !== null) { $post_array["total"] = $total; }
        
        return $this->callAPI($currency, "estimate_sell_market", "POST", $post_array);          
    }
}

class CoinfloorAPIException extends \Exception {}
class CoinfloorAPIFailureException extends CoinfloorAPIException {}
class CoinfloorAPIInvalidJSONException extends CoinfloorAPIException{}