<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
    <x-stat-card title="Monthly Revenue" :value="'₹'.number_format($monthlyRevenue, 0)" trend="This month" :href="route('admin.insights.monthly-revenue')" />
    <x-stat-card title="Active Learners" :value="number_format($activeLearners)" trend="This month" :href="route('admin.insights.active-learners')" />
    <x-stat-card title="Conversions" :value="$conversions.'%'" trend="Lead to paid" :href="route('admin.insights.conversions')" />
    <x-stat-card title="Time Spent" :value="$timeSpent" trend="Avg engagement" :href="route('admin.insights.time-spent')" />
</div>
