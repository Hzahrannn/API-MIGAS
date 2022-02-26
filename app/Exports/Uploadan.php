<?php
 
namespace App\Exports;
 
use App\Tools;
use App\Well;
use App\Upload;
use App\Kategori;
use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
 
class Uploadan implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Upload::join('sumur','uploadan.id_sumur','=','sumur.id_sumur')->join('tools','uploadan.id_tools','=','tools.id_tools')->join('kategori','uploadan.id_kategori','=','kategori.id_kategori')->join('user','uploadan.id_user','=','user.id_user')->select('uploadan.*', 'sumur.*','tools.tools','user.nama')->get();
    }
}