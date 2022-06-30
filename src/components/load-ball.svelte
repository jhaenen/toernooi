<script lang="ts">
    import ball from "@/assets/basketball.svg";

    export let error = false;
</script>

<template>
    <div class="flex flex-col items-center justify-end h-[75px] w-fit">
        {#if !error}
            <img class="animate-bounce h-10 w-10" src={ball} alt=""/>
            <span>Laden...</span>
        {:else}
            <div class="mb-3"><i class="fa-solid fa-triangle-exclamation fa-2xl"></i></div>
            <span>Er is iets fout gegaan</span>
        {/if}
    </div>
</template>