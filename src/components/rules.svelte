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
      class:max-h-[2000px]={!preview}
    >
      <p>
        De wedstrijden worden gespeeld volgens de regels van het
        Familietoernooi Basketbal:
      </p>
      <ul class="list-disc list-inside my-2 mx-4 space-y-2">
        <li>
          Wedstrijden duren <strong>15 minuten doorlopende tijd</strong>. Daarna
          is er <strong>5 minuten wisseltijd</strong>. Zorg dat je team op tijd
          klaarstaat.
        </li>
        <li>
          De tijd wordt centraal geregeld. De wedstrijd eindigt bij het
          eindsignaal.
        </li>
        <li>
          Fout op een doelende speler <strong>zonder</strong> score: + 2 of 3
          punten. Geen vrije worpen. Daarna balbezit voor de tegenstander.
        </li>
        <li>
          Fout op een doelende speler <strong>met</strong> score: score telt + 1
          extra punt. Geen bonusvrije worp. Daarna balbezit voor de
          tegenstander.
        </li>
        <li>
          Wisselen mag alleen bij een dood spelmoment. In de
          <strong>laatste minuut</strong> mag <strong>niet</strong> meer worden
          gewisseld.
        </li>
        <li>
          Dames mogen <strong>niet</strong> door heren geblokt worden (wel door
          dames).
        </li>
        <li>Winst = 2 punten, gelijk = 1 punt, verlies = 0 punten.</li>
        <li>
          Bij de <strong>Mini</strong> en <strong>Competitieve</strong> poules is
          de poulestand de eindstand. Bij de <strong>Recreatieve</strong> poule
          spelen de beste 4 teams finalerondes.
        </li>
        <li>
          Bij gelijke eindstand in de finalerondes schieten beide teams 1 vrije
          worp voor de winst totdat 1 van de teams wel heeft geraakt (winnaar)
          en het andere team niet. Spelers moeten rouleren voor het nemen van de
          vrije worpen.
        </li>
      </ul>

      <p class="font-bold mt-4">Bij gelijke stand in de poule:</p>
      <ol class="list-decimal list-inside my-2 mx-4 space-y-2">
        <li>Doelsaldo</li>
        <li>Meest gescoorde punten</li>
        <li>Serie van drie vrije worpen, door drie verschillende spelers</li>
        <li>Bij onduidelijkheden beslist de wedstrijdleiding.</li>
      </ol>

      <p class="font-bold mt-4">Extra regels U12 / Miniveld:</p>
      <ol class="list-decimal list-inside my-2 mx-4 space-y-2">
        <li>Schotpogingen mogen niet worden geblokt.</li>
        <li>De bal mag niet uit iemands handen worden getrokken.</li>
        <li>Alle scores tellen voor 2 punten (geen driepunters).</li>
      </ol>

      <p class="mt-4 italic">
        Fair play staat voorop: plezier, sportiviteit en respect voor elkaar
        zijn belangrijker dan winnen 😊
      </p>

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