<script lang="ts">
    import ErrorIcon from '@/assets/icons/error.svg?component';

    export let label = "NO_LABEL";
    export let Icon = ErrorIcon;
    export let color = "#000000";
</script>

<template>
    <div class="flex flex-col items-center">
        <Icon class="h-[22.5px] w-[22.5px] mb-0.5 sm:hidden" fill={color}/>
        <span class="font-bold">{label}</span>
    </div>
</template>