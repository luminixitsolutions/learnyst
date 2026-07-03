@props(['colspan' => 1])

<tr class="animate-pulse">
    @for($i = 0; $i < $colspan; $i++)
        <td class="py-4 px-4"><div class="h-4 bg-slate-100 rounded-lg w-full"></div></td>
    @endfor
</tr>
