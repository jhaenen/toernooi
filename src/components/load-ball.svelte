<script lang="ts">
    import ball from "@/assets/basketball.svg";
    import ErrorIcon from '@/assets/icons/error.svg';

    export let error = false;
</script>

<template>
    <div class="flex flex-col items-center justify-end h-[75px] w-full fixed top-[40%] left-0">
        {#if !error}
            <img class="animate-bounce h-10 w-10" src={ball} alt=""/>
            <span>Laden...</span>
        {:else}
            <img src={ErrorIcon} class="w-8 h-8 mb-3" alt=""/>
            <span>Er is iets fout gegaan</span>
        {/if}
    </div>
</template>