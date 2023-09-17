<?php

namespace App\Http\Services\Partner;
use App\Models\PartnerServices;
use Illuminate\Support\Facades\Log;

class PartnerServicesService 
{
	public function createService($payload)
	{
		$insertData = array(
						'name' 			=> $payload['name'],
						'discription' 	=> $payload['discription'],
						'gender' 		=> $payload['gender'],
						'status' 		=> '1',
						'created_by' 	=> $payload['account_id'],
						'created_on' 	=> time()
					);
		return PartnerServices::createService($insertData);
	}

	public function isNameExists($name, $accountID)
	{
		return PartnerServices::isNameExists($name, $accountID);
	}

	public function listAllService($payload)
	{
		return PartnerServices::listAllService($payload);
	}
	
	public function countServiceID($accountID, $serviceID)
	{
		return PartnerServices::countServiceID($accountID, $serviceID);
	}

	public function getServiceDetails($payload)
	{
		return PartnerServices::getServiceDetails($payload);
	}

	public function countName($name, $accountID, $serviceID)
	{
		return PartnerServices::countName($name, $accountID, $serviceID);
	}

	public function updateService($payload)
	{
		$serviceID = $payload['service_id'];

		$updateData = array(
						'name' 			=> $payload['name'],
						'discription' 	=> $payload['discription'],
						'gender' 		=> $payload['gender'],
						'modified_by' 	=> $payload['account_id'],
						'modified_on' 	=> time()
					);

		     if (array_key_exists('status', $payload))
			 	$updateData['status'] = $payload['status'];

		return PartnerServices::updateService($updateData, $serviceID);
	}

	public function serviceStatusUpdate($payload)
	{
		$serviceID = $payload['service_id'];
		$updateData = array('status' => $payload['status']);

		return PartnerServices::serviceStatusUpdate($serviceID, $updateData);
	}

	public function deleteService($payload)
	{	
		$serviceID = $payload['service_id'];

		return PartnerServices::deleteService($payload, $serviceID);
	}

	public function createServiceCategory($payload)
	{
		$insertData = array(
						'service_id' 	=> $payload['service_id'],
						'name' 			=> $payload['name'],
						'discription' 	=> $payload['discription'],
						'gender' 		=> $payload['gender'],
						'status' 		=> '1',
						'created_by' 	=> $payload['account_id'],
						'created_on' 	=> time()
					);
		return PartnerServices::createServiceCategory($insertData);
	}

	public function isCatNameExists($name, $accountID)
	{
		return PartnerServices::isCatNameExists($name, $accountID);
	}

	public function listAllCategories($payload) 
	{
		return PartnerServices::listAllCategories($payload);
	}

	public function countCatID($accountID, $catID)
	{
		return PartnerServices::countCatID($accountID, $catID);
	}

	public function getCategoryDetails($payload)
	{
		return PartnerServices::getCategoryDetails($payload);
	}

	public function updateCategory($payload)
	{
		$catID = $payload['cat_id']; 
		$updateData = array(
						'service_id' => $payload['service_id'],
						'name' => $payload['name'],
						'discription' => $payload['discription'],
						'gender' => $payload['gender'],
						'modified_by' => $payload['account_id'],
						'modified_on' => time()
					);

		if (array_key_exists('status', $payload))
			$updateData['status'] = $payload['status'];

		return PartnerServices::updateCategory($updateData, $catID);
	}

	public function categoryStatusUpdate($payload)
	{	
		$catID = $payload['cat_id'];
		$updateData = array('status' => $payload['status']);
		
		return PartnerServices::categoryStatusUpdate($catID, $updateData);
	}

	public function deleteCategory($payload)
	{
		$categoryID = $payload['cat_id'];

		return PartnerServices::deleteCategory($payload, $categoryID);
	}

	public function countCatName($name, $accountID, $catID)
	{
		return PartnerServices::countCatName($name, $accountID, $catID);
	}

	public function createSubType($payload)
	{
		$insertData = array(
						'service_id' => $payload['service_id'],
						'cat_id' => $payload['cat_id'],
						'name' => $payload['name'],
						'discription' => $payload['discription'],
						'gender' => $payload['gender'],
						'price' => $payload['price'],
						'time' => strtotime($payload['time']),
						'status' => '1',
						'created_by' => $payload['account_id'],
						'created_on' => time()
					);
		return PartnerServices::createSubType($insertData);
	}

	public function isSubTypeNameExists($name, $accountID)
	{
		return PartnerServices::isSubTypeNameExists($name, $accountID);
	}

	public function listAllServiceSubtypes($payload)
	{
		return PartnerServices::listAllServiceSubtypes($payload);
	}

	public function countSubTypeID($accountID, $subTypeID)
	{
		return PartnerServices::countSubTypeID($accountID, $subTypeID);
	}

	public function getSubTypeDetails($payload)
	{
		return PartnerServices::getSubTypeDetails($payload);
	}

	public function updateSubType($payload)
	{
		$subTypeID = $payload['subtype_id']; 
		$updateData = array(
						'service_id' => $payload['service_id'],
						'cat_id' => $payload['cat_id'],
						'name' => $payload['name'],
						'discription' => $payload['discription'],
						'gender' => $payload['gender'],
						'price' => $payload['price'],
						'time' => strtotime($payload['time']),
						'modified_by' => $payload['account_id'],
						'modified_on' => time()
					);

		if (array_key_exists('status', $payload))
			$updateData['status'] = $payload['status'];

		return PartnerServices::updateSubType($updateData, $subTypeID);
	}

	public function subtypeStatusUpdate($payload)
	{	
		$subTypeID = $payload['subtype_id'];
		$updateData = array('status' => $payload['status']);
		
		return PartnerServices::subtypeStatusUpdate($subTypeID, $updateData);
	}

	public function deleteSubType($payload)
	{
		$subTypeID = $payload['subtype_id'];

		return PartnerServices::deleteSubType($payload, $subTypeID);
	}

	public function countSubTypeName($name, $accountID, $subTypeID)
	{
		return PartnerServices::countSubTypeName($name, $accountID, $subTypeID);
	}

	public function getCategoriesList($payload)
	{
		return PartnerServices::getCategoriesList($payload);
	}

	public function getSubTypeList($payload)
	{
		return PartnerServices::getSubTypeList($payload);
	}
}
