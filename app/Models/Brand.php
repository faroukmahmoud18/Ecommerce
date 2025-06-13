<?php

namespace App\Models;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; // Added

class Brand extends Model
{
    use HasFactory; // Added
    protected $fillable=['title','slug','status'];

    // public static function getProductByBrand($id){
    //     return Product::where('brand_id',$id)->paginate(10);
    // }
    public function products(){
        return $this->hasMany('App\Models\Product','brand_id','id')->where('status','active');
    }
    public static function getProductByBrand($slug){
        return Brand::with(['products' => function ($query) {
            $query->withCount('variants');
        }])->where('slug',$slug)->first();
    }
}
