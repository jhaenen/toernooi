<script lang="ts">
    // Framework
    import { onMount } from "svelte";

    // Types
    import type { Poule } from "@/types/types";

    // Components
    import Loader from "@/components/load-ball.svelte";

    let poules: Array<Poule> = [];

    let error = false;

    onMount(async () => {
        const server = import.meta.env.VITE_SERVER_URL;

        // Get poules from server
        try {
            const response = await fetch(server + "poules");
            poules = await response.json();
        } catch (err) {
            error = true;
            console.error(err);
        }
    });
</script>

<template>
    <div class="flex flex-col items-center m-4 gap-y-8">
        <!-- Title -->
        <h1 class="my-2 font-light text-5xl">Poules</h1>

        <!-- Poule list -->
        {#each poules as poule (poule.id)}
            <a class="text-2xl font-bold" href="#/poules/{poule.id}">{poule.name}</a>
        {:else}
            <!-- Loader -->
            <Loader {error}/>
        {/each}
    </div>
</template>