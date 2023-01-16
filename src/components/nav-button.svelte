<script lang="ts">
    import SVG from 'svelte-inline-svg';

    import ErrorIcon from '@/assets/icons/error.svg';

    export let label = "NO_LABEL";
    export let icon = ErrorIcon;
    export let color = "#000000";
</script>

<template>
    <div class="flex flex-col items-center">
        <SVG src={icon} class="h-[22.5px] w-[22.5px] mb-0.5 sm:hidden" fill={color}/>
        <span class="font-bold">{label}</span>
    </div>
</template>