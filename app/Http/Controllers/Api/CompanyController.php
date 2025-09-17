<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\company;
use App\Models\user_active_companies;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if(!$user){
            return  response()->json(['message' => 'Unauthenticated'],401);
        }
        if( $request->name==""){
            return  response()->json(['message' => 'Please Enter company'],401);
        }
        if( $request->industry==""){
            return  response()->json(['message' => 'Please Enter industry'],401);
        }
        $data = array(
            "user_id" => $user->id,
            "name" => $request->name,
            "address" => $request->address,
            "industry" => $request->industry,
        );
        try {
        $added =company::insertGetId($data);
        if($added){
         return response()->json(['message' => 'company is registered now'],200);
        }
        else{
            return response()->json(['message' => 'Something went wrong'],500);
        }
        } catch (QueryException $e) {
            return response()->json([
                'error' => 'Database query error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $user = $request->user();
        if(!$user){
            return  response()->json(['message' => 'Unauthenticated'],401);
        }

        if($request->company_id==""){
            return  response()->json(['message' => 'Please Enter Company'],401);
        }
        $checkCompany =company::where('id',$request->company_id)->count();
        if($checkCompany==0){
            return  response()->json(['message' => 'Company Not Found'],404);
        }
        try {
         $checkRecord = user_active_companies::where("user_id", $user->id)->where("company_id", $request->company_id)->first();
        if(!empty($checkRecord)){
            return  response()->json(['message' => 'Please check this company is already active'],401);
        }
        else{
          $number =  user_active_companies::where("user_id", $user->id)->count();
          if($number >0){
                user_active_companies::where("user_id", $user->id)->delete();
          }

            $data = array(
            "user_id" => $user->id,
            "company_id" => $request->company_id,
        );
            try {
        $added =user_active_companies::insertGetId($data);
        if($added){
            return  response()->json(['message' => 'company successfully activated'],200);
        }
        else{
            return  response()->json(['message' => 'Something went wrong'],500);
        }
        } catch (QueryException $e) {
            return response()->json([
                'error' => 'Database query error',
                'message' => $e->getMessage(),
            ], 500);
        }
        }

        } catch (QueryException $e) {
            return response()->json([
                'error' => 'Database query error',
                'message' => $e->getMessage(),
            ], 500);
        }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompanyRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
         $user = $request->user();
        if(!$user){
            return  response()->json(['message' => 'Unauthenticated'],401);
        }
        //print_r($user->id); die;

         $allCompany = company::with(['users:id,name','ActiveCompany:id,company_id'])->whereRelation('users','id', $user->id)->get()->map(function ($key) {
            // print_r(json_encode($key)); die;
            return [
            'Companyname' => $key->name,
            'industry' => $key->industry,
            'address' => $key->address,
            'username' => $key->users?->name,
            'ActiveCompany' => isset($key->ActiveCompany?->id)?'yes':'no',
        ];

         });
         if($allCompany->isEmpty()){
             return  response()->json(['message' => 'No Data Found','data'=>[]],401);
         }
         return  response()->json(['message' => 'Data Found','data'=>$allCompany],200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        $user = $request->user();
        if(!$user){
            return  response()->json(['message' => 'Unauthenticated'],401);
        }
        if( $request->company_id==""){
            return  response()->json(['message' => 'Please Enter Company Id'],401);
        }
        if( $request->name==""){
            return  response()->json(['message' => 'Please Enter company name'],401);
        }
        if( $request->industry==""){
            return  response()->json(['message' => 'Please Enter industry'],401);
        }
        $checkCompany =company::where('id',$request->company_id)->count();
        if($checkCompany==0){
            return  response()->json(['message' => 'Company Not Found'],404);
        }

       
        $data = array(
            "name" => $request->name,
            "address" => $request->address,
            "industry" => $request->industry,
        );
        try {
        $update =company::where("id",$request->company_id)->where("user_id",$user->id)->update($data);
        if($update){
         return response()->json(['message' => 'company details is updated'],200);
        }
        else{
            return response()->json(['message' => 'Something went wrong'],500);
        }
      } catch (QueryException $e) {
            return response()->json([
                'error' => 'Database query error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompanyRequest $request, Company $company)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
       if($request->company_id==""){
        return  response()->json(['message' => 'Please Enter Company Id'],401);
        }
        try {
           $company = company::findOrFail($request->company_id)->delete();
           return response()->json(['message' => 'company deleted successfully.']);
        } catch (ModelNotFoundException $e) {
         return response()->json(['message' => 'company not found'],404);
        }
    }
}
