<script lang="ts">

    export let label = "NO_LABEL";
    export let icon = "fa-triangle-exclamation";
</script>

<template>
    <div class="flex flex-col items-center">
        <div class="mb-0.5 sm:hidden"><i class="fa-solid {icon} fa-lg"></i></div>
        <span class="font-bold">{label}</span>
    </div>
</template>