<script lang="ts">
    import { onMount } from "svelte";
    
    import type { Event } from "@/types/event_types";

    export let title = "";
    export let events: Array<Event> = [];
    
    $: lastItem = events.length - 1;

    interface Dot {
        el: HTMLElement;
        lineLength: number;
    }

    let dots: Array<Dot> = [];

    for (let i = 0; i < events.length; i++) {
        dots.push({el: null, lineLength: 0});
    }

    onMount(() => {
        calculateLineLengths();

        // Run calculateLineLengths when window is resized
        window.addEventListener('resize', calculateLineLengths);
    });

    function calculateLineLengths() {
        // Loop through all events
        for (let i = 0; i < dots.length - 1; i++) {
            // Check if dots.el is not null
            if (dots[i].el && dots[i + 1].el) {
                // Get bounding client rect of element with dot(i) id and the next element
                const dot_el = dots[i].el;
                const next_el = dots[i + 1].el;

                const dot = dot_el.getBoundingClientRect();
                const next = next_el.getBoundingClientRect();

                // Get the y coordinate of the center of dot
                const dotY = dot.y + dot.height / 2;

                // Get the y coordinate of the center of next
                const nextY = next.y + next.height / 2;

                const length = nextY - dotY;

                // Calculate the length of the line
                dots[i].lineLength = length;
            }
        }     
    }
</script>

<template>
    <div>
        {#if title !== ""}
           <h2 class="text-1xl font-bold mb-2">{title}</h2> 
        {/if}
        <div class="flex flex-col gap-3 ml-2">
            {#each events as event, i}
                <div class="flex flex-row items-center gap-x-2">
                    <div class="text-sm w-[36px] text-center">{event.time}</div>
                    <div bind:this={dots[i].el} class="relative w-2 h-2 bg-primary rounded-full flex-shrink-0">
                        {#if i != lastItem}
                            <div class="absolute -z-10 w-0.5 top-1/2 left-1/2 -translate-x-[0.9px] border-l-2 border-dotted border-slate-300" style={"height: " + dots[i].lineLength + "px"}></div>
                        {/if}
                    </div>
                    <div>{event.name}</div>
                </div>
            {/each}
        </div>
    </div>
</template>