<script lang="ts">
    export let label = "";
    export let checked = false;
</script>

<template>
    <div class="h-6">
        <label class="inline-flex relative items-center cursor-pointer">
            <input type="checkbox" class="sr-only peer" bind:checked={checked} on:click>
            <div class="w-11 h-6 bg-gray-200 rounded-full peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
            {#if label != ""}
                <span class="ml-3 text-sm font-medium">Details</span>
            {/if}
        </label>
    </div>
</template>