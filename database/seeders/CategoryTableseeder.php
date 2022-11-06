<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\category;
class CategoryTableseeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categoryRecords = [
            ['id'=>1,'parent_id'=>0,'category_name'=>'Dry Wall Screws','category_url'=>'','category_image'=>'','description'=>'','category_discount'=>0,'meta_title'=>'Dry Wall Screws','meta_keyword'=>'Dry Wall Screws','meta_description'=>'Dry Wall Screws','status'=>1],
            ['id'=>2,'parent_id'=>0,'category_name'=>'Other Fasteners','category_url'=>'','category_image'=>'','description'=>'','category_discount'=>0,'meta_title'=>'Other Fasteners','meta_keyword'=>'Other Fasteners','meta_description'=>'Other Fasteners','status'=>1],
            ['id'=>3,'parent_id'=>0,'category_name'=>'Anchors & Rivets','category_url'=>'','category_image'=>'','description'=>'','category_discount'=>0,'meta_title'=>'Anchors & Rivets','meta_keyword'=>'Anchors & Rivets','meta_description'=>'Anchors & Rivets','status'=>1],
            ['id'=>4,'parent_id'=>0,'category_name'=>'Self Tapping Screws','category_url'=>'','category_image'=>'','description'=>'','category_discount'=>0,'meta_title'=>'Self Tapping Screws','meta_keyword'=>'Self Tapping Screws','meta_description'=>'Self Tapping Screws','status'=>1],

        ];
       
        Category::insert($categoryRecords);
    }
}
