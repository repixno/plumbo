<?PHP

// error_reporting(E_ALL); ini_set('display_errors', 1);
chdir(dirname(__FILE__));
include '../../../bootstrap.php';

config('website.config');
config('production.ecwid');
import('system.cli');
// import( 'website.ecwidorder' );
model('production.ecwid');
import('website.order.manual.ecwid');


//$url = 'https://app.ecwid.com/api/v3/20597088/orders?&offset=0&token=secret_MSXhCPqyFZjwSG3UesUeNFCetLA5ypAm';
// api url som viser ordrer som har status awaiting prosess. Mediaclip tek tak i ordrer som får status "prosessing"
//	$upateorderurl= 'https://app.ecwid.com/api/v3/20597088/orders?&vendorOrderNumber=$ecwidorder&fulfillmentStatus=PROCESSING&offset=0&token=secret_MSXhCPqyFZjwSG3UesUeNFCetLA5ypAm';

$url = 'https://app.ecwid.com/api/v3/20597088/orders?&fulfillmentStatus=AWAITING_PROCESSING&token=secret_4XjKdG5Fmq8snS1edtxpc8VAD1rs7DPt';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$data = curl_exec($ch);
curl_close($ch);

Util::debug( $data );
//echo '<h1> JSON RESPONSE </h1>';
//echo $data;
//$json_obj = preg_replace('\n', " ", utf8_encode($data));
$json_obj = json_decode($data, true);

//echo '<br> <br> <br> <br>';
//echo '<h1> PHP Array (json_decode)</h1>';
$ecwidorder = $item['vendorOrderNumber'];

$productkobling = Settings::Get('ecwid', 'article');

foreach ($json_obj['items'] as $item) {
    // and fulfillmentStatus=AWAITING_PROCESSING
    // update status to 

    $query = DB::query("SELECT id FROM ecwid WHERE ecwidid = ? ", $ecwidorder)->fetchSingle();
    print_r($item);

    $data = array();
    if (!$query > 0) {


        echo 'Customer number: ' . $item['customerId'] . " \n ";
        echo 'subtotal: ' . $item['subtotal'] . " \n ";
        echo 'Total orders: ' . $item['total'] . " \n ";
        echo 'VendorOrderNumber: ' . $item['vendorOrderNumber'] . " \n ";
        echo 'SKU: ' . $item['items'] [0] ['sku'] . " \n ";
        echo 'orderComments: ' . $item['orderComments'] . " \n ";
        echo 'Produkt: ' . $item['items'] [0]['name'] . " \n ";
        echo 'Antall: ' . $item ['items'] [0] ['quantity'] . " \n ";
        echo 'Jobid: ' . $item['items'] [0]['selectedOptions'][0] ['value'] . " \n ";
        echo 'email: ' . $item['email'] . " \n ";
        echo 'createDate: ' . $item['createDate'] . " \n ";
        echo 'name: ' . $item['billingPerson'] ['name'] . " \n ";
        echo 'street: ' . $item['billingPerson'] ['street'] . " \n ";
        echo 'postalCode: ' . $item['billingPerson'] ['postalCode'] . " \n ";
        echo 'postalCode: ' . $item['billingPerson'] ['city'] . " \n ";
        echo 'mobil: ' . $item['billingPerson'] ['phone'] . " \n ";
        echo 'shippingOption: ' . $item['shippingOption'] ['shippingMethodName'] . " \n ";
        echo 'paymentMethod: ' . $item['paymentMethod'] . " \n ";
    }

    $prodno = $productkobling [$item['items'][0]['sku']];


    $articles['prints'][] = array(
        'prodno' => $prodno,
					   'quantity' => $item['items'][0]['quantity'],
        // 'prodno' =>  $prodno,
        'file' => "nofile",
        'fitin' => "true"
    );
    $orderdata = new DBEcwid();
    $orderdata->ecwidid = $item['vendorOrderNumber'];
    $orderdata->downloaded = date('Y-m-d H:i:s');

				//$orderdata->projecttype = $key;
				//$orderdata->save();

    $articles['productionmethod'][] = 352;
    $articles['papertype'][] = 11;

    $data = array(
				//	'userid' =>  $item['customerId'],
        'userid' => '1395977',
        'fullname' => $item['billingPerson'] ['name'],
        'address' => $item['billingPerson'] ['street'],
        'email' => $item['email'],
        'zipcode' => $item['billingPerson'] ['postalCode'],
        'city' => $item['billingPerson'] ['city'],
        'mobile_phone_number' => $item['billingPerson'] ['phone'],
        'price' => $item['productPrice'],
								//	'article' => $item['items'] [0]['name'],
        'startprice' => $item['price'],
        'totalprice' => $item['subtotal'],
        'customerId' => $item['customerId'],
        'article' => $articles,
        'comment' => $item['orderComments']
    );

    //	die;

    //Util::Debug($productkobling);
    //Util::Debug($data);
    //echo $data ['customerId'];
    $ecwidcustomerid = $item['customerId'];
    $ecwidordernumber = $item['orderNumber'];

    //echo " \n" . "productkobling:" . $productkobling . "\n";
    //echo " \n" . "ecwidorder:" . $ecwidordernumber . "\n";



    $storeID = "20597088";
    $myToken = "secret_4XjKdG5Fmq8snS1edtxpc8VAD1rs7DPt";

    $senddata = array("fulfillmentStatus" => "PROCESSING", "orderNumber" => "$ecwidordernumber");

    $data_string = json_encode($senddata);
    $url = "https://app.ecwid.com/api/v3/" . urlencode($storeID) . "/orders/" . $data['orderNumber'] . "?token=" . $myToken;
    
    
      echo " \n" . "datastring:" . $senddata . "\n";
       echo " \n" . "url:" . $senddata . "\n";
      echo " \n" . "datastring:" . $data_string . "\n";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($data_string)));

    $response = curl_exec($ch);
    curl_close($ch);
die;
    $order = new ManualOrder();
    $orderid = $order->executeManualOrder($data);

    $orderdata->repixid = $orderid;

    echo " \n" . "repixid:" . $orderid . "\n";
    //		 Util::Debug( $data );

    //Util::Debug($orderdata);

    $orderdata->save();
    //Util::Debug($data);




    die;
}
?>