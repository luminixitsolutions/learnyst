<?php
require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$c = App\Models\Company::where('slug','luminix-it-solution')->first();
App\Models\Course::where('created_by',$c->owner_user_id)->orderBy('title')->get(['id','title','price','sale_price'])->each(fn($x)=>print($x->id.' | '.$x->title.' | price='.$x->price."\n"));
echo 'Bundles: '.App\Models\Bundle::where('created_by',$c->owner_user_id)->count()."\n";
