<script lang="ts">
    import { onMount } from "svelte";
    import type { Poule } from "@/types";

    import Loader from "@/view/load-ball.svelte";

    let poules: Array<Poule> = [];

    onMount(async () => {
        const response = await fetch("http://localhost:4000/g_poules.php");
        const data = await response.json();
        poules = data;
        console.log(poules);
    });
</script>

<template>
    <div class="flex flex-col items-center sm:items-start sm:text-left">
        <h1 class="m-4 font-light text-5xl">Poules</h1>

        {#each poules as poule (poule.id)}
            <a class="m-4 text-2xl font-bold" href="#/poules/{poule.id}">{poule.name}</a>
        {:else}
            <div class="fixed top-[40%] sm:static"><Loader/></div>
        {/each}
    </div>
</template>