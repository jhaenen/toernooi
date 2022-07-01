<script lang="ts">
    // Framework
    import { onMount } from "svelte";

    // Types
    import type { Poule } from "@/types";

    // Components
    import Loader from "@/components/load-ball.svelte";

    let poules: Array<Poule> = [];

    let error = false;

    onMount(async () => {
        const server = import.meta.env.VITE_SERVER_URL;

        // Get poules from server
        try {
            const response = await fetch(server + "g_poules.php");
            poules = await response.json();
        } catch (err) {
            error = true;
            console.error(err);
        }
    });
</script>

<template>
    <div class="flex flex-col items-center sm:items-start sm:text-left">
        <!-- Title -->
        <h1 class="m-4 font-light text-5xl">Poules</h1>

        <!-- Poule list -->
        {#each poules as poule (poule.id)}
            <a class="m-4 text-2xl font-bold" href="#/poules/{poule.id}">{poule.name}</a>
        {:else}
            <!-- Loader -->
            <div class="fixed top-[40%] sm:left-1/2"><Loader {error}/></div>
        {/each}
    </div>
</template>