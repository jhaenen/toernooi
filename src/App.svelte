<script lang="ts">
    import Router from 'svelte-spa-router';

    import Home from '@/routes/home.svelte';
    import Results from '@/routes/results.svelte';

    import NavButton from '@/view/nav-button.svelte';
    import logo from "@/assets/logo.webp";

    const routes = {
        '/': Home,
        '/results': Results,
    };
</script>

<main>
    <div class="absolute bottom-0 right-0 left-0 sm:static">
        <nav class="flex justify-evenly sm:justify-start bg-primary text-white py-4 sm:p3 flex-wrap">
            <a href="#/" class="hidden h-12 w-12 mx-4 sm:block"><img src={logo} alt="Svelte Logo"/></a>
            <a href="#/" class="px-4 sm:py-3 sm:px-4"><NavButton label="Home" icon="fa-house"/></a>
            <a href="#/" class="px-4 sm:py-3 sm:px-4"><NavButton label="Poules" icon="fa-basketball"/></a>
            <a href="#/" class="px-4 sm:py-3 sm:px-4"><NavButton label="Dagschema" icon="fa-newspaper"/></a>
            <a href="#/" class="hidden mi:block px-4 sm:py-3 sm:px-4"><NavButton label="Organizatie" icon="fa-address-card"/></a>
        </nav>
    </div>
    
    <Router {routes}/>
</main>