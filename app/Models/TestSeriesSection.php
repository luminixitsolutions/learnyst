<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestSeriesSection extends Model
{
    protected $fillable = ['test_series_id', 'title', 'sort_order'];

    public function testSeries()
    {
        return $this->belongsTo(TestSeries::class);
    }
}
