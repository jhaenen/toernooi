<script lang="ts">
    // Assets
    import sadball from "@/assets/sad-ball.svg";

    export let params: {wild: string};

    if(import.meta.env.DEV) console.warn("Page not found: /" + params.wild);
</script>

<template>
    <div class="flex flex-col items-center sm:items-start sm:text-left">
        <!-- Title -->
        <h1 class="m-4 font-light text-5xl">Onbekende pagina</h1>

        <!-- Sad ball :( -->
        <img class="w-32 h-32" src={sadball} alt="Sad Ball">

        <!-- Message -->
        <p class="m-4">Deze pagina bestaat niet. Ga terug naar <a class="text-primary underline" href="#/">Startpagina</a>.</p>
    </div>
</template>