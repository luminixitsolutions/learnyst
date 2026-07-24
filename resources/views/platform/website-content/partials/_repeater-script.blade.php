<script>
function websiteRepeater(initial) {
    return {
        items: Array.isArray(initial) && initial.length ? initial : [{}],
        add(row) { this.items.push(row || {}); },
        remove(index) { if (this.items.length > 1) this.items.splice(index, 1); }
    }
}
</script>
