<script>
    import Rules from "@/components/rules.svelte";
    import Schedule from "@/components/schedule.svelte";

</script>
<main>
    <div class="flex flex-col items-center sm:items-start sm:text-left overflow-x-hidden">
        <!-- Title -->
        <h1 class="m-4 text-[12vw] w-full text-center leading-none font-light mi:text-5xl sm:mt-8 sm:text-left">Algemene informatie</h1>

        <!-- Main text -->
        <div class="flex flex-col m-3 gap-4">
            <!-- FAQ -->
            <h2 class="mt-5 text-[8vw] leading-none mi:text-3xl">Dagschema</h2>
            <div><Schedule/></div>

            <!-- Reglement -->
            <h2 id="rules" class="mt-5 text-[8vw] leading-none mi:text-3xl scroll-mt-2">Reglement</h2>
            <div><Rules/></div>

            <!-- Address -->
            <h2 class="mt-5 text-[8vw] leading-none mi:text-3xl">Adres</h2>
            <a href="https://goo.gl/maps/BDXNNJDC43LKQzZT8" target="_blank" class="text-primary underline"><address>Schoemakerstraat 340, 2629 HP Delft</address></a>
            {#if navigator.onLine}
                <iframe title="kaart" src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d614.1854017026016!2d4.3821292!3d51.9933631!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c5b60b00000001%3A0x95c80e12a0134cd5!2sBasketbalvereniging%20Das%20Delft!5e0!3m2!1snl!2snl!4v1661557009444!5m2!1snl!2snl" width="600" height="300" style="border:0;" allow="fullscreen" loading="lazy" referrerpolicy="no-referrer-when-downgrade" class="w-[80vw] h-80 sm:w-[600px]"></iframe>
            {/if}

            <!-- Contact -->
            <h2 class="mt-5 text-[8vw] leading-none mi:text-3xl">Contact</h2>
            <div>E-mail: <a href="mailto:toernooi.dasdelft@gmail.com?subject=Vragen%20familietoernooi%202022" class="text-primary underline">toernooi.dasdelft@gmail.com</a></div>

            <div class="h-5"></div>
            
        </div>
    </div>
</main>