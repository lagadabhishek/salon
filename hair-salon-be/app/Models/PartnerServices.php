<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PartnerServices extends Model
{
    use HasFactory;

    public static function createService($insertData)
    {
        try {
            $lastInsertedID = DB::table('tbl_services')->insertGetId($insertData);
            Log::info("Service created successfully. [".json_encode($insertData)."]");
            return$lastInsertedID;
        } catch (Exception $e) {
            Log::info("Got exception in createService" . __METHOD__ . " " . PHP_EOL . $e->getMessage());
        }
    }

    public static function isNameExists($name, $accountID)
    {
        try {
            $count = DB::table('tbl_services')
                ->where('name', $name)
                ->where('created_by', $accountID)
                ->count();

            if ($count > 0) {
                Log::info("Service name already exists. [".$name."][".$accountID."]");
                return true;
            }
            else
                return false;

        } catch (Exception $e) {
            Log::info("Got exception in isNameExists" . __METHOD__ . " " . PHP_EOL . $e->getMessage());
        }
    }

    public static function listAllService($criteria)
    {
        try {
            $list = DB::table('tbl_services')
                        ->select('id as service_id', 'name', 'status', 'discription', 'gender', 'created_on')
                        ->where('created_by', $criteria['account_id'])
                        ->get();
        
             foreach ($list as $service) {
                            $service->created_on = date('m-d-Y', $service->created_on);

                            // Count the number of categories for this service..
                            $categoriesCount = DB::table('tbl_category')
                                ->where('service_id', $service->service_id)
                                ->count();

                            $service->categories_count = $categoriesCount;
                         }



            Log::info("Service list fetched successfully. [".$criteria['account_id']."]");
            return$list;
        } catch (Exception $e) {
            Log::info("Got exception in listAllService" . __METHOD__ . " " . PHP_EOL . $e->getMessage());
        }
    }
//new code for the service id

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

    public static function countServiceID($accountID, $serviceID)
    {
        try {
            $count = DB::table('tbl_services')
                        ->where('id', $serviceID)
                        ->where('created_by', $accountID)
                        ->count();
            return $count;
        } catch (Exception $e) {
            Log::info("Got exception in countServiceID" . __METHOD__ . " " . PHP_EOL . $e->getMessage());
        }
    }

    public static function getServiceDetails($payload)
    {
        try {
            $details = DB::table('tbl_services')
                        ->select('id as service_id', 'name', 'status','discription', 'gender')
                        ->where('id', $payload['service_id'])
                        ->where('created_by', $payload['account_id'])
                        ->first();
            Log::info("Service details fetched successfully. [".$payload['account_id']."]");
            return $details;
        } catch (Exception $e) {
            Log::info("Got exception in getServiceDetails" . __METHOD__ . " " . PHP_EOL . $e->getMessage());
        }
    }

    public static function countName($name, $accountID, $serviceID)
    {
        try {
            $count = DB::table('tbl_services')
                ->where('name', $name)
                ->where('created_by', $accountID)
                ->where('id', '!=', $serviceID)
                ->count();

            if ($count > 0) {
                Log::info("Service name already exists. [".$name."][".$accountID."][".$serviceID."]");
                return true;
            }
            else
                return false;

        } catch (Exception $e) {
            Log::info("Got exception in countName" . __METHOD__ . " " . PHP_EOL . $e->getMessage());
        }
    }

    public static function updateService($updateData, $serviceID)
    {
        try {
            $update = DB::table('tbl_services')
                ->where('id', $serviceID)
                ->update($updateData);
            Log::info("Service updated successfully. [".$serviceID."]");
            return$update;
        } catch (Exception $e) {
            Log::info("Got exception in updateService" . __METHOD__ . " " . PHP_EOL . $e->getMessage());
        }
    }

    public static function serviceStatusUpdate($serviceID, $updateData)
    {
        $statusUpdate = DB::table('tbl_services')
                    ->where('id', $serviceID)
                    ->update($updateData);
        
        return $statusUpdate;
    }

    public static function deleteService($payload, $serviceID)
    {          
            $deleteService = DB::table('tbl_services')
                        ->where('id', $serviceID)
                        ->delete();
                   
            return $deleteService;
    }

    public static function createServiceCategory($insertData)
    {
        try {
            $lastInsertedID = DB::table('tbl_category')->insertGetId($insertData);
            Log::info("Service category created successfully. [".json_encode($insertData)."]");
            return$lastInsertedID;
        } catch (Exception $e) {
            Log::info("Got exception in createServiceCategory   " . __METHOD__ . " " . PHP_EOL . $e->getMessage());
        }
    }

    public static function isCatNameExists($name, $accountID)
    {
        try {
            $count = DB::table('tbl_category')
                ->where('name', $name)
                ->where('created_by', $accountID)
                ->count();

            if ($count > 0) {
                Log::info("Service category name already exists. [".$name."][".$accountID."]");
                return true;
            }
            else
                return false;

        } catch (Exception $e) {
            Log::info("Got exception in isCatNameExists" . __METHOD__ . " " . PHP_EOL . $e->getMessage());
        }
    }

    public static function listAllCategories($criteria)
    {
        try {
            $list = DB::table('tbl_category')
                        ->select('id as cat_id', 'service_id', 'name', 'discription', 'status', 'gender', 'created_on')
                        ->where('created_by', $criteria['account_id'])
                        ->get();
            // foreach ($list as $value)
            //     $value->created_on = date('m-d-Y', $value->created_on);


            foreach ($list as $category) {
                $category->created_on = date('m-d-Y', $category->created_on);

                // Count the number of subttype for this categories..
                $subtypeCount = DB::table('tbl_service_subtype')
                    ->where('cat_id', $category->cat_id)
                    ->count();

                $category->subtype_count = $subtypeCount;
             }

            
            Log::info("Categories list fetched successfully. [".$criteria['account_id']."]");
            return$list;
        } catch (Exception $e) {
            Log::info("Got exception in listAllCategories" . __METHOD__ . " " . PHP_EOL . $e->getMessage());
        }
    }

    public static function countCatID($accountID, $catID)
    {
        try {
            $count = DB::table('tbl_category')
                        ->where('id', $catID)
                        ->where('created_by', $accountID)
                        ->count();
            return $count;
        } catch (Exception $e) {
            Log::info("Got exception in countCatID" . __METHOD__ . " " . PHP_EOL . $e->getMessage());
        }
    }

    public static function getCategoryDetails($payload)
    {
        try {
            $details = DB::table('tbl_category')
                        ->select('id as cat_id', 'service_id', 'name', 'status', 'gender')
                        ->where('id', $payload['cat_id'])
                        ->where('created_by', $payload['account_id'])
                        ->first();
            Log::info("Category details fetched successfully. [".$payload['account_id']."]");
            return $details;
        } catch (Exception $e) {
            Log::info("Got exception in getCategoryDetails" . __METHOD__ . " " . PHP_EOL . $e->getMessage());
        }
    }

    public static function updateCategory($updateData, $catID)
    {
        try {
            $update = DB::table('tbl_category')
                ->where('id', $catID)
                ->update($updateData);
            Log::info("Category updated successfully. [".$catID."]");
            return$update;
        } catch (Exception $e) {
            Log::info("Got exception in updateCategory" . __METHOD__ . " " . PHP_EOL . $e->getMessage());
        }
    }

    public static function categoryStatusUpdate($catID, $updateData)
    {
        $updateStatus = DB::table('tbl_category')
                    ->where('id', $catID)
                    ->update($updateData);

        return $updateStatus;
    }

    public static function deleteCategory($payload,$categoryID)
    {
        $deleteCategory = DB::table('tbl_category')
                    ->where('id', $categoryID)
                    ->delete();
                    
        return $deleteCategory;
    }

    public static function countCatName($name, $accountID, $catID)
    {
        try {
            $count = DB::table('tbl_category')
                ->where('name', $name)
                ->where('created_by', $accountID)
                ->where('id', '!=', $catID)
                ->count();

            if ($count > 0) {
                Log::info("Category name already exists. [".$name."][".$accountID."][".$catID."]");
                return true;
            }
            else
                return false;

        } catch (Exception $e) {
            Log::info("Got exception in countCatName" . __METHOD__ . " " . PHP_EOL . $e->getMessage());
        }
    }

    public static function createSubType($insertData)
    {
        try {
            $lastInsertedID = DB::table('tbl_service_subtype')->insertGetId($insertData);
            Log::info("Service subtype created successfully. [".json_encode($insertData)."]");
            return$lastInsertedID;
        } catch (Exception $e) {
            Log::info("Got exception in createSubType   " . __METHOD__ . " " . PHP_EOL . $e->getMessage());
        }
    }

    public static function isSubTypeNameExists($name, $accountID)
    {
        try {
            $count = DB::table('tbl_service_subtype')
                ->where('name', $name)
                ->where('created_by', $accountID)
                ->count();

            if ($count > 0) {
                Log::info("Service subtype name already exists. [".$name."][".$accountID."]");
                return true;
            }
            else
                return false;

        } catch (Exception $e) {
            Log::info("Got exception in isSubTypeNameExists" . __METHOD__ . " " . PHP_EOL . $e->getMessage());
        }
    }

     public static function listAllServiceSubtypes($criteria)
    {
        try {
            $list = DB::table('tbl_service_subtype')
                        ->select('id as subtype_id', 'service_id', 'cat_id', 'name', 'status', 'discription', 'gender', 'price', 'time', 'created_on')
                        ->where('created_by', $criteria['account_id'])
                        ->get();
            foreach ($list as $value)
            {
                $value->time       = date('h:i:s A', $value->time);
                $value->created_on = date('m-d-Y', $value->created_on);
            }
            
            Log::info("Service subtypes list fetched successfully. [".$criteria['account_id']."]");
            return$list;
        } catch (Exception $e) {
            Log::info("Got exception in listAllServiceSubtypes" . __METHOD__ . " " . PHP_EOL . $e->getMessage());
        }
    }

    public static function countSubTypeID($accountID, $subTypeID)
    {
        try {
            $count = DB::table('tbl_service_subtype')
                        ->where('id', $subTypeID)
                        ->where('created_by', $accountID)
                        ->count();
            return $count;
        } catch (Exception $e) {
            Log::info("Got exception in countSubTypeID" . __METHOD__ . " " . PHP_EOL . $e->getMessage());
        }
    }

    public static function getSubTypeDetails($payload)
    {
        try {
            $details = DB::table('tbl_service_subtype')
                        ->select('id as subtype_id', 'service_id', 'cat_id', 'name', 'status', 'gender', 'price', 'time')
                        ->where('id', $payload['subtype_id'])
                        ->where('created_by', $payload['account_id'])
                        ->first();            
            $details->time       = date('h:i:s A', $details->time);
            Log::info("Service subtype details fetched successfully. [".$payload['account_id']."]");
            return $details;
        } catch (Exception $e) {
            Log::info("Got exception in getSubTypeDetails" . __METHOD__ . " " . PHP_EOL . $e->getMessage());
        }
    }

    public static function updateSubType($updateData, $subTypeID)
    {
        try {
            $update = DB::table('tbl_service_subtype')
                ->where('id', $subTypeID)
                ->update($updateData);
            Log::info("Service subtype updated successfully. [".$subTypeID."]");
            return $update;
        } catch (Exception $e) {
            Log::info("Got exception in updateSubType" . __METHOD__ . " " . PHP_EOL . $e->getMessage());
        }
    }

    public static function subtypeStatusUpdate($subTypeID, $updateData)
    {
        $updateStatus = DB::table('tbl_service_subtype')
                    ->where('id', $subTypeID)
                    ->update($updateData);

        return $updateStatus;
    }

    public static function deleteSubType($payload, $subTypeID)
    {
        $deleteSubType = DB::table('tbl_service_subtype')
                    ->where('id', $subTypeID)
                    ->delete();

        return $deleteSubType;
    }

    public static function countSubTypeName($name, $accountID, $subTypeID)
    {
        try {
            $count = DB::table('tbl_service_subtype')
                ->where('name', $name)
                ->where('created_by', $accountID)
                ->where('id', '!=', $subTypeID)
                ->count();

            if ($count > 0) {
                Log::info("Category name already exists. [".$name."][".$accountID."][".$subTypeID."]");
                return true;
            }
            else
                return false;

        } catch (Exception $e) {
            Log::info("Got exception in countSubTypeName" . __METHOD__ . " " . PHP_EOL . $e->getMessage());
        }
    }

    public static function getCategoriesList($payload)
    {
        try {
            $list = DB::table('tbl_category')
                        ->select('id as cat_id', 'service_id', 'name', 'status', 'gender', 'created_on')
                        ->where('service_id', $payload['service_id'])
                        ->where('created_by', $payload['account_id'])
                        ->get();
            foreach ($list as $value)
                $value->created_on = date('m-d-Y', $value->created_on);
            
            Log::info("Categories list fetched successfully for using service id. [".$payload['service_id']."] [".$payload['account_id']."]" );
            return$list;
        } catch (Exception $e) {
            Log::info("Got exception in getCategoriesList" . __METHOD__ . " " . PHP_EOL . $e->getMessage());
        }
    }

    public static function getSubTypeList($payload)
    {
        try {
            $list = DB::table('tbl_service_subtype')
                        ->select('id as subtype_id', 'service_id', 'cat_id', 'name', 'status', 'gender', 'price', 'time', 'created_on')
                        ->where('cat_id', $payload['cat_id'])
                        ->where('created_by', $criteria['account_id'])
                        ->get();
            foreach ($list as $value)
            {
                $value->time       = date('h:i:s A', $value->time);
                $value->created_on = date('m-d-Y', $value->created_on);
            }
            
            Log::info("Service subtypes list fetched successfully for using category id. [".$payload['cat_id']."] [".$payload['account_id']."]" );
            return$list;
        } catch (Exception $e) {
            Log::info("Got exception in getSubTypeList" . __METHOD__ . " " . PHP_EOL . $e->getMessage());
        }
    }

}
