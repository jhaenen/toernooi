<script lang="ts">
    // Types
    import type {Event} from "@/types/event_types";

    // Components
    import EventComp from "@/components/event.svelte";

    const gameEvents: Array<Event> = [{name: "Aanmelden teams", time: "12:00"}, {name: "Start toernooi", time: "12:30"}, {name: "Wedstrijd Fortuna 1", time: "16:00"}, {name: "Einde toernooi", time: "17:45"}, {name: "Prijsuitreiking", time: "18:30"}];
    const partyEvents: Array<Event> = [{name: "Foodtruck open", time: "18:00"}, {name: "Oefenwedstrijd Heren 1 DAS - Lokomotief", time: "20:00"}, {name: "Foodtruck sluiten", time: "21:00"}, {name: "SKA/Reggae band de Limelites", time: "21:30"}, {name: "DJ duo Ben Bridges en El Commercial", time: "22:30"}, {name: "Einde feest", time: "--:--"}];
</script>

<template>
    <div class="flex flex-col items-start justify-evenly w-full h-full lg:flex-row lg:justify-start lg:gap-x-20">
        <div class="ml-2 mb-4"><EventComp events={gameEvents} title="Toernooi"/></div>
        <div class="ml-2 mt-2"><EventComp events={partyEvents} title="Foodtruck & Feest"/></div>
    </div>
</template>