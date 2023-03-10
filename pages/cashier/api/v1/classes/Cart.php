<?php
/**
 * Created by PhpStorm.
 * User: Eric
 * Date: 12/21/2017
 * Time: 6:22 AM
 */
/**
 * Class to handle all db operations
 * This class will have CRUD methods for database tables
 *
 * @author Kogi Eric
 *
 */

/**
 * Change the queries in this methods to correspond with your sql queries
 * this is also the class you should add any other crud method for all the api calls
 *
 *
 * NOTE YOU NEED TO INTRODUCE YOUR CONNECTION TO DB HERE BY INCLUDING THE CONNECT.PHP FILE
 *
 *
 * IE private $conn
 *
 * @author Kogi Eric
 */


/**
 * Class DbHandler
 */

class CartDbHandler
{


    /**
     * @var PDO
     */
    /**
     * DbHandler constructor.
     */


    /**
     * @return mixed
     */

    /**
     * @var PDO
     */
    private $conn;

    /**
     * DbHandler constructor.
     */
    function __construct()
    {
       // require_once dirname(__FILE__) . '../configs/DbConnect.php';
        require_once dirname(__FILE__) . '/../configs/DbConnect.php';
       // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }

   

    //$_POST["amount"],$_POST["phone"],$_POST["orderId"],$_POST["firebaseToken"]
    public function pay($amount,$phone,$orderId,$firebasetoken){
        $stk_request_url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
    $outh_url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';


    $safaricom_pass_key = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";
    $safaricom_party_b = "174379";
    $safaricom_bussiness_short_code = "174379";

    //$safaricom_Auth_key = "hAVnRxa2UOjyAnydVJMG31A0OuDDCxm5";
    $safaricom_Auth_key = "a76Iw1pLxVjk9GkiIqAWaNLalSC16rM3";
    
    //$safaricom_Secret = "UcpmdCdI8bAakdgm";

    $safaricom_Secret = "H6G0EKL8h4hNEl2h";


    $outh = $safaricom_Auth_key . ':' . $safaricom_Secret;


    $curl_outh = curl_init($outh_url);
    curl_setopt($curl_outh, CURLOPT_RETURNTRANSFER, 1);

    $credentials = base64_encode($outh);
    curl_setopt($curl_outh, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . $credentials));
    curl_setopt($curl_outh, CURLOPT_HEADER, false);
    curl_setopt($curl_outh, CURLOPT_SSL_VERIFYPEER, false);

    $curl_outh_response = curl_exec($curl_outh);

    $json = json_decode($curl_outh_response, true);


    $time = date("YmdHis", time());

    $password = $safaricom_bussiness_short_code . $safaricom_pass_key . $time;


    $curl_stk = curl_init();
    curl_setopt($curl_stk, CURLOPT_URL, $stk_request_url);
    curl_setopt($curl_stk, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:Bearer ' . $json['access_token'])); //setting custom header
    $curl_post_data = array(

        'BusinessShortCode' => '174379',
        'Password' => base64_encode($password),
        'Timestamp' => $time,
        'TransactionType' => 'CustomerPayBillOnline',
        'Amount' => $amount,
        'PartyA' => $phone,
        'PartyB' => '174379',
        'PhoneNumber' => $phone,
        'CallBackURL' => 'https://denkiri.000webhostapp.com/sales/pages/cashier/api/v1/payment/callback.php?orderId='. urlencode($orderId).'&ftoken='.urlencode($firebasetoken),
        'AccountReference' => '4352',
        'TransactionDesc' => $phone
    );


    $data_string = json_encode($curl_post_data);

    curl_setopt($curl_stk, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl_stk, CURLOPT_POST, true);
    curl_setopt($curl_stk, CURLOPT_HEADER, false);
    curl_setopt($curl_stk, CURLOPT_POSTFIELDS, $data_string);

    $curl_stk_response = curl_exec($curl_stk);

// <script>
// alert(""$curl_stk_response);

// </script>
    $testjason = json_decode($curl_stk_response);

    if($testjason->ResponseCode == 0){
        return "Request made successfuly";
    }else{
        return "Something went wrong, please try again";
    }


   // return $curl_stk_response;
      //  }

    }
    public function insertIntoOrders($invoice,$cashier,$date,$ptype,$amount,$cname){
       // echo $data["totalPrice"];
       //error_log(json_encode($data["defaultBilling"]));
       $date = date('F d, Y');
       $dmonth = date('F');
       $dyear = date('Y');    
       $paymentStatus=0;
   
        $stmt = $this->conn->prepare("INSERT INTO mpesa_payments

        (invoice_number,cashier,date,type,amount,name,month,year,paymentStatus)
           
        VALUES(?,?,?,?,?,?,?,?,?)");

        $stmt->bind_param("ssssssssi",$invoice,$cashier,$date,$ptype,$amount,$cname,$dmonth,$dyear,$paymentStatus);

        $stmt->execute();
       // $id = $this->conn->lastInsertId();
       // mysqli_insert_id($con)

        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();

       


       
       
        return $this->getOrder($invoice);
      

    }
    
    public function updateOrderNotPaid($orderId,$details){
        $stmt = $this->conn->prepare("UPDATE  mpesa_payments SET paymentStatus= 2,payment_details= ?  WHERE invoice_number = ?  ");

        $stmt->bind_param("ss",$details,$orderId);

        $stmt->execute();

        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();

       
        if($num_rows>0){
            return false;
        }else{
            return true;

        }
    }

    public function updateOrderPaid($orderId,$details,$mpesacost,$mpesareciept,$mpesadate,$mpesanumber){
        $stmt = $this->conn->prepare("UPDATE mpesa_payments SET paymentStatus = 1, payment_details= ?,mpesa_amount= ?,MpesaReceiptNumber= ?,TransactionDate= ?,PhoneNumber= ?  WHERE invoice_number = ?  ");

        $stmt->bind_param("ssssss",$details,$mpesacost,$mpesareciept,$mpesadate,$mpesanumber,$orderId);

        $stmt->execute();

        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();

       
        if($num_rows>0){
            return false;
        }else{
            return true;

        }
    }

   
    public function  getOrder($invoice){
        $stmt = $this->conn->prepare("SELECT * FROM mpesa_payments WHERE invoice_number = ?  ");
        $stmt->bind_param("s", $invoice);
       
        $stmt->execute();
        
        
        $result = $stmt->get_result();
        $num_rows = $result->num_rows;
        
        $stmt->close();

       
        
        if($num_rows>0){
            $data=$result->fetch_assoc();
           $data["invoice_number"]= json_decode($data["invoice_number"], true);
           $data["cashier"]= json_decode($data["cashier"], true);
           $data["date"]= json_decode($data["date"], true);
           $data["type"]= json_decode($data["type"], true);
           $data["amount"]= json_decode($data["amount"], true);
           $data["name"]= json_decode($data["name"], true);
           $data["month"]= json_decode($data["month"], true);
           $data["year"]= json_decode($data["year"], true);
           $data["paymentStatus"]= $this->getPaymentStatus($data["paymentStatus"]);
             return $data;
        }else{
            return null;

        }
    }
   
    private function getPaymentStatus($paymentStatus){
        if($paymentStatus==1){
            return "Full Payment Recieved by Sokoni";
        }
        if($paymentStatus==0){
            return " Payment Pending";
        }
        if($paymentStatus==2){
            return " Payment Failed";
        }
        
        return "Null";
    }

    public function objectToArray($d) {
        if (is_object($d)) {
            $d = get_object_vars($d);
        }
        if (is_array($d)) {
            return array_map(__FUNCTION__, $d);
        } else {
            return $d;
        }
    }


 

  








  




    
 




 





  
}











?>
