<?php
require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$c = App\Models\Company::where('slug','luminix-it-solution')->first();
$ownerId = (int) $c->owner_user_id;
echo "Owner: {$ownerId}\nCourses:\n";
App\Models\Course::where('created_by',$ownerId)->orderBy('title')->get(['id','title'])->each(fn($x)=>print("  {$x->id} | {$x->title}\n"));
echo "Instructors:\n";
App\Models\User::where('created_by',$ownerId)->whereHas('role',fn($q)=>$q->where('slug','instructor'))->get(['id','name','email'])->each(fn($x)=>print("  {$x->id} | {$x->name}\n"));
echo 'Batches: '.App\Models\Batch::whereIn('course_id', App\Models\Course::where('created_by',$ownerId)->pluck('id'))->count()."\n";
