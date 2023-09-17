<?php

namespace App\Http\Services\List;
use App\Models\ListModels;
use Illuminate\Support\Facades\Log;

class ListServices 
{

	public function listAllServicePublic($payload)
	{		
        // if(!array_key_exists('sort_by_id', $payload))
        // {
     	//    	$payload['sort_by_id'] = "id";
        // }

        //$value = $payload['offset'];

        if(!array_key_exists('sort_by_name', $payload))	
        {
        	$payload['sort_by_name'] = "name" ;
        }
        if(!array_key_exists('limit', $payload))
        {
        	$payload['limit'] = 100;
        }
        if(!array_key_exists('offset', $payload))
        {
        	$payload['offset'] = $payload['offset'];
        }
       
        $sortOrder = array_key_exists('sort_by', $payload) ? $payload['sort_by'] : 'asc';

		return ListModels::listAllServicePublic($payload,  $sortOrder); //$sortOrder
	}

	// public function serviceList($payload)
	// {
	// 	return ListModels::serviceList($payload);
	// }

	public function listAllCategoriesPublic($payload) 
	{
        if(!array_key_exists('sort_by_name', $payload))	
        {
        	$payload['sort_by_name'] = "name";
        }
        if (!array_key_exists('limit', $payload)) 
        {
        	$payload['limit'] = 100; 
   		}
    	if (!array_key_exists('offset', $payload)) 
    	{
        	$payload['offset'] = $payload['offset']; 
    	}

    	$sortOrder = array_key_exists('sort_order', $payload) ? $payload['sort_order'] : 'asc';

		return ListModels::listAllCategoriesPublic($payload, $sortOrder);
	}
	
	public function listAllServiceSubtypesPublic($payload)
	{	
        if(!array_key_exists('sort_by_name', $payload))	
        {
        	$payload['sort_by_name'] = "name";
        }
        if(!array_key_exists('limit', $payload))
        {
        	$payload['limit'] = 100;
        }
        if(!array_key_exists('offset', $payload))
        {
        	$payload['offset'] = $payload['offset'];
        }

        $sortOrder = array_key_exists('sort_order', $payload) ? $payload['sort_order'] : 'asc';

		return ListModels::listAllServiceSubtypesPublic($payload, $sortOrder);
	}
	
	public function autoSuggestionServices($payload)
	{
		return ListModels::autoSuggestionServices($payload);
	}	
	
	public function autoSuggestionCategories($payload)
	{		
		return ListModels::autoSuggestionCategories($payload);
	}

	public function autoSuggestionSubtypes($payload)
	{		
		return ListModels::autoSuggestionSubtypes($payload);
	}

	public function autoSuggestionServicesPublic($payload)
	{
		return ListModels::autoSuggestionServicesPublic($payload);
	}

	public function autoSuggestionCategoriesPublic($payload)
	{
		return ListModels::autoSuggestionCategoriesPublic($payload);
	}
	
	public function bookAppointment($payload)
	{	
    
	    $dateTime = $payload['date'] . $payload['time'];

	    $epochTime = strtotime($dateTime);

	    $insertData = array(
	        'subtypes' =>  $payload['subtype_id'],
	        'book_date' => $epochTime,
	        'book_by'   => $payload['account_id'],
	        'crated_on' => time()
	    );

		return ListModels::bookAppointment($insertData);
	}

public function findExistingDate($date)
{
    return ListModels::findExistingDate($date);
}

public function getSubtypesForBookedBy($bookedById)
{
    return ListModels::getSubtypesForBookedBy($bookedById);
}



}
