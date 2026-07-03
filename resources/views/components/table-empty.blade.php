@props(['action' => null, 'actionLabel' => 'Create', 'colspan' => 1])

<tr>
    <td colspan="{{ $colspan }}" class="py-12">
        <x-empty-state title="No results found" description="Try adjusting your search or filters, or create a new record." :action="$action" :actionLabel="$actionLabel" />
    </td>
</tr>
