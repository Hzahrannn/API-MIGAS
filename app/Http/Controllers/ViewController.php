<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use App\Exports\Uploadan;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\Tools;
use App\Well;
use App\Upload;
use App\Kategori;
use App\User;
use Auth;
use App\Tanggal;

class ViewController extends BaseController
{
    public function photos(Request $request)
    {
        //GET TANGGAL
        if(empty($_GET['dateFrom']) && empty($_GET['dateTo'])){
            $kosong = 1;
        }
        else{
            if(!empty($_GET['dateFrom']) && empty($_GET['dateTo'])){
                $t2 = $_GET['dateFrom'];
                $t1 = date('Y-m-d');
            }
            
            else if(empty($_GET['dateFrom']) && !empty($_GET['dateTo'])){
                $t2 = $_GET['dateTo'];
                $t1 = date('Y-m-d');
            }
            else if(!empty($_GET['dateFrom']) && !empty($_GET['dateTo'])){
                $t2 = $_GET['dateFrom'];
                $t1 = $_GET['dateTo'];
            }
            
            $kosong = 0;
        }
        //END
        
        //GET PAGE
        if(!empty($_GET['page'])){
            $page = $_GET['page'];
        }
        else{
            $page=1;
        }
        //END
        
        // //GET PAGGING
        // if($kosong == 1){
        //     $page = $page * 2;
        //     $skip = -2 + $page;
        //     $take = 2;
            
        //     $pag = Tanggal::orderBy('date', 'ASC')->skip($skip)->take($take)->get();
            
        //     $c= count($pag);
            
        //     if($c == "2"){
                
        //         $t1 = $pag[0]->date;
        //         $t2 = $pag[1]->date;
                
                
        //     }
        //     else if($c == "1"){
                
        //         $t1 = $pag[0]->date;
        //         $t2 = 0;
                
        //     }
        //     else if($c == "0"){
        //         $t1=0;
        //         $t2=0;
                
        //     }
        // }
        // else{
        //     $page = $page * 2;
        //     $skip = -2 + $page;
        //     $take = 2;
            
        //     $pag = Tanggal::whereBetween('date', [$dateFrom, $dateTo])->skip($skip)->take($take)->get();
            
            
        //     $c= count($pag);
            
        //     if($c == "2"){
                
        //         $t1 = $pag[0]->date;
        //         $t2 = $pag[1]->date;
                
                
        //     }
        //     else if($c == "1"){
                
        //         $t1 = $pag[0]->date;
        //         $t2 = 0;
                
        //     }
        //     else if($c == "0"){
        //         $t1=0;
        //         $t2=0;
                
        //     }
            
        // }
        // //END
        
        //GET LIMIT
        if(!empty($_GET['limit'])){
            $limit = $_GET['limit'];
        }
        else{
            $limit=1;
        }
        //END
        
        //GET USER
        if(!empty($_GET['userId'])){
            $user = $_GET['userId'];
        }
        else{
            $user="0";
        }
        //END
        
        //GET Uploader
        if(!empty($_GET['uploader'])){
            $userid = $_GET['uploader'];
            $fuser = User::where('nama',$userid)->first();
            if(!empty($fuser)){
                $fUser = $fuser->id_user;
            }
            else{
                $fUser="";
            }
        }
        else{
            $userid="";
        }
        //END
        
        //GET KATEGORI
        if(!empty($_GET['category'])){
            $category = $_GET['category'];
            $fkat = Kategori::where('kategori',$category)->first();
            if(!empty($fkat)){
                $fKat = $fkat->id_kategori;
            }
            else{
                $fKat="";
            }
        }
        else{
            $category="";
        }
        //END
        
        //GET SUMUR
        if(!empty($_GET['well'])){
            $well = $_GET['well'];
            if(strpos($well, "-") !== false){
                $pecah = explode("-",$well);
                $fwell_id = $pecah[0];
                $fwell_nama = $pecah[1];
                
                $fWell_id = Well::where('nama_sumur',$fwell_id)->first();
                if(!empty($fWell_id)){
                    $fWell_id = $fWell_id->id_sumur;
                    $fWell_nama = $fwell_nama;
                }
                else{
                    $fWell_id="";
                    $fWell_nama = $fwell_nama;
                }
            }
            else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                $fWell_id="";
                $fWell_nama = $well;
            }
            else{
                $fWell_id = Well::where('nama_sumur',$well)->first();
                if(!empty($fWell_id)){
                    $fWell_id = $fWell_id->id_sumur;
                    $fWell_nama="";
                }
                else{
                    $fWell_id="";
                    $fWell_nama="";
                }
            }
        }
        else{
            $well;
        }
        //END
        
        //GET TOOL
        if(!empty($_GET['tool'])){
            $tool = $_GET['tool'];
            $ftool = Tools::where('tools',$tool)->first();
            if(!empty($ftool)){
                $fTool = $ftool->id_tools;
            }
            else{
                $fTool="";
            }
        }
        else{
            $tool="";
        }
        //END
        
        //GET SEARCH
        if(!empty($_GET['searchQuery'])){
            $search = $_GET['searchQuery'];
            
            //SEARCH TOOL
            $sT = Tools::where('tools','like','%'.$search.'%')->first();
            if(!empty($sT)){
                $sT_id = $sT->id_tools;
            }
            else{
                $sT_id="";
            }
            //END
            //SEARCH KATEGORI
            $sK = Kategori::where('kategori','like','%'.$search.'%')->first();
            if(!empty($sK)){
                $sK_id = $sK->id_kategori;
                
            }
            else{
                $sK_id="";
            }
            //END
            //SEARCH USER
            $sU = User::where('nama','like','%'.$search.'%')->first();
            if(!empty($sU)){
                $sU_id = $sU->id_user;
            }
            else{
                $sU_id="";
            }
            //END
            //SEARCH SUMUR
            if(strpos($search, "-") !== false){
                $exp = explode("-",$search);
                $well_name = $exp[0];
                $well_id = $exp[1];
                
                $aS = Well::where('nama_sumur',$well_name)->first();
                if(!empty($aS)){
                    $well_name = $aS->id_sumur="";
                }
                else{
                    $well_name = "";
                }
                
            }
            else if(strpos($search, "0") !== false || strpos($search, "1") !== false || strpos($search, "2") !== false || strpos($search, "3") !== false || strpos($search, "4") !== false){
                $well_name = "";
                $well_id = $search;
            }
            else{
                $well_name = $search;
                $aS = Well::where('nama_sumur',$search)->first();
                if(!empty($aS)){
                    $well_name = $aS->id_sumur;
                    $well_id = "";
                }
                else{
                    $well_name = "";
                    $well_id = "";
                }
            }
            //END
        }
        else{
            $search="";
        }
        //END
        
        //FILTER
        if($user == 0){
            if(!empty($search)){
                $page = $page * 2;$skip = -2 + $page;$take = 2;
                if($kosong == 1){
                    
                    $getd = Upload::orderBy('waktu','DESC')->Orwhere('id_user',$sU_id)->Orwhere('id_kategori',$sK_id)->Orwhere('id_tools',$sT_id)->Orwhere('id_sumur',$well_name)->Orwhere('no_sumur',$well_id)->distinct()->skip($skip)->take($take)->get(['waktu']);
                    
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    $photo = Upload::orderBy('waktu','DESC')->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($sK_id,$sT_id,$sU_id,$well_id,$well_name) {$query1->Orwhere('id_user',$sU_id)->Orwhere('id_kategori',$sK_id)->Orwhere('id_tools',$sT_id)->Orwhere('id_sumur',$well_name)->Orwhere('no_sumur',$well_id);})->select(['id_upload', 'foto','waktu'])->get();
                }
                else{
                    
                    $getd = Upload::orderBy('waktu','DESC')->Orwhere('id_user',$sU_id)->Orwhere('id_kategori',$sK_id)->Orwhere('id_tools',$sT_id)->Orwhere('id_sumur',$well_name)->Orwhere('no_sumur',$well_id)->whereBetween('waktu',[$t2,$t1])->distinct()->skip($skip)->take($take)->get(['waktu']);
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    $photo = Upload::orderBy('waktu','DESC')->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($sK_id,$sT_id,$sU_id,$well_id,$well_name) {$query1->Orwhere('id_user',$sU_id)->Orwhere('id_kategori',$sK_id)->Orwhere('id_tools',$sT_id)->Orwhere('id_sumur',$well_name)->Orwhere('no_sumur',$well_id);})->Orwhere('id_user',$sU_id)->Orwhere('id_kategori',$sK_id)->Orwhere('id_tools',$sT_id)->Orwhere('id_sumur',$well_id)->Orwhere('no_sumur',$well_name)->select(['id_upload', 'foto','waktu'])->get();
                }
                
            }
            
            if(empty($userid) && empty($category) && empty($well) && empty($tool) && empty($search)){
                
                $page = $page * 2;$skip = -2 + $page;$take = 2;
                if($kosong == 1){
                    $getd = Upload::orderBy('waktu','DESC')->distinct()->skip($skip)->take($take)->get(['waktu']);
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    $photo = Upload::orderBy('waktu','DESC')->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->select(['id_upload', 'foto','waktu'])->get();
                }
                else{
                    
                    $getd = Upload::orderBy('waktu','DESC')->whereBetween('waktu',[$t2,$t1])->distinct()->skip($skip)->take($take)->get(['waktu']);
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    
                    $photo = Upload::orderBy('waktu','DESC')->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->select(['id_upload', 'foto','waktu'])->get();
                }
                
            }
            
            if(!empty($userid) && empty($category) && empty($well) && empty($tool)){
                $page = $page * 2;$skip = -2 + $page;$take = 2;
                if($kosong == 1){
                    $getd = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->distinct()->skip($skip)->take($take)->get(['waktu']);
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    $photo = Upload::orderBy('waktu','DESC')->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where('id_user',$fUser)->select(['id_upload', 'foto','waktu'])->get();
                }
                else{
                    $getd = Upload::orderBy('waktu','DESC')->whereBetween('waktu',[$t2,$t1])->where('id_user',$fUser)->distinct()->skip($skip)->take($take)->get(['waktu']);
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    $photo = Upload::orderBy('waktu','DESC')->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where('id_user',$fUser)->select(['id_upload', 'foto','waktu'])->skip($skip)->take($take)->get();
                }
                
            }
            
            if(!empty($userid) && !empty($category) && empty($well) && empty($tool)){
                $page = $page * 2;$skip = -2 + $page;$take = 2;
                if($kosong == 1){
                    $getd = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->where('id_kategori',$fKat)->distinct()->skip($skip)->take($take)->get(['waktu']);
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    $photo = Upload::orderBy('waktu','DESC')->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where('id_user',$fUser)->where('id_kategori',$fKat)->select(['id_upload', 'foto','waktu'])->get();
                }
                else{
                    $getd = Upload::orderBy('waktu','DESC')->whereBetween('waktu',[$t2,$t1])->where('id_user',$fUser)->where('id_kategori',$fKat)->distinct()->skip($skip)->take($take)->get(['waktu']);
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    $photo = Upload::orderBy('waktu','DESC')->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where('id_user',$fUser)->where('id_kategori',$fKat)->select(['id_upload', 'foto','waktu'])->skip($skip)->take($take)->get();
                }
            }
            
            if(!empty($userid) && !empty($category) && !empty($well) && empty($tool)){
                $page = $page * 2;$skip = -2 + $page;$take = 2;
                if($kosong == 1){
                    if(strpos($well, "-") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else{
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    if(strpos($well, "-") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else{
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->select(['id_upload', 'foto','waktu'])->get();
                    }                
                    
                }
                else{
                    if(strpos($well, "-") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->whereBetween('waktu',[$t2,$t1])->where('id_user',$fUser)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->whereBetween('waktu',[$t2,$t1])->where('id_user',$fUser)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else{
                        $getd = Upload::orderBy('waktu','DESC')->whereBetween('waktu',[$t2,$t1])->where('id_user',$fUser)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    if(strpos($well, "-") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else{
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->select(['id_upload', 'foto','waktu'])->get();
                    }  
                }
                
            }
            
            if(!empty($userid) && !empty($category) && !empty($well) && !empty($tool)){
                $page = $page * 2;$skip = -2 + $page;$take = 2;
                if($kosong == 1){
                    if(strpos($well, "-") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->where('id_kategori',$fKat)->where('id_tools',$fTool)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->where('id_kategori',$fKat)->where('id_tools',$fTool)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else{
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->where('id_kategori',$fKat)->where('id_tools',$fTool)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    if(strpos($well, "-") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->where('id_kategori',$fKat)->where('id_tools',$fTool)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->where('id_kategori',$fKat)->where('id_tools',$fTool)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else{
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->where('id_kategori',$fKat)->where('id_tools',$fTool)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                }
                else{
                    if(strpos($well, "-") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->where('id_kategori',$fKat)->where('id_tools',$fTool)->whereBetween('waktu',[$t2,$t1])->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->where('id_kategori',$fKat)->where('id_tools',$fTool)->whereBetween('waktu',[$t2,$t1])->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else{
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->where('id_kategori',$fKat)->where('id_tools',$fTool)->whereBetween('waktu',[$t2,$t1])->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    if(strpos($well, "-") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->where('id_kategori',$fKat)->where('id_tools',$fTool)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->where('id_kategori',$fKat)->where('id_tools',$fTool)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else{
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->where('id_kategori',$fKat)->where('id_tools',$fTool)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                }
                
            }
            
            if(empty($userid) && !empty($category) && !empty($well) && !empty($tool)){
                $page = $page * 2;$skip = -2 + $page;$take = 2;
                if($kosong == 1){
                    if(strpos($well, "-") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_kategori',$fKat)->where('id_tools',$fTool)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_kategori',$fKat)->where('id_tools',$fTool)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else{
                        $getd = Upload::orderBy('waktu','DESC')->where('id_kategori',$fKat)->where('id_tools',$fTool)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    if(strpos($well, "-") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_kategori',$fKat)->where('id_tools',$fTool)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_kategori',$fKat)->where('id_tools',$fTool)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else{
                        $photo = Upload::orderBy('waktu','DESC')->where('id_kategori',$fKat)->where('id_tools',$fTool)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                }
                else{
                    if(strpos($well, "-") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_kategori',$fKat)->where('id_tools',$fTool)->whereBetween('waktu',[$t2,$t1])->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_kategori',$fKat)->where('id_tools',$fTool)->whereBetween('waktu',[$t2,$t1])->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else{
                        $getd = Upload::orderBy('waktu','DESC')->where('id_kategori',$fKat)->where('id_tools',$fTool)->whereBetween('waktu',[$t2,$t1])->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    if(strpos($well, "-") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_kategori',$fKat)->where('id_tools',$fTool)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_kategori',$fKat)->where('id_tools',$fTool)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else{
                        $photo = Upload::orderBy('waktu','DESC')->where('id_kategori',$fKat)->where('id_tools',$fTool)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                }
                
            }
            
            if(empty($userid) && empty($category) && !empty($well) && !empty($tool)){
                $page = $page * 2;$skip = -2 + $page;$take = 2;
                if($kosong == 1){
                    if(strpos($well, "-") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_tools',$fTool)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_tools',$fTool)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else{
                        $getd = Upload::orderBy('waktu','DESC')->where('id_tools',$fTool)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    if(strpos($well, "-") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_tools',$fTool)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else{
                        $photo = Upload::orderBy('waktu','DESC')->where('id_tools',$fTool)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                }
                else{
                    if(strpos($well, "-") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_tools',$fTool)->whereBetween('waktu',[$t2,$t1])->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_tools',$fTool)->whereBetween('waktu',[$t2,$t1])->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else{
                        $getd = Upload::orderBy('waktu','DESC')->where('id_tools',$fTool)->whereBetween('waktu',[$t2,$t1])->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    if(strpos($well, "-") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_tools',$fTool)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else{
                        $photo = Upload::orderBy('waktu','DESC')->where('id_tools',$fTool)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                }
                
            }
            
            if(empty($userid) && empty($category) && empty($well) && !empty($tool)){
                $page = $page * 2;$skip = -2 + $page;$take = 2;
                if($kosong == 1){
                    $getd = Upload::orderBy('waktu','DESC')->where('id_tools',$fTool)->distinct()->skip($skip)->take($take)->get(['waktu']);
                    
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    $photo = Upload::orderBy('waktu','DESC')->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where('id_tools',$fTool)->select(['id_upload', 'foto','waktu'])->get();
                    
                    
                }
                else{
                    $getd = Upload::orderBy('waktu','DESC')->whereBetween('waktu',[$t2,$t1])->where('id_tools',$fTool)->distinct()->skip($skip)->take($take)->get(['waktu']);
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    $photo = Upload::orderBy('waktu','DESC')->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where('id_tools',$fTool)->select(['id_upload', 'foto','waktu'])->skip($skip)->take($take)->get();
                }
                
            }
            
            if(!empty($userid) && empty($category) && !empty($well) && !empty($tool)){
                $page = $page * 2;$skip = -2 + $page;$take = 2;
                if($kosong == 1){
                    if(strpos($well, "-") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->where('id_tools',$fTool)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->where('id_tools',$fTool)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else{
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->where('id_tools',$fTool)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    if(strpos($well, "-") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->where('id_tools',$fTool)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->where('id_tools',$fTool)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else{
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->where('id_tools',$fTool)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                }
                else{
                    if(strpos($well, "-") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->where('id_tools',$fTool)->whereBetween('waktu',[$t2,$t1])->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->where('id_tools',$fTool)->whereBetween('waktu',[$t2,$t1])->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else{
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->where('id_tools',$fTool)->whereBetween('waktu',[$t2,$t1])->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    if(strpos($well, "-") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->where('id_tools',$fTool)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->where('id_tools',$fTool)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else{
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->where('id_tools',$fTool)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                }
                
            }
            
            if(!empty($userid) && empty($category) && empty($well) && !empty($tool)){
                $page = $page * 2;$skip = -2 + $page;$take = 2;
                if($kosong == 1){
                    $getd = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->where('id_tools',$fTool)->distinct()->skip($skip)->take($take)->get(['waktu']);
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    $photo = Upload::orderBy('waktu','DESC')->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where('id_user',$fUser)->where('id_tools',$fTool)->select(['id_upload', 'foto','waktu'])->get();
                }
                else{
                    $getd = Upload::orderBy('waktu','DESC')->whereBetween('waktu',[$t2,$t1])->where('id_user',$fUser)->where('id_tools',$fTool)->distinct()->skip($skip)->take($take)->get(['waktu']);
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    $photo = Upload::orderBy('waktu','DESC')->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where('id_user',$fUser)->where('id_tools',$fTool)->select(['id_upload', 'foto','waktu'])->skip($skip)->take($take)->get();
                }
                
            }
            
            if(!empty($userid) && empty($category) && !empty($well) && empty($tool)){
                $page = $page * 2;$skip = -2 + $page;$take = 2;
                if($kosong == 1){
                    if(strpos($well, "-") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else{
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    if(strpos($well, "-") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else{
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                }
                else{
                    if(strpos($well, "-") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->whereBetween('waktu',[$t2,$t1])->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->whereBetween('waktu',[$t2,$t1])->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else{
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->whereBetween('waktu',[$t2,$t1])->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    if(strpos($well, "-") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else{
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$fUser)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                }
                
            }
            
            if(empty($userid) && !empty($category) && !empty($well) && empty($tool)){
                $page = $page * 2;$skip = -2 + $page;$take = 2;
                if($kosong == 1){
                    if(strpos($well, "-") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_kategori',$fKat)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_kategori',$fKat)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else{
                        $getd = Upload::orderBy('waktu','DESC')->where('id_kategori',$fKat)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    if(strpos($well, "-") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_kategori',$fKat)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_kategori',$fKat)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else{
                        $photo = Upload::orderBy('waktu','DESC')->where('id_kategori',$fKat)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                }
                else{
                    if(strpos($well, "-") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_kategori',$fKat)->whereBetween('waktu',[$t2,$t1])->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_kategori',$fKat)->whereBetween('waktu',[$t2,$t1])->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else{
                        $getd = Upload::orderBy('waktu','DESC')->where('id_kategori',$fKat)->whereBetween('waktu',[$t2,$t1])->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    if(strpos($well, "-") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_kategori',$fKat)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_kategori',$fKat)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else{
                        $photo = Upload::orderBy('waktu','DESC')->where('id_kategori',$fKat)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                }
                
            }
            
            if(empty($userid) && !empty($category) && empty($well) && empty($tool)){
                $page = $page * 2;$skip = -2 + $page;$take = 2;
                if($kosong == 1){
                    $getd = Upload::orderBy('waktu','DESC')->where('id_kategori',$fKat)->distinct()->skip($skip)->take($take)->get(['waktu']);
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    $photo = Upload::orderBy('waktu','DESC')->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where('id_kategori',$fKat)->select(['id_upload', 'foto','waktu'])->get();
                }
                else{
                    $getd = Upload::orderBy('waktu','DESC')->whereBetween('waktu',[$t2,$t1])->where('id_kategori',$fKat)->distinct()->skip($skip)->take($take)->get(['waktu']);
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    $photo = Upload::orderBy('waktu','DESC')->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where('id_kategori',$fKat)->select(['id_upload', 'foto','waktu'])->skip($skip)->take($take)->get();
                }
                
            }
            
            if(empty($userid) && empty($category) && !empty($well) && empty($tool)){
                $page = $page * 2;$skip = -2 + $page;$take = 2;
                if($kosong == 1){
                    
                    if(strpos($well, "-") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else{
                        $getd = Upload::orderBy('waktu','DESC')->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    
                    if(strpos($well, "-") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else{
                        $photo = Upload::orderBy('waktu','DESC')->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                }
                else{
                    if(strpos($well, "-") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->whereBetween('waktu',[$t2,$t1])->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->whereBetween('waktu',[$t2,$t1])->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else{
                        $getd = Upload::orderBy('waktu','DESC')->whereBetween('waktu',[$t2,$t1])->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    if(strpos($well, "-") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else{
                        $photo = Upload::orderBy('waktu','DESC')->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->select(['id_upload', 'foto','waktu'])->get();
                    }                
                    
                }
                
            }
            
        }
        else{  
            if(!empty($search)){
                $page = $page * 2;$skip = -2 + $page;$take = 2;
                if($kosong == 1){
                    
                    $getd = Upload::orderBy('waktu','DESC')->Orwhere('id_user',$sU_id)->Orwhere('id_kategori',$sK_id)->Orwhere('id_tools',$sT_id)->Orwhere('id_sumur',$well_name)->Orwhere('no_sumur',$well_id)->distinct()->skip($skip)->take($take)->get(['waktu']);
                    
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    $photo = Upload::orderBy('waktu','DESC')->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($sK_id,$sT_id,$sU_id,$well_id,$well_name) {$query1->Orwhere('id_user',$sU_id)->Orwhere('id_kategori',$sK_id)->Orwhere('id_tools',$sT_id)->Orwhere('id_sumur',$well_name)->Orwhere('no_sumur',$well_id);})->select(['id_upload', 'foto','waktu'])->get();
                }
                else{
                    
                    $getd = Upload::orderBy('waktu','DESC')->Orwhere('id_user',$sU_id)->Orwhere('id_kategori',$sK_id)->Orwhere('id_tools',$sT_id)->Orwhere('id_sumur',$well_name)->Orwhere('no_sumur',$well_id)->whereBetween('waktu',[$t2,$t1])->distinct()->skip($skip)->take($take)->get(['waktu']);
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    $photo = Upload::orderBy('waktu','DESC')->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($sK_id,$sT_id,$sU_id,$well_id,$well_name) {$query1->Orwhere('id_user',$sU_id)->Orwhere('id_kategori',$sK_id)->Orwhere('id_tools',$sT_id)->Orwhere('id_sumur',$well_name)->Orwhere('no_sumur',$well_id);})->Orwhere('id_user',$sU_id)->Orwhere('id_kategori',$sK_id)->Orwhere('id_tools',$sT_id)->Orwhere('id_sumur',$well_id)->Orwhere('no_sumur',$well_name)->select(['id_upload', 'foto','waktu'])->get();
                }
                
            }
            
            if(empty($userid) && empty($category) && empty($well) && empty($tool)){
                
                $page = $page * 2;$skip = -2 + $page;$take = 2;
                if($kosong == 1){
                    $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->distinct()->skip($skip)->take($take)->get(['waktu']);
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->select(['id_upload', 'foto','waktu'])->get();
                }
                else{
                    
                    $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->whereBetween('waktu',[$t2,$t1])->distinct()->skip($skip)->take($take)->get(['waktu']);
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    
                    $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->select(['id_upload', 'foto','waktu'])->get();
                }
                
            }
            
            if(!empty($userid) && empty($category) && empty($well) && empty($tool)){
                $page = $page * 2;$skip = -2 + $page;$take = 2;
                if($kosong == 1){
                    $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->distinct()->skip($skip)->take($take)->get(['waktu']);
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where('id_user',$fUser)->select(['id_upload', 'foto','waktu'])->get();
                }
                else{
                    $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->whereBetween('waktu',[$t2,$t1])->where('id_user',$fUser)->distinct()->skip($skip)->take($take)->get(['waktu']);
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where('id_user',$fUser)->select(['id_upload', 'foto','waktu'])->skip($skip)->take($take)->get();
                }
                
            }
            
            if(!empty($userid) && !empty($category) && empty($well) && empty($tool)){
                $page = $page * 2;$skip = -2 + $page;$take = 2;
                if($kosong == 1){
                    $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->where('id_kategori',$fKat)->distinct()->skip($skip)->take($take)->get(['waktu']);
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where('id_user',$fUser)->where('id_kategori',$fKat)->select(['id_upload', 'foto','waktu'])->get();
                }
                else{
                    $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->whereBetween('waktu',[$t2,$t1])->where('id_user',$fUser)->where('id_kategori',$fKat)->distinct()->skip($skip)->take($take)->get(['waktu']);
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where('id_user',$fUser)->where('id_kategori',$fKat)->select(['id_upload', 'foto','waktu'])->skip($skip)->take($take)->get();
                }
            }
            
            if(!empty($userid) && !empty($category) && !empty($well) && empty($tool)){
                $page = $page * 2;$skip = -2 + $page;$take = 2;
                if($kosong == 1){
                    if(strpos($well, "-") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else{
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    if(strpos($well, "-") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else{
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->select(['id_upload', 'foto','waktu'])->get();
                    }                
                    
                }
                else{
                    if(strpos($well, "-") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->whereBetween('waktu',[$t2,$t1])->where('id_user',$fUser)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->whereBetween('waktu',[$t2,$t1])->where('id_user',$fUser)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else{
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->whereBetween('waktu',[$t2,$t1])->where('id_user',$fUser)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    if(strpos($well, "-") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else{
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->select(['id_upload', 'foto','waktu'])->get();
                    }  
                }
                
            }
            
            if(!empty($userid) && !empty($category) && !empty($well) && !empty($tool)){
                $page = $page * 2;$skip = -2 + $page;$take = 2;
                if($kosong == 1){
                    if(strpos($well, "-") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->where('id_kategori',$fKat)->where('id_tools',$fTool)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->where('id_kategori',$fKat)->where('id_tools',$fTool)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else{
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->where('id_kategori',$fKat)->where('id_tools',$fTool)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    if(strpos($well, "-") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->where('id_kategori',$fKat)->where('id_tools',$fTool)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->where('id_kategori',$fKat)->where('id_tools',$fTool)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else{
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->where('id_kategori',$fKat)->where('id_tools',$fTool)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                }
                else{
                    if(strpos($well, "-") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->where('id_kategori',$fKat)->where('id_tools',$fTool)->whereBetween('waktu',[$t2,$t1])->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->where('id_kategori',$fKat)->where('id_tools',$fTool)->whereBetween('waktu',[$t2,$t1])->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else{
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->where('id_kategori',$fKat)->where('id_tools',$fTool)->whereBetween('waktu',[$t2,$t1])->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    if(strpos($well, "-") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->where('id_kategori',$fKat)->where('id_tools',$fTool)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->where('id_kategori',$fKat)->where('id_tools',$fTool)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else{
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->where('id_kategori',$fKat)->where('id_tools',$fTool)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                }
                
            }
            
            if(empty($userid) && !empty($category) && !empty($well) && !empty($tool)){
                $page = $page * 2;$skip = -2 + $page;$take = 2;
                if($kosong == 1){
                    if(strpos($well, "-") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_kategori',$fKat)->where('id_tools',$fTool)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_kategori',$fKat)->where('id_tools',$fTool)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else{
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_kategori',$fKat)->where('id_tools',$fTool)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    if(strpos($well, "-") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_kategori',$fKat)->where('id_tools',$fTool)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_kategori',$fKat)->where('id_tools',$fTool)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else{
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_kategori',$fKat)->where('id_tools',$fTool)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                }
                else{
                    if(strpos($well, "-") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_kategori',$fKat)->where('id_tools',$fTool)->whereBetween('waktu',[$t2,$t1])->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_kategori',$fKat)->where('id_tools',$fTool)->whereBetween('waktu',[$t2,$t1])->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else{
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_kategori',$fKat)->where('id_tools',$fTool)->whereBetween('waktu',[$t2,$t1])->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    if(strpos($well, "-") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_kategori',$fKat)->where('id_tools',$fTool)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_kategori',$fKat)->where('id_tools',$fTool)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else{
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_kategori',$fKat)->where('id_tools',$fTool)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                }
                
            }
            
            if(empty($userid) && empty($category) && !empty($well) && !empty($tool)){
                $page = $page * 2;$skip = -2 + $page;$take = 2;
                if($kosong == 1){
                    if(strpos($well, "-") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_tools',$fTool)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_tools',$fTool)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else{
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_tools',$fTool)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    if(strpos($well, "-") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$user)->where('id_tools',$fTool)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else{
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_tools',$fTool)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                }
                else{
                    if(strpos($well, "-") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_tools',$fTool)->whereBetween('waktu',[$t2,$t1])->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_tools',$fTool)->whereBetween('waktu',[$t2,$t1])->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else{
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_tools',$fTool)->whereBetween('waktu',[$t2,$t1])->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    if(strpos($well, "-") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_tools',$fTool)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else{
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_tools',$fTool)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                }
                
            }
            
            if(empty($userid) && empty($category) && empty($well) && !empty($tool)){
                $page = $page * 2;$skip = -2 + $page;$take = 2;
                if($kosong == 1){
                    $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_tools',$fTool)->distinct()->skip($skip)->take($take)->get(['waktu']);
                    
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where('id_tools',$fTool)->select(['id_upload', 'foto','waktu'])->get();
                    
                    
                }
                else{
                    $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->whereBetween('waktu',[$t2,$t1])->where('id_tools',$fTool)->distinct()->skip($skip)->take($take)->get(['waktu']);
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where('id_tools',$fTool)->select(['id_upload', 'foto','waktu'])->skip($skip)->take($take)->get();
                }
                
            }
            
            if(!empty($userid) && empty($category) && !empty($well) && !empty($tool)){
                $page = $page * 2;$skip = -2 + $page;$take = 2;
                if($kosong == 1){
                    if(strpos($well, "-") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->where('id_tools',$fTool)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->where('id_tools',$fTool)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else{
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->where('id_tools',$fTool)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    if(strpos($well, "-") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->where('id_tools',$fTool)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->where('id_tools',$fTool)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else{
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->where('id_tools',$fTool)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                }
                else{
                    if(strpos($well, "-") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->where('id_tools',$fTool)->whereBetween('waktu',[$t2,$t1])->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->where('id_tools',$fTool)->whereBetween('waktu',[$t2,$t1])->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else{
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->where('id_tools',$fTool)->whereBetween('waktu',[$t2,$t1])->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    if(strpos($well, "-") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->where('id_tools',$fTool)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->where('id_tools',$fTool)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else{
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->where('id_tools',$fTool)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                }
                
            }
            
            if(!empty($userid) && empty($category) && empty($well) && !empty($tool)){
                $page = $page * 2;$skip = -2 + $page;$take = 2;
                if($kosong == 1){
                    $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->where('id_tools',$fTool)->distinct()->skip($skip)->take($take)->get(['waktu']);
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where('id_user',$fUser)->where('id_tools',$fTool)->select(['id_upload', 'foto','waktu'])->get();
                }
                else{
                    $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->whereBetween('waktu',[$t2,$t1])->where('id_user',$fUser)->where('id_tools',$fTool)->distinct()->skip($skip)->take($take)->get(['waktu']);
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where('id_user',$fUser)->where('id_tools',$fTool)->select(['id_upload', 'foto','waktu'])->skip($skip)->take($take)->get();
                }
                
            }
            
            if(!empty($userid) && empty($category) && !empty($well) && empty($tool)){
                $page = $page * 2;$skip = -2 + $page;$take = 2;
                if($kosong == 1){
                    if(strpos($well, "-") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else{
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    if(strpos($well, "-") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else{
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                }
                else{
                    if(strpos($well, "-") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->whereBetween('waktu',[$t2,$t1])->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->whereBetween('waktu',[$t2,$t1])->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else{
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->whereBetween('waktu',[$t2,$t1])->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    if(strpos($well, "-") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else{
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_user',$fUser)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                }
                
            }
            
            if(empty($userid) && !empty($category) && !empty($well) && empty($tool)){
                $page = $page * 2;$skip = -2 + $page;$take = 2;
                if($kosong == 1){
                    if(strpos($well, "-") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_kategori',$fKat)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_kategori',$fKat)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else{
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_kategori',$fKat)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    if(strpos($well, "-") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_kategori',$fKat)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_kategori',$fKat)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else{
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_kategori',$fKat)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                }
                else{
                    if(strpos($well, "-") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_kategori',$fKat)->whereBetween('waktu',[$t2,$t1])->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_kategori',$fKat)->whereBetween('waktu',[$t2,$t1])->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else{
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_kategori',$fKat)->whereBetween('waktu',[$t2,$t1])->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    if(strpos($well, "-") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_kategori',$fKat)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_kategori',$fKat)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else{
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_kategori',$fKat)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                }
                
            }
            
            if(empty($userid) && !empty($category) && empty($well) && empty($tool)){
                $page = $page * 2;$skip = -2 + $page;$take = 2;
                if($kosong == 1){
                    $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where('id_kategori',$fKat)->distinct()->skip($skip)->take($take)->get(['waktu']);
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where('id_kategori',$fKat)->select(['id_upload', 'foto','waktu'])->get();
                }
                else{
                    $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->whereBetween('waktu',[$t2,$t1])->where('id_kategori',$fKat)->distinct()->skip($skip)->take($take)->get(['waktu']);
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where('id_kategori',$fKat)->select(['id_upload', 'foto','waktu'])->skip($skip)->take($take)->get();
                }
                
            }
            
            if(empty($userid) && empty($category) && !empty($well) && empty($tool)){
                $page = $page * 2;$skip = -2 + $page;$take = 2;
                if($kosong == 1){
                    
                    if(strpos($well, "-") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else{
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    
                    if(strpos($well, "-") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else{
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                }
                else{
                    if(strpos($well, "-") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->whereBetween('waktu',[$t2,$t1])->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->whereBetween('waktu',[$t2,$t1])->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    else{
                        $getd = Upload::orderBy('waktu','DESC')->where('id_user',$user)->whereBetween('waktu',[$t2,$t1])->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->distinct()->skip($skip)->take($take)->get(['waktu']);
                    }
                    $c= count($getd);
                    if($c == "2"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = $getd[1]->waktu;
                    
                    }
                    else if($c == "1"){
                        
                        $t1 = $getd[0]->waktu;
                        $t2 = 0;
           
                    }
                    else if($c == "0"){
                        $t1=0;
                        $t2=0;
                        
                    }
                    if(strpos($well, "-") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id)->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else if(strpos($well, "0") !== false || strpos($well, "1") !== false || strpos($well, "2") !== false || strpos($well, "3") !== false || strpos($well, "4") !== false){
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->Where('no_sumur',$fWell_nama);})->select(['id_upload', 'foto','waktu'])->get();
                    }
                    else{
                        $photo = Upload::orderBy('waktu','DESC')->where('id_user',$user)->where(function($query) use ($t2,$t1) {$query->where('waktu',$t2)->orWhere('waktu',$t1);})->where(function($query1) use ($fWell_id,$fWell_nama) {$query1->where('id_sumur',$fWell_id);})->select(['id_upload', 'foto','waktu'])->get();
                    }                
                    
                }
                
            }
            
        }
        
        
        //END
        
        //CREATE JSON
        $collection = collect();
        foreach ($photo as $invoice) {
            $collection->push([
                'id' => $invoice->id_upload,
                'photo' => $invoice->foto,
                'date' => $invoice->waktu,
            ]);
        }
            
        $fotos = [];
        $i=-1;
        $j=0;
        $date0="";
        foreach ($collection as $key => $value) {
            if($date0 != $value['date']){
                $i++;
                $fotos[$i]['date'] = $value['date'];
                $date0=$value['date'];
                $j=0;
                    
            }
                
            if($date0 == $value['date']){
                    
                $fotos[$i]['photos'][$j]['id'] = $value['id'];
                $fotos[$i]['photos'][$j]['picture'] = $value['photo'];
                $j++;
            }
            $date0=$value['date'];
        }
        //END
        
        
    	return response()->json(
    	   [
    	        "code" => 200,
                "status" => "OK",
                "data" => $fotos
    	   ]
    	);
    }
    
    public function detail($id,Request $request)
    {
        
    	$photo = Upload::where('id_upload',$id)->join('sumur','uploadan.id_sumur','=','sumur.id_sumur')->join('tools','uploadan.id_tools','=','tools.id_tools')
    	->join('kategori','uploadan.id_kategori','=','kategori.id_kategori')->join('user','uploadan.id_user','=','user.id_user')
    	->select('uploadan.foto as picture','kategori.kategori as category', 'sumur.nama_sumur as well', 'tools.tools as tool','user.nama as uploader', 'uploadan.keterangan as description', 'uploadan.waktu as date', 'uploadan.time as time', 'uploadan.created_at as createdAt', 'uploadan.updated_at as updatedAt', 'uploadan.id_upload as id')->first();
    	
    	$photo1 = Upload::where('id_upload',$id)->join('sumur','uploadan.id_sumur','=','sumur.id_sumur')->join('tools','uploadan.id_tools','=','tools.id_tools')
    	->join('kategori','uploadan.id_kategori','=','kategori.id_kategori')->join('user','uploadan.id_user','=','user.id_user')
    	->select('uploadan.no_sumur as no')->first();
    	
    	
    	$photo->well = $photo->well."-".$photo1->no;
    	$eks = explode(":",$photo->time);
    	$photo->time = $eks[0].":".$eks[1];
    	$photo->id = (int)$photo->id;
    	if(empty($photo->updatedAt)){
    	    $photo->updatedAt = $photo->createdAt;
    	}
    	return response()->json(
    	   [
    	        "code" => 200,
                "status" => "OK",
                "data" => $photo
    	   ]
    	);
    	
    }
    
    public function login(Request $request)
    {
    	$user = $request->username;
    	$password = md5($request->password);
    	
    	$login = User::where('username',$user)->where('password',$password)->select('id_user as id','nama as name')->first();
    	
    	if(empty($login)){
    	    $status = "NO";
    	    $code = 404;
    	}
    	else{
    	    $status = "OK";
    	    $code = 200;
    	}
    	
    	
    	return response()->json(
    	   [
    	        "code" => $code,
                "status" => $status,
                "data" => $login
    	   ]
    	);
    	
    }
    
    public function spinner(Request $request)
    {
        
        $tools = Tools::select('id_tools as id','tools as tool')->get();
        $well = Well::select('id_sumur as id','nama_sumur as well')->get();
        $user = User::select('id_user as id','nama as name')->get();
        $kategori = Kategori::select('id_kategori as id','kategori as category')->get();
    	
    	$spinner = [];
    	$spinner['users'] = $user;
    	$spinner['categories'] = $kategori;
    	$spinner['wells'] = $well;
    	$spinner['tools'] = $tools;
    	
    	
    	
    	
    	return response()->json(
    	   [
    	        "code" => 200,
                "status" => "OK",
                "data" => $spinner
    	   ]
    	);
    	
    }
    
    public function upload(Request $request)
    {
        if(!empty($_POST['userId'])){
            $userid = $_POST['userId'];
        }
        else{
            $userid;
        }
        
        
        if(!empty($_POST['category'])){
            $kaatt = $_POST['category'];
        }
        else{
            $kaatt="";
        }
        
        if(!empty($_POST['well'])){
            $well = $_POST['well'];
        }
        else{
            $well="";
        }
        
        if(!empty($_POST['tool'])){
        $tool = $_POST['tool'];
        }
        else{
            $tool="";
        }
            
                    $kat = Kategori::where('kategori','=',$kaatt)->first();
                    
                    if(!empty($kat)){
                        $id_kategori = $kat->id_kategori;
                    }
                    else{
                        $id_kategori = "";
                    }
                    
                    $tol = Tools::where('tools',$tool)->first();
                    if(!empty($tol)){
                        $id_tools = $tol->id_tools;
                    }else{
                        $id_tools = "";
                    }
                    
                    $exp = explode("-",$well);
                    $well_name = $exp[0];
                    $well_id = $exp[1];
                    
                    
                    $wel = Well::where('nama_sumur',$well_name)->first();
                    if(!empty($wel)){
                        $id_well = $wel->id_sumur;
                    }else{
                        $id_well = "";
                    }
  
        if ($files = $request->file('picture')) {
             
            
            $image = $request->file('picture');
            $imagename = time().'.'.$image->guessExtension();
            
            $image->move('img',$imagename);
    
            $up = new Upload();
            $up->foto ="https://wellhistorydoc.com/img/" .$imagename;
            $up->id_user = $userid;
            $up->id_sumur = $id_well;
            $up->no_sumur = $well_id;
            $up->id_tools = $id_tools;
            $up->id_kategori = $id_kategori;
            $up->keterangan = $request->description;
            $up->waktu = $request->date;
            $up->time = $request->time;
            $up->save();
            
            
            $tt = $request->date;
            $aaa = Tanggal::where('date',$tt)->first();
            if(empty($aaa)){
                $w = new Tanggal();
                $w->date = $request->date;
                $w->save();
            }
            
            $id_up = $up->id_upload;
            $photo = Upload::where('id_upload',$id_up)->join('sumur','uploadan.id_sumur','=','sumur.id_sumur')->join('tools','uploadan.id_tools','=','tools.id_tools')
    	    ->join('kategori','uploadan.id_kategori','=','kategori.id_kategori')->join('user','uploadan.id_user','=','user.id_user')->select('uploadan.foto as picture','kategori.kategori as category', 'sumur.nama_sumur as well', 'tools.tools as tool','user.nama as uploader', 'uploadan.keterangan as description', 'uploadan.waktu as date', 'uploadan.time as time', 'uploadan.created_at as createdAt', 'uploadan.updated_at as updatedAt', 'uploadan.id_upload as id')->first();
    	    
            
        
        }
    	return response()->json(
    	   [
    	        "code" => 201,
                "status" => "OK",
                "data" => $photo
    	   ]
    	);
    	
    }
    
    public function update(Request $request)
    {     
        $id = $request->id;
        $up = Upload::where('id_upload',$id)->first();
            
            $kaatt = $request->category;
            $well = $request->well;
            $tool = $request->tool;
        
            
                    $kat = Kategori::where('kategori','=',$kaatt)->first();
                    
                    if(!empty($kat)){
                        $id_kategori = $kat->id_kategori;
                    }
                    else{
                        $id_kategori = "";
                    }
                    
                    $tol = Tools::where('tools',$tool)->first();
                    if(!empty($tol)){
                        $id_tools = $tol->id_tools;
                    }else{
                        $id_tools = "";
                    }
                    
                    $exp = explode("-",$well);
                    $well_name = $exp[0];
                    $well_id = $exp[1];
                    
                    
                    $wel = Well::where('nama_sumur',$well_name)->first();
                    if(!empty($wel)){
                        $id_well = $wel->id_sumur;
                    }else{
                        $id_well = "";
                    }
  
        if ($files = $request->file('picture')) {
             
            
            $image = $request->file('picture');
            $imagename = time().'.'.$image->guessExtension();
            
            $image->move('img',$imagename);
            
            $up->foto ="https://wellhistorydoc.com/img/" .$imagename;
        }
            $up->id_user = $request->userId;
            $up->id_sumur = $id_well;
            $up->no_sumur = $well_id;
            $up->id_tools = $id_tools;
            $up->id_kategori = $id_kategori;
            $up->keterangan = $request->description;
            $up->waktu = $request->date;
            $up->time = $request->time;
            $up->save();
            
            $id_up = $up->id_upload;
            $photo = Upload::where('id_upload',$id_up)->join('sumur','uploadan.id_sumur','=','sumur.id_sumur')->join('tools','uploadan.id_tools','=','tools.id_tools')
    	    ->join('kategori','uploadan.id_kategori','=','kategori.id_kategori')->join('user','uploadan.id_user','=','user.id_user')->select('uploadan.foto as picture','kategori.kategori as category', 'sumur.nama_sumur as well', 'tools.tools as tool','user.nama as uploader', 'uploadan.keterangan as description', 'uploadan.waktu as date', 'uploadan.time as time', 'uploadan.created_at as createdAt', 'uploadan.updated_at as updatedAt', 'uploadan.id_upload as id')->first();
    	    
            
            
        
        
    	return response()->json(
    	   [
    	        "code" => 204,
                "status" => "OK",
                "data" => $photo
    	   ]
    	);
    	
    }
    
    public function delete($id, Request $request)
    {     
            $up = Upload::where('id_upload',$id)->first();
            
            $up->delete();
        
    	
    	return response()->json(
    	   [
    	        "code" => 200,
                "status" => "OK"
    	   ]
    	);
    	
    }
    
    public function excel(Request $request)
    {     
        
        // Excel::store(new Uploadan(2018), 'invoices.xlsx', 'real_public');
        // $path = "https://wellhistorydoc.com/invoices.xlsx";
        $aa = $_GET['isWithPicture'];
        if($aa == "true"){
            $path = "https://web.wellhistorydoc.com/akudata.php";
        }
        else{
            $path = "https://web.wellhistorydoc.com/kamudata.php";
        }
        
        return response()->json(
    	   [
    	        "code" => 200,
                "status" => "OK",
                "data" => $path
    	   ]
    	);
    }

   
}

