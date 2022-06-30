<script lang="ts">
    import sadball from "@/assets/sad-ball.svg";
</script>

<template>
    <div class="flex flex-col items-center sm:items-start sm:text-left">
        <h1 class="m-4 font-light text-5xl">Onbekende pagina</h1>

        <img class="w-32 h-32" src={sadball} alt="Sad Ball">

        <p class="m-4">Deze pagina bestaat niet. Ga terug naar <a class="text-primary underline" href="#/">Startpagina</a>.</p>
    </div>
</template>