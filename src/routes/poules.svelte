<script lang="ts">
    // Framework
    import { onMount } from "svelte";

    // Types
    import type { Poule } from "@/types/types";

    // Components
    import Loader from "@/components/load-ball.svelte";

    let poules: Array<Poule> = [];

    let error = false;
    let loaded = false;

    onMount(async () => {
        const server = import.meta.env.VITE_SERVER_URL;

        // Get poules from server
        try {
            const response = await fetch(server + "poules");
            poules = await response.json();

            loaded = true;
        } catch (err) {
            error = true;
            console.error(err);
        }
    });
</script>

<template>
    <div class="flex flex-col items-center m-4 gap-y-8">
        <!-- Title -->
        <h1 class="my-2 font-light text-5xl sm:mt-8">Poules</h1>

        <!-- Poule list -->
        <div class="flex flex-col items-center w-fit gap-y-6">
            {#each poules as poule (poule.id)}
                <a class="relative border-2 p-3 rounded-lg border-slate-500 w-full text-center text-2xl font-bold" href="#/poules/{poule.id}">
                    {poule.name}
                    <div class="absolute w-full h-1 bottom-0 left-0 -z-10" style={"background-color: " + poule.color}></div>
                </a>
            {:else}
                <!-- Loader -->
                {#if !loaded} 
                    <Loader {error}/>
                {:else}
                    <div class="text-center">
                        <p>Er zijn nog geen poules aangemaakt.</p>
                    </div>
                {/if}
            {/each}
        </div>
    </div>
</template>