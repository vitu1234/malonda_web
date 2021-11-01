<?php

Class AllData{
		    //count total records
	
	function allData(){
		require_once dirname(__FILE__).'/Functions.php';
		$operation = new Functions();
		$query = "SELECT *FROM users ";
		$response_data = array();
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

	}
}	
?>