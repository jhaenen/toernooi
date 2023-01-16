<script lang="ts">
    import SVG from 'svelte-inline-svg';

    const ball = "/src/assets/basketball.svg";
    const ErrorIcon = '/src/assets/icons/error.svg';
    const NoWiFiIcon = '/src/assets/icons/wifi-slash.svg';

    export let error = false;
</script>

<template>
    <div class="flex flex-col items-center justify-end h-[75px] w-full fixed top-[40%] left-0">
        {#if !error}
            <img class="animate-bounce h-10 w-10" src={ball} alt=""/>
            <span>Laden...</span>
        {:else}
            {#if navigator.onLine}
                <SVG src={ErrorIcon} class="w-9 h-9 mb-3 fill-slate-600" alt=""/>
                <span>Er is iets fout gegaan</span>
            {:else}
                <SVG src={NoWiFiIcon} class="w-10 h-10 mb-3 fill-slate-600" alt=""/>
                <span>Geen internet verbinding</span>
            {/if}
        {/if}
    </div>
</template>