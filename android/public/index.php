<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
// use Slim\Http\UploadedFile;

require '../vendor/autoload.php';

require '../includes/Functions.php';
// require '../includes/braintreepayments/vendor/autoload.php';
require '../vendor/braintree/braintree_php/lib/autoload.php';


$app = new \Slim\App([
    'settings'=>[
        'displayErrorDetails'=>true
    ]
]);

$gateway = new Braintree\Gateway([
  'environment' => 'sandbox',
  'merchantId' => 'nskdd9vk7ks6bhhf',
  'publicKey' => 'n86cvzm4fcsfpbrd',
  'privateKey' => '76527d518ab7c550d0f9915949fd5816'
]);

$container = $app->getContainer();
$container['upload_products'] = "../../images/products/";
$container['upload_profile'] = "../../img/profile_pictures/";
$container['gateway'] = $gateway;
// if(file_exists(__DIR__ . "/../.env")) {
//     $dotenv = new Dotenv\Dotenv(__DIR__ . "/../");
//     $dotenv->load();
// }

// Braintree_Configuration::environment('sandbox');
// Braintree_Configuration::merchantId('nskdd9vk7ks6bhhf');
// Braintree_Configuration::publicKey('n86cvzm4fcsfpbrd');

// Braintree_Configuration::privateKey('76527d518ab7c550d0f9915949fd5816');

//$app->add(new Tuupola\Middleware\HttpBasicAuthentication([
//    "secure"=>false,
//    "users" => [
//        "belalkhan" => "123456",
//    ]
//]));

//POST METHODS
//user login
$app->post('/userlogin', function(Request $request, Response $response){

    if(!haveEmptyParameters(array('phone', 'password'), $request, $response)){
        $request_data = $request->getParsedBody(); 

        $phone = $request_data['phone'];
        $password = $request_data['password'];

        $operation = new Functions();
        
        $query = "SELECT * FROM `users` WHERE phone = '$phone'";
        $count = $operation->countAll($query);
        if($count>0){
            //get the user
            $user = $operation->retrieveSingle($query);
            $hashed_password = $user['password'];

            if(password_verify($password, $hashed_password)){

                if($user['account_status'] == 1){

                    //get user but filter out some values
                    $user = $operation->retrieveSingle("SELECT *FROM `users` WHERE phone = '$phone'");
                    $user_id = $user['user_id'];

                    $response_data = array();
                    
                    $response_data['error']=false; 
                    $response_data['message'] = 'Login Successful';
                    $response_data['user'] = $user;

                          //retrieve all other data
                    $response_data['error']=false; 
                    $response_data['message'] = 'All data';

              //retrieve all other data
                    $getUsers = $operation->retrieveMany("SELECT *FROM users");
                    $getProducts = $operation->retrieveMany("SELECT *FROM products");
                    $getCategories = $operation->retrieveMany("SELECT *FROM categories");
                    $getBusiness = $operation->retrieveMany("SELECT *FROM business_info");
                    $getUnits = $operation->retrieveMany("SELECT *FROM units");

                    $response_data['users'] = $getUsers;
                    $response_data['products'] = $getProducts;
                    $response_data['categories'] = $getCategories;
                    $response_data['business_info'] = $getBusiness;
                    $response_data['units'] = $getUnits;


                    $response->write(json_encode($response_data));

                    return $response
                    ->withHeader('Content-type', 'application/json')
                    ->withStatus(200); 
                }else{
                    $response_data = array();

                    $response_data['error']=true; 
                    $response_data['message'] = 'Account Suspended';

                    $response->write(json_encode($response_data));

                    return $response
                    ->withHeader('Content-type', 'application/json')
                    ->withStatus(201); 
                }
            }else{
                $response_data = array();

                $response_data['error']=true; 
                $response_data['message'] = 'Invalid credential';

                $response->write(json_encode($response_data));

                return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(201);  
            }
            
        }else{
         $response_data = array();

         $response_data['error']=true; 
         $response_data['message'] = 'User does not exist';

         $response->write(json_encode($response_data));

         return $response
         ->withHeader('Content-type', 'application/json')
         ->withStatus(201);  
     }        
 }

 return $response
 ->withHeader('Content-type', 'application/json')
 ->withStatus(422);    
});

//add user
$app->post('/add_user', function(Request $request, Response $response){

    if(!haveEmptyParameters(array('fullname','phone','phone', 'password','user_role'), $request, $response)){
        $request_data = $request->getParsedBody(); 

        $fullname = $request_data['fullname'];
        $phone = $request_data['phone'];
        $user_role = $request_data['user_role'];
        $phone = $request_data['phone'];
        $pass = $request_data['password'];
        //encyrpt password
        $password=password_hash($pass, PASSWORD_DEFAULT);

        $operation = new Functions();
        
        $query = "SELECT * FROM `users` WHERE phone = '$phone' ";
        $count = $operation->countAll($query);
        if($count==0){

            $table = "users";
            $data = [
                'fullname'=>"$fullname",
                'phone'=>"$phone",
                'phone'=>"$phone",
                'password'=>"$password",
                'user_role'=>"$user_role"
            ];

            if ($operation->insertData($table,$data) == 1) {
                # code...
               $response_data = array();

               $response_data['error']=false; 
               $response_data['message'] = 'Account created, please login to continue!';
                         //retrieve all other data
               $getUsers = $operation->retrieveMany("SELECT *FROM users");
               $getSuppliers = $operation->retrieveMany("SELECT *FROM supplier");
               $getProducts = $operation->retrieveMany("SELECT *FROM products");
               $getCategories = $operation->retrieveMany("SELECT *FROM categories");
               $getCheckout = $operation->retrieveMany("SELECT *FROM checkout");
               $getSales = $operation->retrieveMany("SELECT *FROM sales");

               $response_data['users'] = $getUsers;
               $response_data['suppliers'] = $getSuppliers;
               $response_data['products'] = $getProducts;
               $response_data['categories'] = $getCategories;
               $response_data['checkouts'] = $getCheckout;
               $response_data['sales'] = $getSales;


               $response->write(json_encode($response_data));

               return $response
               ->withHeader('Content-type', 'application/json')
               ->withStatus(200); 
           }else{
               $response_data = array();

               $response_data['error']=true; 
               $response_data['message'] = 'An error occured while registering, try again later!';

               $response->write(json_encode($response_data));

               return $response
               ->withHeader('Content-type', 'application/json')
               ->withStatus(201); 
           }




       }else{
         $response_data = array();

         $response_data['error']=true; 
         $response_data['message'] = 'phone is taken, try another phone';

         $response->write(json_encode($response_data));

         return $response
         ->withHeader('Content-type', 'application/json')
         ->withStatus(201);  
     }        
 }

 return $response
 ->withHeader('Content-type', 'application/json')
 ->withStatus(422);    
});


//add business info
$app->post('/add_business_info', function(Request $request, Response $response){

    if(!haveEmptyParameters(array('user_id','business_name','business_phone','business_address','longtude','latitude'), $request, $response)){
        $request_data = $request->getParsedBody(); 
        $response_data = array();

        $user_id = $request_data['user_id'];
        $business_name = $request_data['business_name'];
        $business_phone = $request_data['business_phone'];
        $business_address = $request_data['business_address'];
        

        if ($request_data['longtude'] != 'longtude') {
         $longtude = $request_data['longtude'];
     }else{
        $longtude ='';
    }

    if ($request_data['latitude'] != 'latitude') {
        $latitude = $request_data['latitude'];
    }else{
        $latitude = '';
    }



    $operation = new Functions();

    $query = "SELECT * FROM `business_info` WHERE user_id = '$user_id'";

    $count = $operation->countAll($query);
    if($count==0){

        $table = "business_info";
        $data = [
            'user_id'=>"$user_id",
            'business_name'=>"$business_name",
            'business_phone'=>"$business_phone",
            'business_address'=>"$business_address",
            'longtude'=>"$longtude",
            'latitude'=>"$latitude",
        ];



        if ($operation->insertData($table,$data) == 1) {
                    // code...



            $response_data['error']=false; 
            $response_data['message'] = 'Business info has been added';



              //retrieve all other data
            $getUsers = $operation->retrieveMany("SELECT *FROM users");
            $getProducts = $operation->retrieveMany("SELECT *FROM products");
            $getCategories = $operation->retrieveMany("SELECT *FROM categories");
            $getBusiness = $operation->retrieveMany("SELECT *FROM business_info");
            $getUnits = $operation->retrieveMany("SELECT *FROM units");

            $response_data['users'] = $getUsers;
            $response_data['products'] = $getProducts;
            $response_data['categories'] = $getCategories;
            $response_data['business_info'] = $getBusiness;
            $response_data['units'] = $getUnits;

            $response->write(json_encode($response_data));

            return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(200); 
        }else{

            $response_data = array();

            $response_data['error']=true; 
            $response_data['message'] = 'An error occured, try again later!';

            $response->write(json_encode($response_data));

            return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(201); 

        }



    }else{
     $response_data = array();

     $response_data['error']=true; 
     $response_data['message'] = 'It seems you already added business info';

     $response->write(json_encode($response_data));

     return $response
     ->withHeader('Content-type', 'application/json')
     ->withStatus(201);  
 }        
}

return $response
->withHeader('Content-type', 'application/json')
->withStatus(422);    
});


//add category
$app->post('/add_category', function(Request $request, Response $response){
    if(!haveEmptyParameters(array('category_name','notes'), $request, $response)){

        $request_data = $request->getParsedBody(); 

        $category_name = $request_data['category_name'];
        $notes = $request_data['notes'];

        $operation = new Functions();
        
        $query = "SELECT * FROM `categories` WHERE category_name = '$category_name'";

        $count = $operation->countAll($query);
        if($count==0){

            $table = "categories";
            $data = [
                'category_name'=>"$category_name",
                'category_note'=>"$notes",
            ];

            if ($operation->insertData($table,$data) == 1) {

                $response_data = array();

                $response_data['error']=false; 
                $response_data['message'] = 'Category has been added';

                      //retrieve all other data
                $getUsers = $operation->retrieveMany("SELECT *FROM users");
                $getSuppliers = $operation->retrieveMany("SELECT *FROM supplier");
                $getProducts = $operation->retrieveMany("SELECT *FROM products");
                $getCategories = $operation->retrieveMany("SELECT *FROM categories");
                $getCheckout = $operation->retrieveMany("SELECT *FROM checkout");
                $getSales = $operation->retrieveMany("SELECT *FROM sales");

                $response_data['users'] = $getUsers;
                $response_data['suppliers'] = $getSuppliers;
                $response_data['products'] = $getProducts;
                $response_data['categories'] = $getCategories;
                $response_data['checkouts'] = $getCheckout;
                $response_data['sales'] = $getSales;


                $response->write(json_encode($response_data));

                return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200); 
            }else{
               $response_data = array();

               $response_data['error']=true; 
               $response_data['message'] = 'An error occured while adding category, try again later!';

               $response->write(json_encode($response_data));

               return $response
               ->withHeader('Content-type', 'application/json')
               ->withStatus(201); 
           }

       }else{
         $response_data = array();

         $response_data['error']=true; 
         $response_data['message'] = 'Category name is already in use, try a different name!';

         $response->write(json_encode($response_data));

         return $response
         ->withHeader('Content-type', 'application/json')
         ->withStatus(201);  
     }        
 }

 return $response
 ->withHeader('Content-type', 'application/json')
 ->withStatus(422);    
});


//add product
$app->post('/add_product', function(Request $request, Response $response) {

    if(!haveEmptyParameters(array('user_id','category_id','unit_id','product_name','product_price','product_quantity','product_threshold','product_description'), $request, $response)){
        $request_data = $request->getParsedBody();

        $directory = $this->get('upload_products');

        $uploadedFiles = $request->getUploadedFiles();
        $operation = new Functions();

        $user_id = $request_data['user_id'];
        $category_id = $request_data['category_id'];
        $unit_id = $request_data['unit_id'];
        $product_name = $request_data['product_name'];
        $product_price = $request_data['product_price'];
        $product_quantity = $request_data['product_quantity'];
        $product_threshold = $request_data['product_threshold'];
        $product_description = $request_data['product_description'];

        //check if same product code already exists or not
        $checkProduct = $operation->countAll("SELECT *FROM products WHERE product_name = '$product_name' AND user_id = '$user_id'");
        if ($checkProduct == 0) {
            if (is_null($uploadedFiles['file'])) {
            // code...
                $table = "products";
                $data = [
                    'user_id'=>"$user_id",
                    'category_id'=>"$category_id",
                    'unit_id'=>"$unit_id",
                    'product_name'=>"$product_name",
                    'price'=>"$product_price",
                    'qty'=>"$product_quantity",
                    'threshold'=>"$product_threshold",
                    'description'=>"$product_description"
                ];
                if ($operation->insertData($table,$data) == 1) {
                     // code...
                    $response_data = array();

                    $response_data['error']=false; 
                    $response_data['message'] = 'Product has been added!';


                          //retrieve all other data
                    $response_data['error']=false; 
                    $response_data['message'] = 'All data';

              //retrieve all other data
                    $getUsers = $operation->retrieveMany("SELECT *FROM users");
                    $getProducts = $operation->retrieveMany("SELECT *FROM products");
                    $getCategories = $operation->retrieveMany("SELECT *FROM categories");
                    $getBusiness = $operation->retrieveMany("SELECT *FROM business_info");
                    $getUnits = $operation->retrieveMany("SELECT *FROM units");

                    $response_data['users'] = $getUsers;
                    $response_data['products'] = $getProducts;
                    $response_data['categories'] = $getCategories;
                    $response_data['business_info'] = $getBusiness;
                    $response_data['units'] = $getUnits;


                    $response->write(json_encode($response_data));

                    return $response
                    ->withHeader('Content-type', 'application/json')
                    ->withStatus(200);
                }else{
                    $response_data = array();

                    $response_data['error']=true; 
                    $response_data['message'] = 'An error occured while adding product, try again later!';

                    $response->write(json_encode($response_data));

                    return $response
                    ->withHeader('Content-type', 'application/json')
                    ->withStatus(201);
                }


            }else{
                // handle single input with single file upload
                $uploadedFile = $uploadedFiles['file'];
                if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                    $filename = moveUploadedFile($directory, $uploadedFile);

                    $table = "products";
                    $data = [
                        'user_id'=>"$user_id",
                        'category_id'=>"$category_id",
                        'unit_id'=>"$unit_id",
                        'product_name'=>"$product_name",
                        'price'=>"$product_price",
                        'qty'=>"$product_quantity",
                        'threshold'=>"$product_threshold",
                        'img_url'=>"$filename",
                        'description'=>"$product_description"
                    ];
                    if ($operation->insertData($table,$data) == 1) {
                         // code...
                        $response_data = array();

                        $response_data['error']=false; 
                        $response_data['message'] = 'Product has been added!';

                       //retrieve all other data
                        $getUsers = $operation->retrieveMany("SELECT *FROM users");
                        $getProducts = $operation->retrieveMany("SELECT *FROM products");
                        $getCategories = $operation->retrieveMany("SELECT *FROM categories");
                        $getBusiness = $operation->retrieveMany("SELECT *FROM business_info");
                        $getUnits = $operation->retrieveMany("SELECT *FROM units");

                        $response_data['users'] = $getUsers;
                        $response_data['products'] = $getProducts;
                        $response_data['categories'] = $getCategories;
                        $response_data['business_info'] = $getBusiness;
                        $response_data['units'] = $getUnits;


                        $response->write(json_encode($response_data));

                        return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(200);
                    }else{
                        $response_data = array();

                        $response_data['error']=true; 
                        $response_data['message'] = 'An error occured while adding product, try again later!';

                        $response->write(json_encode($response_data));

                        return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(201);
                    }


                }else{

                    $response_data = array();

                    $response_data['error']=true; 
                    $response_data['message'] = 'An error occured while adding product, try again later!';

                    $response->write(json_encode($response_data));

                    return $response
                    ->withHeader('Content-type', 'application/json')
                    ->withStatus(201); 
                }
            }
        }else{
           $response_data = array();

           $response_data['error']=true; 
           $response_data['message'] = 'You already added this product!';

           $response->write(json_encode($response_data));

           return $response
           ->withHeader('Content-type', 'application/json')
           ->withStatus(201); 
       }







        // handle multiple inputs with the same key
        // foreach ($uploadedFiles['example2'] as $uploadedFile) {
        //     if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
        //         $filename = moveUploadedFile($directory, $uploadedFile);
        //         $response->write('uploaded ' . $filename . '<br/>');
        //     }
        // }




   }


   return $response
   ->withHeader('Content-type', 'application/json')
   ->withStatus(422); 
});

//add sale 
$app->post('/add_sale', function(Request $request, Response $response){

    $array = ($request->getParsedBody()); 

    if(!haveEmptyArrayParameters(array('product_id','qty'), $request, $response) && !haveEmptyParameters(array('total','paid_amount','change','discount','tax'), $request, $response)){
        $request_data = $request->getParsedBody(); 

        $product = $request_data['product_id'];
        $qty = $request_data['qty'];
        $total = $request_data['total'];
        $paid = $request_data['paid_amount'];
        $change = $request_data['change'];
        $discount = $request_data['discount'];
        $tax = $request_data['tax'];
        $uniqid = uniqid().$total;

        $operation = new Functions();
        $response_data = array();

        $table = "checkout";
        $data = [
            'uniqid'=>"$uniqid",
            'total_before_tax_discount'=>"$total",
            'amount_paid'=>"$paid",
            'change_amount'=>"$change",
            'discount'=>"$discount",
            'tax'=>"$tax"
        ];

        if ($operation->insertData($table,$data) == 1) {

            //get the checkout_id 
            $getCheckout = $operation->retrieveSingle("SELECT * FROM `checkout` WHERE uniqid = '$uniqid' ORDER BY checkout_id DESC");
            $checkout_id = $getCheckout['checkout_id'];

            $table = "sales";
            for($i = 0; $i<count($product); $i++){
                $data = [
                    'product_id'=>"$product[$i]",
                    'checkout_id'=>"$checkout_id",
                    'qty'=>"$qty[$i]"
                ];
                //check if product exists
                if ($operation->countAll("SELECT * FROM `products` WHERE product_id = '$product[$i]'")) {

                    if ($operation->insertData($table,$data) == 1) {
                        $response_data['error']=false; 
                        $response_data['message'] = 'Transaction complete!';
                    }else{
                        $response_data['error']=true; 
                        $response_data['message'] = 'Transaction failed!';
                    }
                }else{
                   $response_data['error']=true; 
                   $response_data['message'] = 'Some items are missing and have not been checked out';
               }

           }
       }else{
        $response_data['error']=true; 
        $response_data['message'] = 'Processing transaction failed, try again later!';
    }

         /*
         echo uniqid().$total."<br/>";

         print_r($request_data);
         echo "<br/><br/><br/><br/>";
         print_r($product);
          echo "<br/><br/><br/><br/>";
         print_r($total);
         die();
         */





           //retrieve all other data
         $getUsers = $operation->retrieveMany("SELECT *FROM users");
         $getSuppliers = $operation->retrieveMany("SELECT *FROM supplier");
         $getProducts = $operation->retrieveMany("SELECT *FROM products");
         $getCategories = $operation->retrieveMany("SELECT *FROM categories");
         $getCheckout = $operation->retrieveMany("SELECT *FROM checkout");
         $getSales = $operation->retrieveMany("SELECT *FROM sales");

         $response_data['users'] = $getUsers;
         $response_data['suppliers'] = $getSuppliers;
         $response_data['products'] = $getProducts;
         $response_data['categories'] = $getCategories;
         $response_data['checkouts'] = $getCheckout;
         $response_data['sales'] = $getSales;



         $response->write(json_encode($response_data));

         return $response
         ->withHeader('Content-type', 'application/json')
         ->withStatus(200);


     }

     return $response
     ->withHeader('Content-type', 'application/json')
     ->withStatus(422);    
 });



//PUT METHODS
//update category
$app->put('/update_category', function(Request $request, Response $response){
    if(!haveEmptyParameters(array('category_id','category_name','notes'), $request, $response)){

        $request_data = $request->getParsedBody(); 

        $category_id = $request_data['category_id'];
        $category_name = $request_data['category_name'];
        $notes = $request_data['notes'];

        $operation = new Functions();
        
        $query = "SELECT * FROM `categories` WHERE category_id = '$category_id'";

        $count = $operation->countAll($query);
        if($count > 0){

            $table = "categories";
            $data = [
                'category_name'=>"$category_name",
                'category_note'=>"$notes",
            ];
            $where = "category_id = '$category_id'";


            if ($operation->updateData($table,$data, $where) == 1) {

                $response_data = array();

                $response_data['error']=false; 
                $response_data['message'] = 'Category has been updated';

                   //retrieve all other data
                $getUsers = $operation->retrieveMany("SELECT *FROM users");
                $getSuppliers = $operation->retrieveMany("SELECT *FROM supplier");
                $getProducts = $operation->retrieveMany("SELECT *FROM products");
                $getCategories = $operation->retrieveMany("SELECT *FROM categories");
                $getCheckout = $operation->retrieveMany("SELECT *FROM checkout");
                $getSales = $operation->retrieveMany("SELECT *FROM sales");

                $response_data['users'] = $getUsers;
                $response_data['suppliers'] = $getSuppliers;
                $response_data['products'] = $getProducts;
                $response_data['categories'] = $getCategories;
                $response_data['checkouts'] = $getCheckout;
                $response_data['sales'] = $getSales;


                $response->write(json_encode($response_data));

                return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200); 
            }else{
               $response_data = array();

               $response_data['error']=true; 
               $response_data['message'] = 'An error occured while updating category, try again later!';

               $response->write(json_encode($response_data));

               return $response
               ->withHeader('Content-type', 'application/json')
               ->withStatus(201); 
           }

       }else{
         $response_data = array();

         $response_data['error']=true; 
         $response_data['message'] = 'Category deleted or not accessible';

         $response->write(json_encode($response_data));

         return $response
         ->withHeader('Content-type', 'application/json')
         ->withStatus(201);  
     }        
 }

 return $response
 ->withHeader('Content-type', 'application/json')
 ->withStatus(422);    
});

//update supplier
$app->put('/update_supplier', function(Request $request, Response $response){

    if(!haveEmptyParameters(array('supplier_id','user_id','fullname','phone','phone','address','notes','is_default'), $request, $response)){
        $request_data = $request->getParsedBody(); 

        $fullname = $request_data['fullname'];
        $phone = $request_data['phone'];
        $phone = $request_data['phone'];
        $notes = $request_data['notes'];
        $address = $request_data['address'];
        $is_default = $request_data['is_default'];
        $supplier_id = $request_data['supplier_id'];
        $user_id = $request_data['user_id'];

        $operation = new Functions();
        
        $query = "SELECT * FROM `users` WHERE user_id = '$user_id'";

        $count = $operation->countAll($query);
        if($count>0){

            $table = "users";
            $data = [
                'fullname'=>"$fullname",
                'phone'=>"$phone",
                'phone'=>"$phone",
            ];
            $where = "user_id = '$user_id'";

            if ($operation->updateData($table,$data,$where) == 1) {
                $table ="supplier";
                if ($is_default == 1) {
                    $where = "is_default = '$is_default'";
                    $false = "0";
                    $data = [
                        'is_default'=>"$false"
                    ];
                    $operation->updateData($table,$data, $where);
                }

                $table = "supplier";
                $data = [
                    'address'=>"$address",
                    'notes' =>"$notes",
                    'is_default'=>"$is_default"
                ];

                $where = "supplier_id = '$supplier_id'";

                if ($operation->updateData($table,$data,$where) == 1) {
                    // code...

                    $response_data = array();

                    $response_data['error']=false; 
                    $response_data['message'] = 'Supplier has been updated';

                       //retrieve all other data

                    $getUsers = $operation->retrieveMany("SELECT *FROM users");
                    $getSuppliers = $operation->retrieveMany("SELECT *FROM supplier");
                    $getProducts = $operation->retrieveMany("SELECT *FROM products");
                    $getCategories = $operation->retrieveMany("SELECT *FROM categories");
                    $getCheckout = $operation->retrieveMany("SELECT *FROM checkout");
                    $getSales = $operation->retrieveMany("SELECT *FROM sales");

                    $response_data['users'] = $getUsers;
                    $response_data['suppliers'] = $getSuppliers;
                    $response_data['products'] = $getProducts;
                    $response_data['categories'] = $getCategories;
                    $response_data['checkouts'] = $getCheckout;
                    $response_data['sales'] = $getSales;

                    $response->write(json_encode($response_data));

                    return $response
                    ->withHeader('Content-type', 'application/json')
                    ->withStatus(200); 
                }else{

                    $response_data = array();

                    $response_data['error']=true; 
                    $response_data['message'] = 'An error occured while finalizing updating supplier, try again later!';

                    $response->write(json_encode($response_data));

                    return $response
                    ->withHeader('Content-type', 'application/json')
                    ->withStatus(201); 

                }

            }else{
               $response_data = array();

               $response_data['error']=true; 
               $response_data['message'] = 'An error occured while updating supplier, try again later!';

               $response->write(json_encode($response_data));

               return $response
               ->withHeader('Content-type', 'application/json')
               ->withStatus(201); 
           }




       }else{
         $response_data = array();

         $response_data['error']=true; 
         $response_data['message'] = 'The requested supplier not found!';

         $response->write(json_encode($response_data));

         return $response
         ->withHeader('Content-type', 'application/json')
         ->withStatus(201);  
     }        
 }

 return $response
 ->withHeader('Content-type', 'application/json')
 ->withStatus(422);    
});

//update product
$app->put('/update_product', function(Request $request, Response $response) {

    if(!haveEmptyParameters(array('product_id','category_id','unit_id','product_name','product_price','product_quantity','product_threshold','product_description'), $request, $response)){
        $request_data = $request->getParsedBody();

        $directory = $this->get('upload_products');

        $uploadedFiles = $request->getUploadedFiles();
        $operation = new Functions();

        $product_id = $request_data['product_id'];
        $category_id = $request_data['category_id'];
        $unit_id = $request_data['unit_id'];
        $product_name = $request_data['product_name'];
        $product_price = $request_data['product_price'];
        $product_quantity = $request_data['product_quantity'];
        $product_threshold = $request_data['product_threshold'];
        $product_description = $request_data['product_description'];
        $where ="product_id = '$product_id'";

        //check if same product code already exists or not
        $checkProduct = $operation->countAll("SELECT *FROM products WHERE product_id = '$product_id'");
        if ($checkProduct > 0) {

             //check if product code already exists or not for a different product
            $getProduct = $operation->retrieveSingle("SELECT *FROM products WHERE product_id = '$product_id'");
            $countProduct = $operation->retrieveSingle("SELECT *FROM products WHERE product_id = '$product_id'");

            if ($countProduct == 0) {

               if (is_null($uploadedFiles['file'])) {
                    // code...
                $table = "products";
                $data = [
                    'category_id'=>"$category_id",
                    'unit_id'=>"$unit_id",
                    'product_name'=>"$product_name",
                    'price'=>"$product_price",
                    'qty'=>"$product_quantity",
                    'threshold'=>"$product_threshold",
                    'description'=>"$product_description"
                ];
                if ($operation->updateData($table,$data, $where) == 1) {
                     // code...
                    $response_data = array();

                    $response_data['error']=false; 
                    $response_data['message'] = 'Product has been updated!';

                    //retrieve all other data
                    $getUsers = $operation->retrieveMany("SELECT *FROM users");
                    $getProducts = $operation->retrieveMany("SELECT *FROM products");
                    $getCategories = $operation->retrieveMany("SELECT *FROM categories");
                    $getBusiness = $operation->retrieveMany("SELECT *FROM business_info");
                    $getUnits = $operation->retrieveMany("SELECT *FROM units");

                    $response_data['users'] = $getUsers;
                    $response_data['products'] = $getProducts;
                    $response_data['categories'] = $getCategories;
                    $response_data['business_info'] = $getBusiness;
                    $response_data['units'] = $getUnits;

                    $response->write(json_encode($response_data));

                    return $response
                    ->withHeader('Content-type', 'application/json')
                    ->withStatus(200);
                }else{
                    $response_data = array();

                    $response_data['error']=true; 
                    $response_data['message'] = 'An error occured while updating product, try again later!';

                    $response->write(json_encode($response_data));

                    return $response
                    ->withHeader('Content-type', 'application/json')
                    ->withStatus(201);
                }


            }else{
                // handle single input with single file upload
                $uploadedFile = $uploadedFiles['file'];
                if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                    $filename = moveUploadedFile($directory, $uploadedFile);


                    $query = "SELECT * FROM `products` WHERE product_id = '$product_id'";
                    //get file and delete
                    $getFile = $operation->retrieveSingle($query);
                    $file = $getFile['product_img_url'];

                    //delele old file
                    if (unlink($directory.$file)) {}



                       $table = "products";
                   $data = [
                      'category_id'=>"$category_id",
                      'unit_id'=>"$unit_id",
                      'product_name'=>"$product_name",
                      'price'=>"$product_price",
                      'qty'=>"$product_quantity",
                      'threshold'=>"$product_threshold",
                      'img_url'=>"$filename",
                      'description'=>"$product_description"
                  ];
                  if ($operation->updateData($table,$data,$where) == 1) {
                         // code...
                    $response_data = array();

                    $response_data['error']=false; 
                    $response_data['message'] = 'Product has been updated!';

                     //retrieve all other data
                    $getUsers = $operation->retrieveMany("SELECT *FROM users");
                    $getProducts = $operation->retrieveMany("SELECT *FROM products");
                    $getCategories = $operation->retrieveMany("SELECT *FROM categories");
                    $getBusiness = $operation->retrieveMany("SELECT *FROM business_info");
                    $getUnits = $operation->retrieveMany("SELECT *FROM units");

                    $response_data['users'] = $getUsers;
                    $response_data['products'] = $getProducts;
                    $response_data['categories'] = $getCategories;
                    $response_data['business_info'] = $getBusiness;
                    $response_data['units'] = $getUnits;

                    $response->write(json_encode($response_data));

                    return $response
                    ->withHeader('Content-type', 'application/json')
                    ->withStatus(200);
                }else{
                    $response_data = array();

                    $response_data['error']=true; 
                    $response_data['message'] = 'An error occured while updating product, try again later!';

                    $response->write(json_encode($response_data));

                    return $response
                    ->withHeader('Content-type', 'application/json')
                    ->withStatus(201);
                }


            }else{

                $response_data = array();

                $response_data['error']=true; 
                $response_data['message'] = 'An error occured while updating product, try again later!';

                $response->write(json_encode($response_data));

                return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(201); 
            }
        }
    }else{

        if ($getProduct['product_id'] == $product_id) {
           if (is_null($uploadedFiles['file'])) {
            // code...
            $table = "products";
            $data = [
              'category_id'=>"$category_id",
              'unit_id'=>"$unit_id",
              'product_name'=>"$product_name",
              'price'=>"$product_price",
              'qty'=>"$product_quantity",
              'threshold'=>"$product_threshold",
              'description'=>"$product_description"
          ];
          if ($operation->updateData($table,$data, $where) == 1) {
                     // code...
            $response_data = array();

            $response_data['error']=false; 
            $response_data['message'] = 'Product has been updated!';

              //retrieve all other data
            $getUsers = $operation->retrieveMany("SELECT *FROM users");
            $getProducts = $operation->retrieveMany("SELECT *FROM products");
            $getCategories = $operation->retrieveMany("SELECT *FROM categories");
            $getBusiness = $operation->retrieveMany("SELECT *FROM business_info");
            $getUnits = $operation->retrieveMany("SELECT *FROM units");

            $response_data['users'] = $getUsers;
            $response_data['products'] = $getProducts;
            $response_data['categories'] = $getCategories;
            $response_data['business_info'] = $getBusiness;
            $response_data['units'] = $getUnits;

            $response->write(json_encode($response_data));

            return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(200);
        }else{
            $response_data = array();

            $response_data['error']=true; 
            $response_data['message'] = 'An error occured while updating product, try again later!';

            $response->write(json_encode($response_data));

            return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(201);
        }
    }else{
                // handle single input with single file upload
        $uploadedFile = $uploadedFiles['file'];
        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            $filename = moveUploadedFile($directory, $uploadedFile);

            $query = "SELECT * FROM `products` WHERE product_id = '$product_id'";
                    //get file and delete
            $getFile = $operation->retrieveSingle($query);
            $file = $getFile['product_img_url'];

                    //delele old file
            if (unlink($directory.$file)) {}

               $table = "products";
           $data = [
              'category_id'=>"$category_id",
              'unit_id'=>"$unit_id",
              'product_name'=>"$product_name",
              'price'=>"$product_price",
              'qty'=>"$product_quantity",
              'threshold'=>"$product_threshold",
              'img_url'=>"$filename",
              'description'=>"$product_description"
          ];
          if ($operation->updateData($table,$data,$where) == 1) {
                                 // code...
            $response_data = array();

            $response_data['error']=false; 
            $response_data['message'] = 'Product has been updated!';

                       //retrieve all other data
            $getUsers = $operation->retrieveMany("SELECT *FROM users");
            $getProducts = $operation->retrieveMany("SELECT *FROM products");
            $getCategories = $operation->retrieveMany("SELECT *FROM categories");
            $getBusiness = $operation->retrieveMany("SELECT *FROM business_info");
            $getUnits = $operation->retrieveMany("SELECT *FROM units");

            $response_data['users'] = $getUsers;
            $response_data['products'] = $getProducts;
            $response_data['categories'] = $getCategories;
            $response_data['business_info'] = $getBusiness;
            $response_data['units'] = $getUnits;
            $response->write(json_encode($response_data));

            return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(200);
        }else{
            $response_data = array();

            $response_data['error']=true; 
            $response_data['message'] = 'An error occured while updating product, try again later!';

            $response->write(json_encode($response_data));

            return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(201);
        }
    }else{
        $response_data = array();

        $response_data['error']=true; 
        $response_data['message'] = 'An error occured while updating product, try again later!';

        $response->write(json_encode($response_data));

        return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(201); 
    }
}
}else{

    $response_data = array();

    $response_data['error']=true; 
    $response_data['message'] = 'Product code is being used by a different product!';

    $response->write(json_encode($response_data));

    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(201);
}

}
}else{
   $response_data = array();

   $response_data['error']=true; 
   $response_data['message'] = 'Requested product not found or deleted!';

   $response->write(json_encode($response_data));

   return $response
   ->withHeader('Content-type', 'application/json')
   ->withStatus(201); 
}
        // handle multiple inputs with the same key
        // foreach ($uploadedFiles['example2'] as $uploadedFile) {
        //     if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
        //         $filename = moveUploadedFile($directory, $uploadedFile);
        //         $response->write('uploaded ' . $filename . '<br/>');
        //     }
        // }




}


return $response
->withHeader('Content-type', 'application/json')
->withStatus(422); 
});

//update product image
$app->post('/update_product_image', function(Request $request, Response $response) {

    if(!haveEmptyParameters(array('product_id'), $request, $response)){
        $request_data = $request->getParsedBody();

        $directory = $this->get('upload_products');

        $uploadedFiles = $request->getUploadedFiles();
        $operation = new Functions();
        $product_id = $request_data['product_id'];

        
        $uploadedFile = $uploadedFiles['file'];
        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            $filename = moveUploadedFile($directory, $uploadedFile);
                // $response->write('uploaded ' . $filename . '<br/>');

            $table = "products";

            $where = "product_id = '$product_id'";
            $query = "SELECT * FROM `products` WHERE product_id = '$product_id'";
            if ($operation->countAll($query) > 0) {
                $data = [
                    'product_img_url'=>"$filename" 
                ];
                    //get file and delete
                $getFile = $operation->retrieveSingle($query);
                $file = $getFile['product_img_url'];

                    //delele old file
                if (unlink($directory.$file)) {}

                    # code...
                    if ($operation->updateData($table,$data,$where) == 1) {
                        $getFile = $operation->retrieveSingle($query);
                        # code...
                        $response_data['error']=false; 
                        $response_data['message'] = 'File Uploaded';
                        //retrieve all other data
                        $getUsers = $operation->retrieveMany("SELECT *FROM users");
                        $getSuppliers = $operation->retrieveMany("SELECT *FROM supplier");
                        $getProducts = $operation->retrieveMany("SELECT *FROM products");
                        $getCategories = $operation->retrieveMany("SELECT *FROM categories");
                        $getCheckout = $operation->retrieveMany("SELECT *FROM checkout");
                        $getSales = $operation->retrieveMany("SELECT *FROM sales");

                        $response_data['users'] = $getUsers;
                        $response_data['suppliers'] = $getSuppliers;
                        $response_data['products'] = $getProducts;
                        $response_data['categories'] = $getCategories;
                        $response_data['checkouts'] = $getCheckout;
                        $response_data['sales'] = $getSales;

                        $response->write(json_encode($response_data));

                    }else{
                        $response_data['error']=true; 
                        $response_data['message'] = 'Not Uploaded, try again later!';
                        

                        $response->write(json_encode($response_data));

                    }
                }else{

                    $response_data['error']=true; 
                    $response_data['message'] = 'Requested product not found!';


                    $response->write(json_encode($response_data));
                }


                return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);  
            }else{
                $response_data['error']=true; 
                $response_data['message'] = 'Not Uploaded';
                $response->write(json_encode($response_data));

                return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200); 
            }



        }


        return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(422); 

    });

//update inventory 
$app->put('/update_inventory', function(Request $request, Response $response){

    $array = ($request->getParsedBody()); 
    // print_r($array['qty']);die();
    // haveEmptyArrayParameters('product_id[]', $request, $response);
    // die();  

    if(!haveEmptyArrayParameters(array('product_id','qty'), $request, $response)){
        $request_data = $request->getParsedBody(); 

        $product = $request_data['product_id'];
        $qty = $request_data['qty'];

        $operation = new Functions();
        $response_data = array();

        $table = "products";
        for($i = 0; $i<count($product); $i++){
            $data = [
                'product_quantity'=>"$qty[$i]"
            ];
            //check if product exists
            if ($operation->countAll("SELECT * FROM `products` WHERE product_id = '$product[$i]'")) {
                $where = "product_id = '$product[$i]'";
                if ($operation->updateData($table,$data,$where) == 1) {
                    $response_data['error']=false; 
                    $response_data['message'] = 'Inventory has been updated';
                }else{
                    $response_data['error']=true; 
                    $response_data['message'] = 'Inventory has not been updated';
                }
            }else{
               $response_data['error']=true; 
               $response_data['message'] = 'Some items are missing and have not been updated';
           }

       }


                //retrieve all other data
       $getUsers = $operation->retrieveMany("SELECT *FROM users");
       $getSuppliers = $operation->retrieveMany("SELECT *FROM supplier");
       $getProducts = $operation->retrieveMany("SELECT *FROM products");
       $getCategories = $operation->retrieveMany("SELECT *FROM categories");
       $getCheckout = $operation->retrieveMany("SELECT *FROM checkout");
       $getSales = $operation->retrieveMany("SELECT *FROM sales");

       $response_data['users'] = $getUsers;
       $response_data['suppliers'] = $getSuppliers;
       $response_data['products'] = $getProducts;
       $response_data['categories'] = $getCategories;
       $response_data['checkouts'] = $getCheckout;
       $response_data['sales'] = $getSales;

       $response->write(json_encode($response_data));

       return $response
       ->withHeader('Content-type', 'application/json')
       ->withStatus(200);


   }

   return $response
   ->withHeader('Content-type', 'application/json')
   ->withStatus(422);    
});


//update Business info
$app->put('/update_business_info', function(Request $request, Response $response){
    if(!haveEmptyParameters(array('user_id','business_id','business_name','business_phone','business_address','longtude','latitude'), $request, $response)){

        $request_data = $request->getParsedBody(); 
                $user_id = $request_data['user_id'];
                $business_id = $request_data['business_id'];
        $business_name = $request_data['business_name'];
        $business_phone = $request_data['business_phone'];
        $business_address = $request_data['business_address'];
        

        if ($request_data['longtude'] != 'longtude') {
            $longtude = $request_data['longtude'];
        }else{
            $longtude ='';
        }

        if ($request_data['latitude'] != 'latitude') {
            $latitude = $request_data['latitude'];
        }else{
            $latitude = '';
        }



        $operation = new Functions();

        $query = "SELECT * FROM `business_info` WHERE business_id = '$business_id'";

        $count = $operation->countAll($query);
        if($count > 0){

            $table = "business_info";
            $data = [
                'user_id'=>"$user_id",
                'business_name'=>"$business_name",
                'business_phone'=>"$business_phone",
                'business_address'=>"$business_address",
                'longtude'=>"$longtude",
                'latitude'=>"$latitude",
            ];

            $where = "business_id = '$business_id'";


            if ($operation->updateData($table,$data,$where) == 1) {
                        // code...



                $response_data['error']=false; 
                $response_data['message'] = 'Business info has been updated';



                  //retrieve all other data
                $getUsers = $operation->retrieveMany("SELECT *FROM users");
                $getProducts = $operation->retrieveMany("SELECT *FROM products");
                $getCategories = $operation->retrieveMany("SELECT *FROM categories");
                $getBusiness = $operation->retrieveMany("SELECT *FROM business_info");
                $getUnits = $operation->retrieveMany("SELECT *FROM units");

                $response_data['users'] = $getUsers;
                $response_data['products'] = $getProducts;
                $response_data['categories'] = $getCategories;
                $response_data['business_info'] = $getBusiness;
                $response_data['units'] = $getUnits;

                $response->write(json_encode($response_data));

                return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200); 
            }else{

                $response_data = array();

                $response_data['error']=true; 
                $response_data['message'] = 'An error occured, try again later!';

                $response->write(json_encode($response_data));

                return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(201); 

            }

        }else{
         $response_data = array();

         $response_data['error']=true; 
         $response_data['message'] = 'Requested resource not found!';

         $response->write(json_encode($response_data));

         return $response
         ->withHeader('Content-type', 'application/json')
         ->withStatus(201);  
        }  
           
    }


return $response
 ->withHeader('Content-type', 'application/json')
 ->withStatus(422);    
});


//GET METHODS

//get all data || gets data only when there is a user in database
$app->get('/all_data',function(Request $request, Response $response){


    $operation = new Functions();
    $query = "SELECT *FROM users ";
    
    if($operation->countAll($query) > 0 ){
        $my_orders = $operation->retrieveMany($query);
        $response_data = array();

        $response_data['error']=false; 
        $response_data['message'] = 'All data';

              //retrieve all other data
        $getUsers = $operation->retrieveMany("SELECT *FROM users");
        $getProducts = $operation->retrieveMany("SELECT *FROM products");
        $getCategories = $operation->retrieveMany("SELECT *FROM categories");
        $getBusiness = $operation->retrieveMany("SELECT *FROM business_info");
        $getUnits = $operation->retrieveMany("SELECT *FROM units");

        $response_data['users'] = $getUsers;
        $response_data['products'] = $getProducts;
        $response_data['categories'] = $getCategories;
        $response_data['business_info'] = $getBusiness;
        $response_data['units'] = $getUnits;


        $response->write(json_encode($response_data));

        return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200);

    }else{
        $response_data = array();

        $response_data['error']=true; 
        $response_data['message'] = 'Nothing on server!';

        $response->write(json_encode($response_data));

        return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(201);
    }

    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(422); 
    
});




//DELETE METHODS

//delete category
$app->delete('/delete_category/{category_id}',function(Request $request, Response $response, array $args){

    $id = $args['category_id'];

    $operation = new Functions();


    //check if category exists
    $query = "SELECT * FROM `categories` 
    WHERE category_id = '$id'
    ";
    
    if($operation->countAll($query) > 0 ){

        $tbl = "categories";
        $where = "category_id = '$id'";

        if ($operation->deleteData($tbl,$where) == 1) {

            $response_data = array();

            $response_data['error']=false; 
            $response_data['message'] = 'category deleted';
            
                 //retrieve all other data
            $getUsers = $operation->retrieveMany("SELECT *FROM users");
            $getSuppliers = $operation->retrieveMany("SELECT *FROM supplier");
            $getProducts = $operation->retrieveMany("SELECT *FROM products");
            $getCategories = $operation->retrieveMany("SELECT *FROM categories");
            $getCheckout = $operation->retrieveMany("SELECT *FROM checkout");
            $getSales = $operation->retrieveMany("SELECT *FROM sales");

            $response_data['users'] = $getUsers;
            $response_data['suppliers'] = $getSuppliers;
            $response_data['products'] = $getProducts;
            $response_data['categories'] = $getCategories;
            $response_data['checkouts'] = $getCheckout;
            $response_data['sales'] = $getSales;


            $response->write(json_encode($response_data));

            return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(200);


        }else{
            $response_data['error']=true; 
            $response_data['message'] = 'Not Deleted';

            $response->write(json_encode($response_data));

            return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(200);
        }



        
    }else{
        $response_data = array();

        $response_data['error']=true; 
        $response_data['message'] = 'The requested category not found!';

        $response->write(json_encode($response_data));

        return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200);
    }
    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(422);   
});

//delete supplier
$app->delete('/delete_supplier/{supplier_id}',function(Request $request, Response $response, array $args){

    $id = $args['supplier_id'];

    $operation = new Functions();


    //check if category exists
    $query = "SELECT * FROM `supplier` 
    WHERE supplier_id = '$id'
    ";
    
    if($operation->countAll($query) > 0 ){

        $tbl = "supplier";
        $where = "supplier_id = '$id'";

        if ($operation->deleteData($tbl,$where) == 1) {

            $response_data = array();

            $response_data['error']=false; 
            $response_data['message'] = 'supplier deleted';
            
            //retrieve all other data
                  //retrieve all other data
            $getUsers = $operation->retrieveMany("SELECT *FROM users");
            $getSuppliers = $operation->retrieveMany("SELECT *FROM supplier");
            $getProducts = $operation->retrieveMany("SELECT *FROM products");
            $getCategories = $operation->retrieveMany("SELECT *FROM categories");
            $getCheckout = $operation->retrieveMany("SELECT *FROM checkout");
            $getSales = $operation->retrieveMany("SELECT *FROM sales");

            $response_data['users'] = $getUsers;
            $response_data['suppliers'] = $getSuppliers;
            $response_data['products'] = $getProducts;
            $response_data['categories'] = $getCategories;
            $response_data['checkouts'] = $getCheckout;
            $response_data['sales'] = $getSales;


            $response->write(json_encode($response_data));

            return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(200);


        }else{
            $response_data['error']=true; 
            $response_data['message'] = 'Supplier not Deleted';

            $response->write(json_encode($response_data));

            return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(200);
        }



        
    }else{
        $response_data = array();

        $response_data['error']=true; 
        $response_data['message'] = 'The requested supplier not found!';

        $response->write(json_encode($response_data));

        return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200);
    }
    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(422);   
});

//delete product
$app->delete('/delete_product/{product_id}',function(Request $request, Response $response, array $args){

    $id = $args['product_id'];

    $operation = new Functions();


    //check if product exists
    $query = "SELECT * FROM `products` 
    WHERE product_id = '$id'
    ";
    
    if($operation->countAll($query) > 0 ){

        $tbl = "products";
        $where = "product_id = '$id'";

        if ($operation->deleteData($tbl,$where) == 1) {

            $response_data = array();


            $directory = $this->get('upload_products');                
            //get file and delete
            $getFile = $operation->retrieveSingle($query);
            $file = $getFile['product_img_url'];

            //delele old file
            if (unlink($directory.$file)) {}

                $response_data['error']=false; 
            $response_data['message'] = 'product deleted';
            
                 //retrieve all other data
            $getUsers = $operation->retrieveMany("SELECT *FROM users");
            $getProducts = $operation->retrieveMany("SELECT *FROM products");
            $getCategories = $operation->retrieveMany("SELECT *FROM categories");
            $getBusiness = $operation->retrieveMany("SELECT *FROM business_info");
            $getUnits = $operation->retrieveMany("SELECT *FROM units");

            $response_data['users'] = $getUsers;
            $response_data['products'] = $getProducts;
            $response_data['categories'] = $getCategories;
            $response_data['business_info'] = $getBusiness;
            $response_data['units'] = $getUnits;


            $response->write(json_encode($response_data));

            return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(200);


        }else{
            $response_data['error']=true; 
            $response_data['message'] = 'Product/item not deleted';

            $response->write(json_encode($response_data));

            return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(200);
        }



        
    }else{
        $response_data = array();

        $response_data['error']=true; 
        $response_data['message'] = 'The requested product/item not found!';

        $response->write(json_encode($response_data));

        return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200);
    }
    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(422);   
});

//delete business info
$app->delete('/delete_business/{business_id}',function(Request $request, Response $response, array $args){

    $id = $args['business_id'];

    $operation = new Functions();


    //check if business exists
    $query = "SELECT * FROM `business_info` 
    WHERE business_id = '$id'
    ";
    
    if($operation->countAll($query) > 0 ){

        $tbl = "business_info";
        $where = "business_id = '$id'";

        if ($operation->deleteData($tbl,$where) == 1) {

            $response_data = array();

            $response_data['error']=false; 
            $response_data['message'] = 'Business Info deleted';
            
                  //retrieve all other data
        $getUsers = $operation->retrieveMany("SELECT *FROM users");
        $getProducts = $operation->retrieveMany("SELECT *FROM products");
        $getCategories = $operation->retrieveMany("SELECT *FROM categories");
        $getBusiness = $operation->retrieveMany("SELECT *FROM business_info");
        $getUnits = $operation->retrieveMany("SELECT *FROM units");

        $response_data['users'] = $getUsers;
        $response_data['products'] = $getProducts;
        $response_data['categories'] = $getCategories;
        $response_data['business_info'] = $getBusiness;
        $response_data['units'] = $getUnits;



            $response->write(json_encode($response_data));

            return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(200);


        }else{
            $response_data['error']=true; 
            $response_data['message'] = 'Business info not Deleted';

            $response->write(json_encode($response_data));

            return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(200);
        }



        
    }else{
        $response_data = array();

        $response_data['error']=true; 
        $response_data['message'] = 'The requested business info not found!';

        $response->write(json_encode($response_data));

        return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200);
    }
    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(422);   
});







function haveEmptyParameters($required_params, $request, $response){
    $error = false; 
    $error_params = '';
    $request_params = $request->getParsedBody(); 

    foreach($required_params as $param){
        if(!isset($request_params[$param]) || strlen($request_params[$param])<=0){
            $error = true; 
            $error_params .= $param . ', ';
        }
    }

    if($error){
        $error_detail = array();
        $error_detail['error'] = true; 
        $error_detail['message'] = 'Required parameters ' . substr($error_params, 0, -2) . ' are missing or empty';
        $response->write(json_encode($error_detail));
    }
    return $error; 
}


function haveEmptyArrayParameters($required_params, $request, $response){
   $error = false; 
   $error_params = '';
   $request_params = $request->getParsedBody(); 

   foreach($required_params as $param){
    if (!array_key_exists($param,$request_params) || count($request_params[$param]) <= 0){
       $error = true; 
       $error_params .= $param . ', ';


   }
}

if($error){
    $error_detail = array();
    $error_detail['error'] = true; 
    $error_detail['message'] = 'Required parameters ' . substr($error_params, 0, -2) . ' are missing or empty';
    $response->write(json_encode($error_detail));
}
return $error; 
}

function getOtherData(){
    $operation = new Functions();
    $response_data = array();
        //retrieve all other data
              //retrieve all other data
    $getUsers = $operation->retrieveMany("SELECT *FROM users");
    $getSuppliers = $operation->retrieveMany("SELECT *FROM supplier");
    $getProducts = $operation->retrieveMany("SELECT *FROM products");
    $getCategories = $operation->retrieveMany("SELECT *FROM categories");
    $getCheckout = $operation->retrieveMany("SELECT *FROM checkout");
    $getSales = $operation->retrieveMany("SELECT *FROM sales");

    $response_data['users'] = $getUsers;
    $response_data['suppliers'] = $getSuppliers;
    $response_data['products'] = $getProducts;
    $response_data['categories'] = $getCategories;
    $response_data['checkouts'] = $getCheckout;
    $response_data['sales'] = $getSales;
}

/**
 * Moves the uploaded file to the upload directory and assigns it a unique name
 * to avoid overwriting an existing uploaded file.
 *
 * @param string $directory directory to which the file is moved
 * @param UploadedFile $uploadedFile file uploaded file to move
 * @return string filename of moved file
 */
function moveUploadedFile($directory, Slim\Http\UploadedFile $uploadedFile){
    $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
    $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
    $filename = sprintf('%s.%0.8s', $basename, $extension);

    $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

    return $filename;
}


$app->run();

