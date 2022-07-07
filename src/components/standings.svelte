<script lang="ts">
    import { onMount } from "svelte";
    import type { Stats } from "@/types/types";

    import Toggle from "@/components/toggle.svelte";

    export let standings: Array<Stats> = [];

    let details = false;

    $: details, localStorage.setItem("details", details.toString());
    
    {
        const localDetails = localStorage.getItem("details");
        if (localDetails != null) {
            details = localDetails === "true";
        } else {
            localStorage.setItem("details", details.toString());
        }
    }

    let overflown = false;    

    onMount(() => {
        overflowCheck();
    });

    let el: HTMLElement;

    function overflowCheck() {
        if (el != undefined) {
            overflown = el.scrollHeight > el.clientHeight || el.scrollWidth > el.clientWidth;
        } else {
            overflown = false;
        }
    }
</script>

<svelte:window on:resize={overflowCheck}/>

<template>
    <div class="overflow-x-scroll overflow-y-visible p-0 w-full" bind:this={el}>
        <table class="w-max my-0.5 mx-auto flex-shrink-0 text-center lg:m-0 border-separate border-spacing-0">
            <thead>
                <tr>
                    <th class="px-2 sticky left-0 bg-white border-r-2 border-b-2 m-">Team</th>
                    <th class="px-2 border-b-2">P</th>
                    <th class="px-2 border-b-2">G</th>
                    <th class="px-2 border-b-2" class:hidden={!details}>W</th>
                    <th class="px-2 border-b-2" class:hidden={!details}>V</th>
                    <th class="px-2 border-b-2">D</th>
                    <th class="px-2 border-b-2" class:hidden={!details}>+</th>
                    <th class="px-2 border-b-2" class:hidden={!details}>-</th>
                </tr>
            </thead>
            <tbody>
                {#each standings as team (team.id)}
                    <tr>
                        <th class="px-2 text-left sticky left-0 bg-white border-r-2">{team.name}</th>
                        <td class="px-2 font-black">{team.points}</td>
                        <td class="px-2">{team.played}</td>
                        <td class="px-2 bg-white" class:hidden={!details}>{team.won}</td>
                        <td class="px-2" class:hidden={!details}>{team.lost}</td>
                        <td class="px-2">{team.score_diff}</td>
                        <td class="px-2" class:hidden={!details}>{team.score_for}</td>
                        <td class="px-2" class:hidden={!details}>{team.score_against}</td>
                    </tr> 
                {/each}
            </tbody>
        </table>
    </div>

    <p class="text-gray-600 text-xs" class:hidden={!overflown}>Swipe tabel voor meer stats</p>

    <!-- Delay overflow check with 1ms to let the DOM change first -->
    <div class="p-4"><Toggle label="Details" bind:checked={details} on:click={() => {setTimeout(overflowCheck, 1)}}/></div> 
</template>