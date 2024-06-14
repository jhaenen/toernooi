<script lang="ts">
  let preview = true;

  function togglePreview() {
    preview = !preview;

    const header_el = document.getElementById("rules");
    const bounding = header_el.getBoundingClientRect();

    if (
      !(
        bounding.top >= 0 &&
        bounding.left >= 0 &&
        bounding.right <= window.innerWidth &&
        bounding.bottom <= window.innerHeight
      )
    ) {
      if (header_el) {
        header_el.scrollIntoView({ behavior: "smooth" });
      } else {
        console.error("Could not find header of rules");
      }
    }
  }
</script>

<template>
  <div>
    <div
      class="overflow-hidden relative transition-[max-height] duration-300"
      class:max-h-64={preview}
      class:max-h-[1848px]={!preview}
    >
      <p>
        De wedstrijden worden gespeeld volgens de reglementen van de Nederlandse
        Basketball Bond, waarbij onderstaande afwijkende regels gelden:
      </p>
      <ol class="list-decimal list-inside my-2 mx-4">
        <li class="my-2">
          De wedstrijden duren 15 minuten doorlopende tijd. Na de wedstrijd is
          er 5 minuten tijd voor wisselen, in deze tijd kan je ook nog even met
          je team warmlopen. (zorg dat je op tijd klaar staat met je hele team)
        </li>
        <li class="my-2">
          De speeltijd is doorlopend en wordt centraal geregeld door de
          wedstrijdleiding. De wedstrijd is afgelopen na het signaal van de
          wedstrijdleiding en niet van de scheidsrechter!
        </li>
        <li class="my-2">
          Een fout op een doelende speler zonder score: geen vrije worp maar + 1
          punt en het aanvallende team houdt de bal en neemt de bal in bij de
          achterlijn.
        </li>
        <li class="my-2">
          Bij een fout op een doelende speler mét score: géén (bonus)vrije
          worpen, wel + 1 punt (dus 3 of 4 in totaal). De verdedigende partij
          neemt daarna de bal in.
        </li>
        <li class="my-2">
          Als een team 6 teamfouten heeft krijgt de tegenstander bij elk
          volgende fout + 1 punt en balbezit. (Dit geldt niet voor aanvallende
          fouten)
        </li>
        <li class="my-2">
          Wissels mogen alleen bij een dood spelmoment en moet via de
          scheidsrechter worden aangevraagd. De laatste minuut mag er niet meer
          gewisseld worden.
        </li>
        <li class="my-2">
          Winnaar krijgt 2 punten, bij gelijkspel ieder 1 punt, verliezer 0
          punten
        </li>
        <li class="my-2">
          Eindstanden in de poule: Indien 2 teams gelijk eindigen in de poule is
          de in navolgende volgorde de beslissing:
          <ol class="list-[lower-alpha] list-inside my-2 mx-4">
            <li class="my-2">
              het doelsaldo van alle wedstrijden; wanneer deze ook gelijk is…
            </li>
            <li class="my-2">
              het aantal gescoorde doelpunten; wanneer deze ook gelijk is...
            </li>
            <li class="my-2">
              een serie van 3 vrije worpen door verschillende spelers/speelsters
              uit een team.
            </li>
          </ol>
        </li>
        <li class="my-2">
          In geval van meningsverschillen of omissies beslist de
          wedstrijdleiding.
        </li>
        <li class="my-2">
          Protesten? Zijn niet nodig en waar dit wel nodig is, treedt regel 9 in
          werking!
        </li>
        <p class="my-2 -mx-4">Extra regels mini's:</p>
        <li class="my-2">
          De bal mag bij een schotpoging niet geblockt worden.
        </li>
        <li class="my-2">
          De bal mag niet bij een andere speler uit zijn handen worden
          getrokken.
        </li>
        <li class="my-2">
          Alle scores tellen voor 2 punten. (Er zijn dus geen 3 punters
          mogelijk)
        </li>
      </ol>

      <div
        class="absolute bottom-0 left-0 w-full text-center m-0 h-3/4 z-[5] transition-opacity duration-300"
        class:opacity-0={!preview}
        class:opacity-1={preview}
        style="background-image: linear-gradient(to bottom, rgba(255,255,255,0), rgba(255, 255, 255, 1));"
      ></div>
    </div>
    <div class="w-full flex justify-center mt-3">
      <button
        class="bg-primary text-white px-4 py-2 rounded-full"
        on:click={togglePreview}
      >
        {#if preview}
          Lees meer
        {:else}
          Verberg tekst
        {/if}
      </button>
    </div>
  </div>
</template>
