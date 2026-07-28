<?php

/**
 * Rebrand stored website content and settings from Learnyst to StudyNest.
 * Run: php database/seeders/rebrand_studynest.php
 */

require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\WebsiteContent;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

function rebrandValue(mixed $value): mixed
{
    if (is_string($value)) {
        return str_replace(
            ['Learnyst', 'learnyst.com', 'LEARNYST'],
            ['StudyNest', 'studynest.com', 'STUDYNEST'],
            $value
        );
    }

    if (is_array($value)) {
        $out = [];
        foreach ($value as $k => $v) {
            $out[$k] = rebrandValue($v);
        }

        return $out;
    }

    return $value;
}

$updatedRows = 0;
WebsiteContent::query()->each(function (WebsiteContent $row) use (&$updatedRows) {
    $before = json_encode($row->content);
    $afterContent = rebrandValue($row->content);
    $label = rebrandValue($row->label);
    if (json_encode($afterContent) !== $before || $label !== $row->label) {
        $row->content = $afterContent;
        $row->label = $label;
        $row->save();
        Cache::forget("website_content.{$row->key}");
        $updatedRows++;
        echo "Updated website_content: {$row->key}\n";
    }
});

// Platform / company settings JSON blobs if any
if (Schema::hasTable('settings')) {
    $settingsUpdated = 0;
    DB::table('settings')->orderBy('id')->each(function ($setting) use (&$settingsUpdated) {
        $changed = false;
        $data = [];
        foreach ((array) $setting as $key => $val) {
            if ($key === 'id') {
                continue;
            }
            $new = rebrandValue($val);
            if ($new !== $val) {
                $changed = true;
            }
            $data[$key] = $new;
        }
        if ($changed) {
            DB::table('settings')->where('id', $setting->id)->update($data);
            $settingsUpdated++;
        }
    });
    echo "Settings rows updated: {$settingsUpdated}\n";
}

// Demo login emails (optional consistency)
$emailMap = [
    'admin@learnyst.com' => 'admin@studynest.com',
    'subadmin@learnyst.com' => 'subadmin@studynest.com',
    'instructor@learnyst.com' => 'instructor@studynest.com',
    'learner@learnyst.com' => 'learner@studynest.com',
    'superadmin@learnyst.com' => 'superadmin@studynest.com',
    'luminix@learnyst.com' => 'luminix@studynest.com',
    'nova@learnyst.com' => 'nova@studynest.com',
    'apex@learnyst.com' => 'apex@studynest.com',
    'brightpath@learnyst.com' => 'brightpath@studynest.com',
    'skillforge@learnyst.com' => 'skillforge@studynest.com',
    'priya.sharma@learnyst.com' => 'priya.sharma@studynest.com',
    'arjun.mehta@learnyst.com' => 'arjun.mehta@studynest.com',
    'sneha.reddy@learnyst.com' => 'sneha.reddy@studynest.com',
    'vikram.patel@learnyst.com' => 'vikram.patel@studynest.com',
    'ananya.iyer@learnyst.com' => 'ananya.iyer@studynest.com',
];

$usersUpdated = 0;
foreach ($emailMap as $from => $to) {
    $n = User::where('email', $from)->update(['email' => $to]);
    $usersUpdated += $n;
}
echo "User emails updated: {$usersUpdated}\n";

// Company contact emails ending with @learnyst.com
$companiesUpdated = DB::table('companies')
    ->where('email', 'like', '%@learnyst.com')
    ->get();
foreach ($companiesUpdated as $company) {
    DB::table('companies')->where('id', $company->id)->update([
        'email' => str_replace('@learnyst.com', '@studynest.com', $company->email),
        'about' => rebrandValue($company->about),
        'tagline' => rebrandValue($company->tagline),
        'name' => rebrandValue($company->name),
    ]);
}
echo "Companies updated: ".$companiesUpdated->count()."\n";

echo "Website content rows updated: {$updatedRows}\n";
echo "Done. Brand is now StudyNest.\n";
