<script lang="ts">
    import BallIcon from "@/assets/basketball.svg?component";
    import ErrorIcon from '@/assets/icons/error.svg?component';
    import NoWiFiIcon from '@/assets/icons/wifi-slash.svg?component';

    export let error = false;
</script>

<template>
    <div class="flex flex-col items-center justify-end h-[75px] w-full fixed top-[40%] left-0">
        {#if !error}
            <BallIcon class="animate-bounce h-10 w-10" alt=""/>
            <span>Laden...</span>
        {:else}
            {#if navigator.onLine}
                <ErrorIcon class="w-9 h-9 mb-3 fill-slate-600" alt=""/>
                <span>Er is iets fout gegaan</span>
            {:else}
                <NoWiFiIcon class="w-10 h-10 mb-3 fill-slate-600" alt=""/>
                <span>Geen internet verbinding</span>
            {/if}
        {/if}
    </div>
</template>