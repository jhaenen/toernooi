<script lang="ts">
    import Router from 'svelte-spa-router';

    import Home from '@/routes/home.svelte';
    import Poules from '@/routes/poules.svelte';
    import PouleStats from '@/routes/poule_stats.svelte';
    import Schema from '@/routes/schema.svelte';
    import NotFound from '@/routes/404.svelte';

    import NavButton from '@/components/nav-button.svelte';
    import logo from "@/assets/logo.webp";

    const routes = {
        '/': Home,
        '/schema': Schema,
        '/poules': Poules,
        '/poules/:id': PouleStats,
        '*': NotFound,
    };
</script>

<main>
    <div class="fixed h-[82px] bottom-0 w-full m-0 p-0 sm:static z-10">
        <nav class="flex justify-evenly sm:justify-start bg-primary text-white py-4 sm:p3 flex-wrap">
            <a href="#/" class="hidden h-12 w-12 mx-4 sm:block"><img src={logo} alt="Svelte Logo"/></a>
            <a href="#/" class="px-4 sm:py-3 sm:px-4"><NavButton label="Home" icon="fa-house"/></a>
            <a href="#/poules" class="px-4 sm:py-3 sm:px-4"><NavButton label="Poules" icon="fa-basketball"/></a>
            <a href="#/schema" class="px-4 sm:py-3 sm:px-4"><NavButton label="Dagschema" icon="fa-newspaper"/></a>
            <a href="#/org" class="hidden mi:block px-4 sm:py-3 sm:px-4"><NavButton label="Organizatie" icon="fa-address-card"/></a>
        </nav>
    </div>
    
    <div class="p-1 absolute top-0 bottom-[78px] overflow-y-scroll w-full sm:static"><Router {routes}/></div>
</main>