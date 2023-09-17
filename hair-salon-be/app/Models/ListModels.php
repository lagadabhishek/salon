<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class ListModels extends Model
{
    use HasFactory;


        public static function listAllServicePublic($payload,  $sortOrder)
        {   
           
            try {
                $list = DB::table('tbl_services')
                            ->select('tbl_services.id as service_id', 'name', 'status', 'discription', 'gender', 'created_on')
                            ->where('id', $payload['service_id'])
                            ->groupBy('tbl_services.name')
                            ->orderBy($payload['sort_by_name'], $sortOrder) 
                            ->limit($payload['limit'])
                            ->offset($payload['offset'])
                            ->get();

                    foreach ($list as $service) {
                            $service->created_on = date('m-d-Y', $service->created_on);

                            // Count the number of categories for this service..
                            $categoriesCount = DB::table('tbl_category')
                                ->where('service_id', $service->service_id)
                                ->count();

                            $service->categories_count = $categoriesCount;
                         }

                return$list;
             } catch (Exception $e) {
                 Log::info("Got exception in listAllServicePublic" . __METHOD__ . " " . PHP_EOL . $e->getMessage());
             }
        }  

    // public static function serviceList($payload)
    // {
    //     try {
    //         $list = DB::table('tbl_services')
    //             ->select('tbl_services.id as service_id', 'tbl_services.name as service_name', 'tbl_category.name as category_name')
    //             ->join('tbl_category', 'tbl_services.id', '=', 'tbl_category.service_id')
    //             ->where('tbl_services.id', $payload['service_id'])
    //             ->count();

    //         return $list;
    //     } catch (Exception $e) {
    //         Log::error("Got exception in listAllServicePublic" . __METHOD__ . " " . PHP_EOL . $e->getMessage()); 
    //     }
    // } 

    public static function listAllCategoriesPublic($payload, $sortOrder)
    {                                  
        try {
            $list = DB::table('tbl_category')
                        ->select('tbl_category.id as cat_id', 'name', 'discription', 'status', 'gender', 'created_on')
                        ->groupBy('tbl_category.id', 'tbl_category.name')
                        ->where('tbl_category.id', $payload['cat_id'])
                        ->orderBy($payload['sort_by_name'], $sortOrder)
                        ->limit($payload['limit'])
                        ->offset($payload['offset'])
                        ->get();

                        foreach ($list as $categori) {
                            $categori->created_on = date('m-d-Y', $categori->created_on);

                            // Count the number of subttype for this categories..
                            $subtypeCount = DB::table('tbl_service_subtype')
                                ->where('cat_id', $categori->cat_id)
                                ->count();

                            $categori->subtype_count = $subtypeCount;
                         }

           return $list;
        } catch (Exception $e) {
            Log::info("Got exception in listAllCategoriesPublic" . __METHOD__ . " " . PHP_EOL . $e->getMessage());
        }
    }
    
    public static function listAllServiceSubtypesPublic($payload,$sortOrder)
    {
        try {
            $list = DB::table('tbl_service_subtype')
                        ->select('id', 'service_id', 'cat_id', 'name', 'status', 'discription', 'gender', 'price', 'time', 'created_on')
                        ->where('service_id', $payload['service_id'])
                        ->where('cat_id', $payload['cat_id'])
                        ->groupBy('tbl_service_subtype.name')
                        ->orderBy($payload['sort_by_name'], $sortOrder)
                        ->limit($payload['limit'])
                        ->offset($payload['offset'])
                        ->get();

                        // ->orderBy($payload['sort_by_id'], $payload['sort_by_name'])
            foreach ($list as $value)
            {
                $value->time       = date('h:i:s A', $value->time);
                $value->created_on = date('m-d-Y', $value->created_on);
            }
            
            return$list;
        } catch (Exception $e) {
            Log::info("Got exception in listAllServiceSubtypesPublic" . __METHOD__ . " " . PHP_EOL . $e->getMessage());
        }
    } 

    public static function autoSuggestionServices($payload)
    {
        $list = DB::table('tbl_services')
                ->select('id as service_id', 'name', 'status', 'discription', 'gender', 'created_on')
                ->where('name','LIKE','%'.$payload['name'].'%')
                ->where('created_by', $payload['account_id'])
                //->where('grp_trade_created_by', $payload['account_id'])
                ->get();

        return $list;
    }

    public static function autoSuggestionCategories($payload)
    {   
        $list = DB::table('tbl_category')
                ->select('id as service_id', 'name', 'discription', 'status', 'gender', 'created_on')
                //->where($userData, 'LIKE' ,'%')
                ->where('name', 'LIKE' ,'%'.$payload['name'].'%')
                ->where('created_by', $payload['account_id'])
                ->get();

        return $list;   
    }

    public static function autoSuggestionSubtypes($payload)
    {   
        $list = DB::table('tbl_service_subtype')
                ->select('id', 'service_id', 'cat_id', 'name', 'status', 'discription', 'gender', 'price', 'time', 'created_on')
                ->where('name', 'LIKE' ,'%'.$payload['name'].'%')
                ->get();

        return $list;   
    }

    public static function autoSuggestionServicesPublic($payload)
    {   
        $list = DB::table('tbl_services')
                ->select('id as service_id', 'name', 'status', 'discription', 'gender', 'created_on')
                ->where('name','LIKE','%'.$payload['name'].'%')
                ->get();

        return $list;
    }

    public static function autoSuggestionCategoriesPublic($payload)
    {   
         $list = DB::table('tbl_category')
                ->select('id as service_id', 'name', 'discription', 'status', 'gender', 'created_on')
                ->where('name', 'LIKE' ,'%'.$payload['name'].'%')
                ->get();

        return $list;   
    }

    public static function autoSuggestionSubtypesPublic($payload)
    {   
        $list = DB::table('tbl_service_subtype')
                ->select('id', 'service_id', 'cat_id', 'name', 'status', 'discription', 'gender', 'price', 'time', 'created_on')
                ->where('name', 'LIKE' ,'%'.$payload['name'].'%')
                ->get();

        return $list;

    }

    public static function bookAppointment($insertData)
    {
        $list = DB::table('tbl_book_appointments')->insertGetId($insertData);

        return $list;

    }

    // public static function countDate($date)
    // {
    //     $count = DB::table('tbl_book_appointments')
    //                 ->where('book_by', $date)
    //                 //->where('created_by', $accountID)
    //                 ->count();
            
    //         return $count;

    // }

// public static function countDate($date)
// {
//     return DB::table('tbl_book_appointments')
//         ->where('book_date', date('d-m-Y', $date)) // Convert epoch date to database date format
//         ->count();
// }
    


    
public static function findExistingDate($date)
{
    return DB::table('tbl_book_appointments')
        ->where('book_date', date('d-m-Y', $date)) // Convert epoch date to database date format
        ->first();
}

public static function getSubtypesForBookedBy($bookedById)
{
 
    return DB::table('tbl_book_appointments')
        ->where('book_by', $bookedById)
        ->distinct()
        ->pluck('subtypes')
        ->toArray();
}

}



